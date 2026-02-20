# Dashboard & Chargers — Implementation Notes

Summary
- Implemented a styled Dashboard (Inertia + Vue 3) using Tailwind CSS.
- Added a `Chargers` page component with Tailwind-based UI and wired the `/chargers` route to fetch data from the local CitrineOS API (`http://localhost:8080/api/chargers`) with a sample-data fallback.

What this document covers
- Files changed / created
- How the front-end styling (Tailwind) is integrated
- How the `Chargers` page gets its data and how to configure it
- How to build and verify assets
- Troubleshooting steps (styles not appearing, API unreachable)
- Next recommended steps

Files changed
- `resources/js/Pages/Dashboard.vue` — replaced with a redesigned Tailwind-based dashboard UI (stat cards, utilization, lists).
- `resources/js/Pages/Chargers.vue` — new Tailwind-styled Chargers page; normalizes `chargers` prop (Array/Object → Array).
- `routes/web.php` — `/chargers` route updated to call CitrineOS API and normalize results; contains a fallback sample array if API is unavailable.
- `tailwind.config.js` — already configured; includes `./resources/js/**/*.vue` in `content` paths and `@tailwindcss/forms` plugin.
- `postcss.config.js` — contains `tailwindcss` and `autoprefixer` plugins.
- `public/build/*` — production build artifacts after running `npm run build`.

How Tailwind is integrated
- Tailwind directives are in `resources/css/app.css` (`@tailwind base; @tailwind components; @tailwind utilities;`).
- PostCSS runs Tailwind + Autoprefixer during Vite build.
- Blade layout (`resources/views/app.blade.php`) calls `@vite('resources/js/app.js')` which injects the correct dev or production asset links depending on the environment.

Key front-end behavior
- During development (`npm run dev`), Vite serves assets and injects module script tags (dev server). Make sure the dev server host/port is reachable from the browser (IPv6 `[::1]` vs `127.0.0.1` can cause differences).
- In production (`npm run build`), assets are emitted to `public/build` and Blade injects the compiled CSS and JS via the Vite manifest.

How `Chargers` data is obtained
- Route implementation (in `routes/web.php`) attempts to fetch JSON from `http://localhost:8080/api/chargers` using Laravel `Http::get()`.
- On success the response is normalized to objects with keys: `id`, `name`, `location`, `online`.
- If the API call fails or times out, the route returns a small sample array as a fallback so the UI remains useful during development.
- The `Chargers.vue` component expects items with at least these fields; it will also accept an associative object and convert it to a list.

How to run locally (dev)
1. Start CitrineOS core (ensure API endpoint `http://localhost:8080/api/chargers` is running and accessible).
2. From `citrine-dashboard` folder:

```bash
npm install
npm run dev
# in another terminal
php artisan serve
```
3. Visit `http://127.0.0.1:8000/chargers` to see the chargers page.

How to build for production
```bash
# build assets
npm run build
# clear Laravel view/cache so Blade picks up new manifest
php artisan view:clear && php artisan cache:clear && php artisan config:clear
# serve (or deploy)
php artisan serve --port=8000
```

Troubleshooting
- Styles present on server but not applied in browser:
  - Hard-refresh (Ctrl+F5) or open an incognito window.
  - DevTools → Network: ensure `/build/assets/app-*.css` returns `200` and `Content-Type: text/css` and contains Tailwind classes (e.g. `.flex`, `.bg-white`).
  - DevTools → Console: check for CSP or JS errors and for a Service Worker serving cached assets (Application → Service Workers → unregister).
  - If using Vite dev server, check whether script/link tags use `[::1]` vs `127.0.0.1` and access the same host in the browser.

- CitrineOS API unreachable:
  - Confirm CitrineOS server is running and listening on the configured port.
  - If API requires authentication (Bearer token or API key), update the route to send the required header (we can add this if you provide the token or environment variable name).
  - If you prefer not to use the fallback sample data, remove the fallback in `routes/web.php` and return a 503 or an empty list instead.

Security notes
- Do not commit any secret tokens or API keys. If citrineos requires auth, store tokens in `.env` and reference with `env('CITRINEOS_API_KEY')`.

Next recommended steps
1. Decide on CitirneOS integration mode:
   - Add authentication headers if required.
   - Add server-side caching (e.g., `Cache::remember()` for 15–60s) to reduce API load.
2. Replace sample fallback with an explicit error state (if you prefer surface errors to developers).
3. Add live updates for charger status (polling, SSE, or WebSockets) for real-time UX.
4. Implement an internal API endpoint (`/api/chargers`) for the SPA and mobile clients that proxies/normalizes citrineos responses and manages auth/caching.
5. Add unit/integration tests for the route normalization logic.

If you'd like, I can:
- Update `routes/web.php` to add caching and auth header support (safely read token from `.env`).
- Remove fallback and surface a friendly error message.
- Create `/api/chargers` JSON endpoint for the SPA and mobile app.

---

File created: `c:\Users\Al Gazali\citrineOs\citrine-dashboard\docs\DASHBOARD_CHARGERS.md`

Tell me which follow-up you want (add caching, add auth, or create an internal API endpoint).