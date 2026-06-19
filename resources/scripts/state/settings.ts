import { action, Action } from 'easy-peasy';

export type ThemeMode = 'light' | 'dark';

export interface ThemeModeColors {
    background: string;
    surface: string;
    surface_alt: string;
    border: string;
    text: string;
    text_muted: string;
    accent: string;
    accent_hover: string;
    success: string;
    danger: string;
    warning: string;
    info: string;
    [key: string]: string;
}

export interface ThemeConfig {
    default_mode: ThemeMode;
    allow_toggle: boolean;
    share_accent_with_admin: boolean;
    geometry: {
        radius: string;
        radius_sm: string;
        border_width: string;
        [key: string]: string;
    };
    modes: {
        light: ThemeModeColors;
        dark: ThemeModeColors;
    };
}

export interface SiteSettings {
    name: string;
    locale: string;
    recaptcha: {
        enabled: boolean;
        siteKey: string;
    };
    theme?: ThemeConfig;
}

export interface SettingsStore {
    data?: SiteSettings;
    setSettings: Action<SettingsStore, SiteSettings>;
}

const settings: SettingsStore = {
    data: undefined,

    setSettings: action((state, payload) => {
        state.data = payload;
    }),
};

export default settings;
