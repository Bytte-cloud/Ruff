// Color families are compiled to reference CSS custom properties so the client
// theme can be changed at runtime (see resources/scripts/theme/engine.ts).
// Defaults for every variable live in resources/scripts/assets/tailwind.css.
//
// Colors are defined as opacity-aware functions (the mechanism supported by the
// pinned tailwindcss 3.0.x — the `<alpha-value>` placeholder only exists in
// 3.1+). This keeps the legacy `*-opacity-*` utilities and the slash modifier
// working without touching any consuming files.
const channel = (property) => ({ opacityValue }) =>
    opacityValue === undefined ? `hsl(var(${property}))` : `hsl(var(${property}) / ${opacityValue})`;

const scale = (name) => ({
    50: channel(`--c-${name}-50`),
    100: channel(`--c-${name}-100`),
    200: channel(`--c-${name}-200`),
    300: channel(`--c-${name}-300`),
    400: channel(`--c-${name}-400`),
    500: channel(`--c-${name}-500`),
    600: channel(`--c-${name}-600`),
    700: channel(`--c-${name}-700`),
    800: channel(`--c-${name}-800`),
    900: channel(`--c-${name}-900`),
});

module.exports = {
    content: [
        './resources/scripts/**/*.{js,ts,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                header: ['"IBM Plex Sans"', '"Roboto"', 'system-ui', 'sans-serif'],
            },
            colors: {
                black: channel('--c-black'),
                white: '#ffffff',
                // "primary"/"neutral" are deprecated aliases kept for existing code.
                // primary/blue/cyan all map to the single themeable accent ramp;
                // gray/neutral are the structural ramp; red/yellow/green are status.
                primary: scale('accent'),
                blue: scale('accent'),
                cyan: scale('accent'),
                gray: scale('gray'),
                neutral: scale('gray'),
                red: scale('danger'),
                yellow: scale('warning'),
                green: scale('success'),
            },
            borderRadius: {
                none: '0px',
                sm: 'var(--radius-sm)',
                DEFAULT: 'var(--radius)',
                md: 'var(--radius-md)',
                lg: 'var(--radius-lg)',
                xl: 'var(--radius-xl)',
                full: '9999px',
            },
            fontSize: {
                '2xs': '0.625rem',
            },
            transitionDuration: {
                250: '250ms',
            },
            borderColor: theme => ({
                default: theme('colors.neutral.400', 'currentColor'),
            }),
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ]
};
