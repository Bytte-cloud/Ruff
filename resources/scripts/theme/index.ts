import { useEffect, useState } from 'react';
import { ThemeConfig, ThemeMode } from '@/state/settings';
import { applyTheme } from '@/theme/engine';

/**
 * Lightweight (non-React) theme-mode store so the theme can be applied before
 * React mounts (avoiding a flash) while still letting components subscribe to
 * mode changes via the useThemeMode() hook.
 */

const STORAGE_KEY = 'ruff:theme-mode';
const listeners = new Set<() => void>();
let current: ThemeMode = 'dark';

export function getThemeConfig(): ThemeConfig | undefined {
    return (window as unknown as { SiteConfiguration?: { theme?: ThemeConfig } }).SiteConfiguration?.theme;
}

export function getMode(): ThemeMode {
    return current;
}

function readSavedMode(): ThemeMode | null {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        return value === 'light' || value === 'dark' ? value : null;
    } catch (e) {
        return null;
    }
}

/**
 * Resolve the starting mode (saved preference when toggling is allowed,
 * otherwise the admin default) and apply it. Call once, before React renders.
 */
export function initTheme(): void {
    const config = getThemeConfig();
    const defaultMode: ThemeMode = config?.default_mode === 'light' ? 'light' : 'dark';
    const saved = readSavedMode();

    current = config?.allow_toggle && saved ? saved : defaultMode;

    if (config) {
        applyTheme(config, current);
    }
}

export function setMode(mode: ThemeMode): void {
    current = mode;
    try {
        localStorage.setItem(STORAGE_KEY, mode);
    } catch (e) {
        /* ignore storage failures (private mode, etc.) */
    }

    const config = getThemeConfig();
    if (config) {
        applyTheme(config, mode);
    }

    listeners.forEach((fn) => fn());
}

export function toggleMode(): void {
    setMode(current === 'dark' ? 'light' : 'dark');
}

/**
 * Subscribe a React component to the current theme mode.
 */
export function useThemeMode(): { mode: ThemeMode; toggle: () => void; allowToggle: boolean } {
    const [, force] = useState(0);

    useEffect(() => {
        const fn = (): void => force((x) => x + 1);
        listeners.add(fn);
        return () => {
            listeners.delete(fn);
        };
    }, []);

    return { mode: current, toggle: toggleMode, allowToggle: !!getThemeConfig()?.allow_toggle };
}
