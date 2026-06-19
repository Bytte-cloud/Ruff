import { ThemeConfig, ThemeMode } from '@/state/settings';

/**
 * Runtime theme engine.
 *
 * Tailwind's color palette is compiled to reference CSS custom properties
 * (e.g. `bg-neutral-800` -> `hsl(var(--c-gray-800) / <opacity>)`). This
 * module turns the small set of semantic theme tokens saved in the admin panel
 * into the full set of `--c-*` variables and writes them onto :root, so the
 * whole client app re-themes at runtime without touching components.
 */

interface RGB {
    r: number;
    g: number;
    b: number;
}

const WHITE: RGB = { r: 255, g: 255, b: 255 };
const BLACK: RGB = { r: 0, g: 0, b: 0 };

const clamp = (n: number, min = 0, max = 255): number => Math.min(max, Math.max(min, n));

function parseColor(input: string): RGB {
    const s = (input || '').trim().toLowerCase();

    if (s.startsWith('#')) {
        let hex = s.slice(1);
        if (hex.length === 3) {
            hex = hex
                .split('')
                .map((c) => c + c)
                .join('');
        }
        if (hex.length === 6) {
            return {
                r: parseInt(hex.slice(0, 2), 16),
                g: parseInt(hex.slice(2, 4), 16),
                b: parseInt(hex.slice(4, 6), 16),
            };
        }
    }

    const rgbMatch = s.match(/rgba?\(([^)]+)\)/);
    if (rgbMatch) {
        const p = rgbMatch[1]
            .split(/[\s,/]+/)
            .filter(Boolean)
            .map((x) => parseFloat(x));
        return { r: clamp(p[0] || 0), g: clamp(p[1] || 0), b: clamp(p[2] || 0) };
    }

    const hslMatch = s.match(/hsla?\(([^)]+)\)/);
    if (hslMatch) {
        const p = hslMatch[1]
            .split(/[\s,/]+/)
            .filter(Boolean)
            .map((x) => parseFloat(x));
        return hslToRgb(p[0] || 0, p[1] || 0, p[2] || 0);
    }

    return { r: 0, g: 0, b: 0 };
}

function hslToRgb(h: number, s: number, l: number): RGB {
    h = ((h % 360) + 360) % 360;
    s = clamp(s, 0, 100) / 100;
    l = clamp(l, 0, 100) / 100;

    const c = (1 - Math.abs(2 * l - 1)) * s;
    const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
    const m = l - c / 2;
    let r = 0;
    let g = 0;
    let b = 0;

    if (h < 60) {
        r = c;
        g = x;
    } else if (h < 120) {
        r = x;
        g = c;
    } else if (h < 180) {
        g = c;
        b = x;
    } else if (h < 240) {
        g = x;
        b = c;
    } else if (h < 300) {
        r = x;
        b = c;
    } else {
        r = c;
        b = x;
    }

    return { r: Math.round((r + m) * 255), g: Math.round((g + m) * 255), b: Math.round((b + m) * 255) };
}

/** Returns an "H S% L%" string consumed as hsl(var(--x)) by the opacity-aware colors. */
function toHslChannels({ r, g, b }: RGB): string {
    const rn = r / 255;
    const gn = g / 255;
    const bn = b / 255;
    const max = Math.max(rn, gn, bn);
    const min = Math.min(rn, gn, bn);
    const d = max - min;
    const l = (max + min) / 2;
    let h = 0;
    let s = 0;

    if (d !== 0) {
        s = d / (1 - Math.abs(2 * l - 1));
        switch (max) {
            case rn:
                h = ((((gn - bn) / d) % 6) + 6) % 6;
                break;
            case gn:
                h = (bn - rn) / d + 2;
                break;
            default:
                h = (rn - gn) / d + 4;
                break;
        }
        h *= 60;
    }

    return `${Math.round(h)} ${Math.round(s * 100)}% ${Math.round(l * 100)}%`;
}

const mix = (a: RGB, b: RGB, t: number): RGB => ({
    r: Math.round(a.r + (b.r - a.r) * t),
    g: Math.round(a.g + (b.g - a.g) * t),
    b: Math.round(a.b + (b.b - a.b) * t),
});

const luminance = ({ r, g, b }: RGB): number => 0.2126 * r + 0.7152 * g + 0.0722 * b;

/**
 * Build the full `--c-*` (and geometry) variable map for a given mode.
 */
export function buildThemeVariables(theme: ThemeConfig, mode: ThemeMode): Record<string, string> {
    const m = theme.modes[mode] || theme.modes.dark;
    const c = (token: string, fallback = '#000000'): RGB => parseColor(m[token] || fallback);

    const bg = c('background');
    const surface = c('surface');
    const surfaceAlt = c('surface_alt');
    const border = c('border');
    const text = c('text');
    const muted = c('text_muted');
    const accent = c('accent');
    const accentHover = c('accent_hover', m.accent);
    const success = c('success');
    const danger = c('danger');
    const warning = c('warning');

    const vars: Record<string, string> = {};
    const set = (key: string, color: RGB): void => {
        vars[key] = toHslChannels(color);
    };

    // Foreground extreme — pushes the brightest text shade the correct way in
    // both light and dark modes.
    const fgExtreme = luminance(text) > luminance(bg) ? WHITE : BLACK;

    // Structural ramp (gray / neutral), assigned by role rather than brightness
    // so it inverts correctly between light and dark.
    set('--c-gray-900', mix(bg, BLACK, 0.28));
    set('--c-gray-800', bg);
    set('--c-gray-700', surface);
    set('--c-gray-600', surfaceAlt);
    set('--c-gray-500', border);
    set('--c-gray-400', muted);
    set('--c-gray-300', mix(muted, text, 0.4));
    set('--c-gray-200', mix(muted, text, 0.72));
    set('--c-gray-100', text);
    set('--c-gray-50', mix(text, fgExtreme, 0.35));
    set('--c-black', mix(bg, BLACK, 0.45));

    // Semantic ramps. 500 = the chosen token, 600 = its hover/darker variant.
    const ramp = (prefix: string, base: RGB, hover: RGB): void => {
        set(`${prefix}-50`, mix(base, WHITE, 0.9));
        set(`${prefix}-100`, mix(base, WHITE, 0.8));
        set(`${prefix}-200`, mix(base, WHITE, 0.6));
        set(`${prefix}-300`, mix(base, WHITE, 0.4));
        set(`${prefix}-400`, mix(base, WHITE, 0.18));
        set(`${prefix}-500`, base);
        set(`${prefix}-600`, hover);
        set(`${prefix}-700`, mix(base, BLACK, 0.26));
        set(`${prefix}-800`, mix(base, BLACK, 0.4));
        set(`${prefix}-900`, mix(base, BLACK, 0.52));
    };

    ramp('--c-accent', accent, accentHover);
    ramp('--c-danger', danger, mix(danger, BLACK, 0.12));
    ramp('--c-warning', warning, mix(warning, BLACK, 0.12));
    ramp('--c-success', success, mix(success, BLACK, 0.12));

    // Geometry tokens drive the themeable border-radius scale.
    const geo = theme.geometry || ({} as ThemeConfig['geometry']);
    if (geo.radius_sm) {
        vars['--radius-sm'] = geo.radius_sm;
        vars['--radius'] = geo.radius_sm;
    }
    if (geo.radius) {
        vars['--radius-md'] = geo.radius;
        vars['--radius-lg'] = geo.radius;
        vars['--radius-xl'] = geo.radius;
    }

    return vars;
}

/**
 * Apply a theme + mode to the document root.
 */
export function applyTheme(theme: ThemeConfig, mode: ThemeMode): void {
    if (typeof document === 'undefined') {
        return;
    }

    const vars = buildThemeVariables(theme, mode);
    const root = document.documentElement;

    Object.keys(vars).forEach((key) => root.style.setProperty(key, vars[key]));
    root.dataset.themeMode = mode;
    // Hint native UI (form controls, scrollbars) to match.
    root.style.setProperty('color-scheme', mode);
}

/**
 * Resolve a `--c-*` variable to a concrete `hsl()` string. Use for contexts
 * that can't consume CSS variables directly (e.g. Chart.js canvas colors).
 */
export function themeColor(variable: string, alpha?: number): string {
    let channels = '0 0% 50%';
    if (typeof document !== 'undefined') {
        const resolved = getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
        if (resolved) {
            channels = resolved;
        }
    }
    return alpha === undefined ? `hsl(${channels})` : `hsl(${channels} / ${alpha})`;
}
