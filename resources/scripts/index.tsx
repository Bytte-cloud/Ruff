import React from 'react';
import ReactDOM from 'react-dom';
import App from '@/components/App';
import { setConfig } from 'react-hot-loader';
import { initTheme } from '@/theme';

// Enable language support.
import './i18n';

// Apply the configured client theme (CSS variables) before the first render so
// the UI paints in the correct colors immediately.
initTheme();

// Prevents page reloads while making component changes which
// also avoids triggering constant loading indicators all over
// the place in development.
//
// @see https://github.com/gaearon/react-hot-loader#hook-support
setConfig({ reloadHooks: false });

ReactDOM.render(<App />, document.getElementById('app'));
