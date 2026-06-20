# Frontend build migration: webpack 4 → 5

## Why

Most remaining frontend Dependabot advisories live in the **webpack 4 build chain**, not in
runtime code. They can't be patched piecemeal because the patched versions need either a newer
webpack or modern ESM/Node that webpack 4 can't consume. Concretely, webpack 4 currently blocks:

- `uuid` ≥ 9 (ships untranspiled ESM → `Module parse failed` under webpack 4)
- `i18next-http-backend` ≥ 2 (renamed exports; also pulls modern syntax)
- `webpack-dev-server` 3 → its vulnerable subtree (`node-forge`, `ip`, `http-proxy-middleware` 0.19, `webpack-dev-middleware` 3)
- `ejs` (via `webpack-bundle-analyzer` old), `tar`, `dset`, the `loader-utils`/`serialize-javascript` churn

Moving to webpack 5 deletes most of that subtree at the source and unblocks the held runtime bumps.

Prereq already done: build runs on **Node 20** (`node:20-alpine`, CI node 20).

## Scope of change (this repo's `webpack.config.js`)

### 1. Dependencies
Bump (peer-compatible with webpack 5):
- `webpack` 4 → **5**, `webpack-cli` 3 → **5**, `webpack-dev-server` 3 → **5**
- `css-loader` 5 → **6+**, `style-loader` 2 → **3+**, `postcss-loader` 4 → **7+**
- `terser-webpack-plugin` 4 → **5**, `webpack-assets-manifest` 3 → **5**
- `webpack-bundle-analyzer` 3 → **4**
- `fork-ts-checker-webpack-plugin` 6 → **8** (6 works too, but 8 is the webpack-5 line)

Remove (replaced by webpack 5 built-in **asset modules**):
- `file-loader`, `svg-url-loader`  → use `type: 'asset/resource'` / `asset/inline`
- `source-map-loader` stays (still valid) but the `enforce: 'pre'` JS rule can be dropped if not needed

Once on webpack 5, the previously-held runtime bumps become possible:
- `uuid` 8 → **11** (and drop `@types/uuid`)
- `i18next-http-backend` 1 → **3** (update `i18n.ts`: `BackendOptions` → `HttpBackendOptions`; smoke-test the
  `i18next-multiload-backend-adapter` wrapper — it is unmaintained and may need replacing)

### 2. `webpack.config.js` changes
- **Asset modules** (replaces file-loader/svg-url-loader):
  ```js
  { test: /\.(png|jpe?g|gif)$/, type: 'asset/resource', generator: { filename: 'images/[name].[hash:8][ext]' } },
  { test: /\.svg$/, type: 'asset/inline' },
  ```
- **Hashing tokens**: `[hash]` → `[fullhash]` in `output.filename`/`chunkFilename` (webpack 5 rename).
- **TerserPlugin**: remove `cache: isProduction` (caching is automatic via `cache`); keep `terserOptions`.
- **`cache: true`** → switch to `cache: { type: 'filesystem' }` for the big build-time win.
- **Drop** `optimization.removeEmptyChunks` (removed; automatic).
- **devServer** (v3 → v4/5 config rename — biggest change):
  - `contentBase` → `static: { directory: path.join(__dirname, 'public') }`
  - `publicPath` → `devMiddleware: { publicPath: ... }`
  - `allowedHosts: ['.Ruff.test']` → `allowedHosts: ['.ruff.test']` (array still ok)
  - `https`/`host`/`port`/`--hot` move into `devServer`/CLI flags; `--public` flag is gone (use `client.webSocketURL`)
  - update the `serve` script accordingly
- **Node core polyfills are NOT automatic in webpack 5.** This repo's tree pulls the crypto-polyfill
  chain (`browserify-sign`, `pbkdf2`, `sha.js`, `cipher-base`, `elliptic`, `bn.js`) and `events`. Audit
  what imports node builtins in the browser bundle; for anything required, add explicit
  `resolve.fallback` (e.g. `crypto: require.resolve('crypto-browserify')`, `stream`, `buffer`, `events`)
  or, preferably, remove the dependency that needs them. Expect a few `Can't resolve 'crypto'`-style
  errors on first build — resolve each deliberately.
- **ESM resolution**: if a modern dep errors with "fully specified", add
  `{ test: /\.m?js$/, resolve: { fullySpecified: false } }`. (The `.mjs` `javascript/auto` rule can go.)

### 3. react-hot-loader → React Fast Refresh (recommended, optional)
`react-hot-loader` + the `react-dom: npm:@hot-loader/react-dom` alias are deprecated and awkward on
webpack 5. Replace with React Fast Refresh:
- add `@pmmmwh/react-refresh-webpack-plugin` + `react-refresh` (dev only)
- remove `react-hot-loader`, the `react-hot-loader/patch` entry, and the `@hot-loader/react-dom` alias
  (point `react-dom` back at the real package)
- this also clears the `react-hot-loader`/`@hot-loader/react-dom` (React 16-pinned) oddity

## Suggested sequencing (small, verifiable PRs)
1. **Core webpack 5**: bump webpack/webpack-cli + loaders, asset modules, hashing, terser/cache,
   polyfill fallbacks. Goal: `yarn build:production` green. (Biggest advisory drop.)
2. **dev-server 5**: rewrite the `devServer` block + `serve` script. (Removes node-forge/ip/http-proxy-middleware subtree.)
3. **Unblock runtime holds**: uuid 11, i18next-http-backend 3 (+ adapter smoke-test).
4. **(optional) Fast Refresh**: drop react-hot-loader.

## Verification at each step
- `yarn install --frozen-lockfile` exit 0 (node 20)
- `yarn build:production` emits assets, **0** compile errors (locally:
  `NODE_OPTIONS=--openssl-legacy-provider` is NOT needed on webpack 5 — drop it from the scripts once migrated)
- `yarn audit` delta
- App smoke test via `reinstall.sh` (translations load, file/SVG assets render, console/file-manager work,
  HMR in `yarn serve`)

## Notes
- Dropping `--openssl-legacy-provider`: webpack 5 uses a supported hash, so once migrated, remove the
  flag from the `build`/`build:production`/`watch`/`serve` scripts in `package.json`.
- `React 16 → 18` is a separate, larger migration (concurrent features, `createRoot`, `react-router` 5→6
  optional) and is **not** required for webpack 5.
