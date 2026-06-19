<?php

/*
|--------------------------------------------------------------------------
| Client (user-facing) theme defaults
|--------------------------------------------------------------------------
|
| These are the default design tokens used by the user-facing dashboard.
| They are overridden by anything saved in the admin Theme tab (stored as
| JSON under the `settings::theme` key) and exposed to the frontend through
| AssetComposer -> window.SiteConfiguration.theme.
|
| Every value here becomes a CSS custom property on the client (e.g.
| `accent` -> `--theme-accent`), so the React app can be themed at runtime.
|
*/

return [
    // 'light' | 'dark' — which palette loads first.
    'default_mode' => 'dark',

    // Allow the end user to flip between light/dark with a toggle.
    'allow_toggle' => true,

    // When true, the admin panel borrows the client's accent color.
    'share_accent_with_admin' => false,

    // Shape / geometry tokens (shared across both modes).
    'geometry' => [
        'radius' => '12px',
        'radius_sm' => '8px',
        'border_width' => '1px',
    ],

    // Per-mode color tokens. Add keys here and they automatically appear in
    // the admin editor and as CSS variables on the client.
    'modes' => [
        'light' => [
            'background' => '#f6f6f4',
            'surface' => '#ffffff',
            'surface_alt' => '#fafaf9',
            'border' => '#ececea',
            'text' => '#1b1b19',
            'text_muted' => '#8d8c83',
            'accent' => '#f97316',
            'accent_hover' => '#ea580c',
            'success' => '#16a34a',
            'danger' => '#e1483a',
            'warning' => '#e08a1e',
            'info' => '#3b82f6',
        ],
        'dark' => [
            'background' => '#1f2933',
            'surface' => '#283543',
            'surface_alt' => '#222d38',
            'border' => '#3b4754',
            'text' => '#e4e9ef',
            'text_muted' => '#9aa5b1',
            'accent' => '#f97316',
            'accent_hover' => '#fb8c3f',
            'success' => '#22c55e',
            'danger' => '#f0584a',
            'warning' => '#eab308',
            'info' => '#60a5fa',
        ],
    ],

    // Friendly labels for the editor (key => label).
    'labels' => [
        'background' => 'Page background',
        'surface' => 'Surface / cards',
        'surface_alt' => 'Surface (alt)',
        'border' => 'Borders',
        'text' => 'Text',
        'text_muted' => 'Muted text',
        'accent' => 'Accent',
        'accent_hover' => 'Accent (hover)',
        'success' => 'Success',
        'danger' => 'Danger',
        'warning' => 'Warning',
        'info' => 'Info',
    ],

    // One-click starting points. Each preset is a full theme (minus labels).
    'presets' => [
        'sunset' => [
            'name' => 'Sunset (default)',
            'default_mode' => 'dark',
            'geometry' => ['radius' => '12px', 'radius_sm' => '8px', 'border_width' => '1px'],
            'modes' => [
                'light' => [
                    'background' => '#f6f6f4', 'surface' => '#ffffff', 'surface_alt' => '#fafaf9',
                    'border' => '#ececea', 'text' => '#1b1b19', 'text_muted' => '#8d8c83',
                    'accent' => '#f97316', 'accent_hover' => '#ea580c',
                    'success' => '#16a34a', 'danger' => '#e1483a', 'warning' => '#e08a1e', 'info' => '#3b82f6',
                ],
                'dark' => [
                    'background' => '#1f2933', 'surface' => '#283543', 'surface_alt' => '#222d38',
                    'border' => '#3b4754', 'text' => '#e4e9ef', 'text_muted' => '#9aa5b1',
                    'accent' => '#f97316', 'accent_hover' => '#fb8c3f',
                    'success' => '#22c55e', 'danger' => '#f0584a', 'warning' => '#eab308', 'info' => '#60a5fa',
                ],
            ],
        ],
        'midnight' => [
            'name' => 'Midnight (indigo)',
            'default_mode' => 'dark',
            'geometry' => ['radius' => '14px', 'radius_sm' => '9px', 'border_width' => '1px'],
            'modes' => [
                'light' => [
                    'background' => '#f5f6fb', 'surface' => '#ffffff', 'surface_alt' => '#f3f4fb',
                    'border' => '#e6e8f4', 'text' => '#1a1c2b', 'text_muted' => '#8a8fb0',
                    'accent' => '#6366f1', 'accent_hover' => '#4f46e5',
                    'success' => '#16a34a', 'danger' => '#e1483a', 'warning' => '#e08a1e', 'info' => '#3b82f6',
                ],
                'dark' => [
                    'background' => '#15161f', 'surface' => '#1e2030', 'surface_alt' => '#191b28',
                    'border' => '#2d2f44', 'text' => '#e6e8f4', 'text_muted' => '#9498bd',
                    'accent' => '#818cf8', 'accent_hover' => '#a5b4fc',
                    'success' => '#22c55e', 'danger' => '#f0584a', 'warning' => '#eab308', 'info' => '#60a5fa',
                ],
            ],
        ],
        'forest' => [
            'name' => 'Forest (emerald)',
            'default_mode' => 'dark',
            'geometry' => ['radius' => '10px', 'radius_sm' => '7px', 'border_width' => '1px'],
            'modes' => [
                'light' => [
                    'background' => '#f3f7f4', 'surface' => '#ffffff', 'surface_alt' => '#eef4ef',
                    'border' => '#e0e9e2', 'text' => '#16201a', 'text_muted' => '#7e8c83',
                    'accent' => '#10b981', 'accent_hover' => '#059669',
                    'success' => '#16a34a', 'danger' => '#e1483a', 'warning' => '#e08a1e', 'info' => '#3b82f6',
                ],
                'dark' => [
                    'background' => '#10201a', 'surface' => '#16291f', 'surface_alt' => '#13241c',
                    'border' => '#24402f', 'text' => '#e2efe7', 'text_muted' => '#8eac9a',
                    'accent' => '#34d399', 'accent_hover' => '#6ee7b7',
                    'success' => '#22c55e', 'danger' => '#f0584a', 'warning' => '#eab308', 'info' => '#60a5fa',
                ],
            ],
        ],
    ],
];
