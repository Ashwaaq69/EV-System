# CitrineOS Dashboard – Frontend to Backend Flow

This document explains how the **citrine-dashboard** project works and how requests flow from the Vue frontend to the Laravel backend.

---

## 1. How the app can run (two modes)

### Mode A: Laravel + Inertia (full stack)

- You run **Laravel** (e.g. `php artisan serve` → `http://localhost:8000`).
- Laravel serves the HTML from `resources/views/app.blade.php`, which loads the Vue app via **Inertia.js** and Vite.
- User visits `http://localhost:8000` → Laravel handles the request → Inertia renders a Vue page (e.g. `ClientPortal.vue`).
- All links and form submissions go to Laravel; Inertia swaps the page without full reload.
- **API calls** from Vue (e.g. `axios.get('/api/client/sessions')`) go to the **same origin** (`http://localhost:8000`), so Laravel receives them.

### Mode B: Standalone Vite (frontend only)

- You run **only** the Vite dev server (`npm run dev` → `http://localhost:5175`).
- `app.js` sees there is no Inertia root (`#app` has no `data-page`) and boots **StandaloneApp** with **Vue Router**.
- Routes are defined in `resources/js/router.js` (e.g. `/client/portal` → `ClientPortal.vue`).
- The same Vue pages (e.g. `ClientPortal.vue`) run, but **no Laravel page load**; it’s a SPA.
- **API calls** from Vue still use paths like `/api/client/sessions`. So that `GET` goes to `http://localhost:5175/api/client/sessions` → **Vite has no API**. To fix that, **Vite proxies** `/api` (and auth routes) to Laravel in `vite.config.js`:
  - Browser request: `http://localhost:5175/api/client/sessions`
  - Vite proxy forwards to: `http://127.0.0.1:8000/api/client/sessions`
- So you must run **both** Laravel (`php artisan serve`) and Vite (`npm run dev`) for API calls to work in standalone mode.

**Summary:**  
- **Laravel+Inertia:** One server (Laravel), same origin, no proxy.  
- **Standalone Vite:** Two servers; Vite proxies `/api` (and auth) to Laravel.

---

## 2. High-level request path (every API call)

```
[Browser]
    │
    │  User action in Vue (e.g. click "Start charging")
    │  → Vue calls e.g. axios.post('/api/client/sessions', { charge_point_id: 1 })
    │
    ▼
[URL requested]
    │  Full URL is either:
    │  • http://localhost:8000/api/client/sessions  (when using Laravel only)
    │  • http://localhost:5175/api/client/sessions (when using Vite; then proxied)
    │
    ▼
[Laravel]
    │  1. Middleware: auth, verified
    │  2. Route: POST /api/client/sessions → ClientApiController@startSession
    │  3. Controller uses Auth::id(), validates input, uses Models
    │  4. Returns JSON response
    │
    ▼
[Vue]
    │  axios receives JSON → updates local state (e.g. activeSession, currentSessionId)
    │  → UI updates (e.g. “Charging started”)
```

So for **every** feature below, the “transaction” is: **Vue (axios) → HTTP request → Laravel route → Controller → Model / DB → JSON response → Vue**.

---

## 3. Authentication flow (frontend → backend)

| Step | Where | What happens |
|------|--------|--------------|
| 1 | User opens `/login` | Laravel route `auth.php` → `AuthenticatedSessionController@create` (or Vue Router in standalone shows `Login.vue`) |
| 2 | User submits login form | Vue: `form.post('/login', { email, password })` (Inertia) or router + axios in standalone. Request goes to **Laravel** `POST /login` → `AuthenticatedSessionController@store` |
| 3 | Laravel | Validates credentials, starts session, redirects to `/home` |
| 4 | `/home` | Laravel `web.php`: if `role === 'admin'` → redirect to `dashboard`, else → redirect to `client.portal` |
| 5 | Client portal | Laravel renders Inertia `ClientPortal` (or Vue Router loads `ClientPortal.vue`). All subsequent API calls send the **same session cookie** so Laravel’s `Auth::user()` is the logged-in user. |

Auth is **session-based** (cookies). No JWT in this flow.

---

## 4. Client portal – feature-by-feature transaction flow

Below, “Frontend” = `ClientPortal.vue` (and sometimes other Vue pages). “Backend” = Laravel routes + `ClientApiController` (or other controllers) + models.

---

### 4.1 Charging sessions (list)

| Step | Frontend | Backend |
|------|----------|---------|
| 1 | On load / refresh: `axios.get('/api/client/sessions')` | `GET /api/client/sessions` → `ClientApiController@sessions` |
| 2 | — | Controller: `ChargingSession::where('user_id', Auth::id())->orderBy('start_time', 'desc')->paginate(5)` |
| 3 | — | Returns JSON: `{ data: [...], current_page, last_page, ... }` |
| 4 | Vue sets `portalSessions.value = response.data` | — |
| 5 | Table in “Recent Activity” / History uses `portalSessions.data` | — |

---

### 4.2 Start charging session

| Step | Frontend | Backend |
|------|----------|---------|
| 1 | User clicks “Start Now” on a station → `startChargingMock(station)` | — |
| 2 | `axios.post('/api/client/sessions', { charge_point_id: station.id })` | `POST /api/client/sessions` → `ClientApiController@startSession` |
| 3 | — | Validates `charge_point_id` exists in `charge_points` table |
| 4 | — | `ChargingSession::create([ user_id, charge_point_id, start_time, status: 'active', kwh_consumed: 0, total_cost: 0 ])` |
| 5 | — | Returns JSON: `{ id, user_id, charge_point_id, status: 'active', ... }` |
| 6 | Vue sets `currentSessionId = response.data.id`, `activeSession = true`, starts local “mock” kWh/cost timer | — |

---

### 4.3 Stop charging session

| Step | Frontend | Backend |
|------|----------|---------|
| 1 | User clicks “Stop Charging” → `stopCharging()` | — |
| 2 | Stops local timer, reads `chargingData.kwh` and `chargingData.cost` | — |
| 3 | `axios.post(\`/api/client/sessions/${currentSessionId}/stop\`, { kwh, cost })` | `POST /api/client/sessions/{id}/stop` → `ClientApiController@stopSession` |
| 4 | — | Loads session for `Auth::id()` and that `id`, ensures it’s active |
| 5 | — | Updates session: `status = 'completed'`, `end_time`, `kwh_consumed`, `total_cost` |
| 6 | — | Loads or creates `Wallet` for user, then `$wallet->decrement('balance', $totalCost)` (if balance ≥ cost) |
| 7 | — | Returns updated session JSON |
| 8 | Vue sets `activeSession = false`, calls `fetchSessions()` and `fetchWallet()` to refresh list and balance | — |

---

### 4.4 Wallet balance and top-up

| Step | Frontend | Backend |
|------|----------|---------|
| 1 | On load: `axios.get('/api/client/wallet')` | `GET /api/client/wallet` → `ClientApiController@wallet` |
| 2 | — | `Wallet::firstOrCreate(['user_id' => Auth::id()], ['balance' => 0, 'currency' => 'RM'])` |
| 3 | — | Returns `{ balance, currency }` |
| 4 | Vue sets `wallet.value = response.data` | — |
| 5 | User clicks “Top up” with amount → `axios.post('/api/client/wallet/top-up', { amount })` | `POST /api/client/wallet/top-up` → `ClientApiController@topUpWallet` |
| 6 | — | Validates amount, same `firstOrCreate`, then `$wallet->increment('balance', $request->amount)` |
| 7 | — | Returns `{ balance, message }` |
| 8 | Vue sets `wallet.value.balance = response.data.balance` | — |

---

### 4.5 Vehicles (EV profiles)

| Action | Frontend | Backend |
|--------|----------|---------|
| **List** | `axios.get('/api/client/vehicles')` on load | `GET /api/client/vehicles` → `vehicles()` → `EvVehicle::where('user_id', Auth::id())->get()` → JSON |
| **Add** | `axios.post('/api/client/vehicles', { brand, model, plate_number, ... })` | `POST /api/client/vehicles` → `storeVehicle()` → validate → `EvVehicle::create([...])` → JSON |
| **Set default** | `axios.post(\`/api/client/vehicles/${id}/default\`)` | `POST /api/client/vehicles/{id}/default` → `setDefaultVehicle()` → clear other defaults, set this one → JSON |
| **Delete** | `axios.delete(\`/api/client/vehicles/${id}\`)` | `DELETE /api/client/vehicles/{id}` → `destroyVehicle()` → find, `$vehicle->delete()` → JSON |

---

### 4.6 Invoice download

| Step | Frontend | Backend |
|------|----------|---------|
| 1 | User clicks download on a session → `downloadInvoice(session)` | — |
| 2 | `axios.get(\`/api/client/sessions/${id}/invoice\`, { responseType: 'blob' })` | `GET /api/client/sessions/{id}/invoice` → `downloadInvoice($id)` |
| 3 | — | Loads session for `Auth::id()`, builds invoice data array, `response()->streamDownload(...)` with JSON filename |
| 4 | Vue creates `<a>` with blob URL and `.click()` to save file | — |

---

### 4.7 Reservation (booking slot)

| Step | Frontend | Backend |
|------|----------|---------|
| 1 | User clicks “Reserve” on map, picks duration → `confirmReservation()` | — |
| 2 | `axios.post('/api/client/reservations', { station_id, slot_minutes })` | `POST /api/client/reservations` → `createReservation()` |
| 3 | — | Validates, returns `{ reserved: true, expires_at, message }` (no DB persistence in current implementation) |
| 4 | Vue shows toast with message | — |

---

## 5. Admin-side flows (dashboard, transactions)

- **Dashboard:** Laravel route `GET /dashboard` → `DashboardController@index` → Inertia render with chargers data. Adding a charger: `POST /dashboard` → `DashboardController@store`.
- **Transactions:** `GET /transactions` → `TransactionController@index` → Inertia with sessions. Stopping a session: `POST /transactions/{id}/stop` → `TransactionController@stopSession`.

These use **Inertia** (full page load from Laravel), not axios to `/api/client/*`.

---

## 6. Data flow diagram (simplified)

```
┌─────────────────────────────────────────────────────────────────────────┐
│  BROWSER                                                                 │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │  Vue SPA (ClientPortal.vue, etc.)                                │   │
│  │  • User clicks "Start charging" / "Top up" / "Add vehicle" etc.   │   │
│  │  • axios.get/post/delete('/api/client/...')                       │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │  HTTP (same origin or via Vite proxy)
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  LARAVEL (citrine-dashboard)                                             │
│  ┌─────────────┐    ┌──────────────────────┐    ┌────────────────────┐  │
│  │  Middleware │ -> │  Route (web.php)     │ -> │  ClientApiController│  │
│  │  auth,      │    │  /api/client/sessions│    │  sessions(),       │  │
│  │  verified   │    │  /api/client/wallet  │    │  startSession(),   │  │
│  │             │    │  /api/client/vehicles│    │  stopSession(),    │  │
│  │             │    │  ...                 │    │  wallet(), ...    │  │
│  └─────────────┘    └──────────────────────┘    └────────────────────┘  │
│                                                              │            │
│                                                              ▼            │
│  ┌────────────────────────────────────────────────────────────────────┐  │
│  │  Models: ChargingSession, EvVehicle, Wallet, ChargePoint, User     │  │
│  │  -> Database (MySQL/PostgreSQL/SQLite): charging_sessions,          │  │
│  │     ev_vehicles, wallets, charge_points, users, ...                 │  │
│  └────────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │  JSON response
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  VUE again                                                               │
│  • axios response → update refs (e.g. portalSessions, wallet, vehicles)  │
│  • UI re-renders (table, balance, map, etc.)                            │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 7. Quick reference: URL → Controller method

| HTTP + URL | Controller method | Purpose |
|------------|-------------------|---------|
| GET  /api/client/sessions | `ClientApiController@sessions` | List my charging sessions (paginated) |
| POST /api/client/sessions | `ClientApiController@startSession` | Start a charging session |
| POST /api/client/sessions/{id}/stop | `ClientApiController@stopSession` | Stop session, update kwh/cost, deduct wallet |
| GET  /api/client/sessions/{id}/invoice | `ClientApiController@downloadInvoice` | Download invoice (JSON file) |
| GET  /api/client/wallet | `ClientApiController@wallet` | Get wallet balance |
| POST /api/client/wallet/top-up | `ClientApiController@topUpWallet` | Top up wallet (no real payment) |
| GET  /api/client/vehicles | `ClientApiController@vehicles` | List my EV vehicles |
| POST /api/client/vehicles | `ClientApiController@storeVehicle` | Add vehicle |
| POST /api/client/vehicles/{id}/default | `ClientApiController@setDefaultVehicle` | Set default vehicle |
| DELETE /api/client/vehicles/{id} | `ClientApiController@destroyVehicle` | Delete vehicle |
| POST /api/client/reservations | `ClientApiController@createReservation` | Reserve slot (returns message) |

All of these are under `Route::middleware(['auth', 'verified'])` in `web.php`, so the user must be logged in and verified; the backend uses `Auth::id()` / `Auth::user()` for all operations.

---

## 8. Files to look at (in order)

1. **Routes:** `routes/web.php` (main), `routes/auth.php` (login/register).
2. **Client API:** `app/Http/Controllers/ClientApiController.php` (every client API method).
3. **Models:** `app/Models/ChargingSession.php`, `Wallet.php`, `EvVehicle.php`, `User.php`, `ChargePoint.php`.
4. **Frontend entry:** `resources/js/app.js` (Inertia vs Standalone, axios not configured here; relative URLs).
5. **Client UI and API calls:** `resources/js/Pages/ClientPortal.vue` (all `axios.get/post/delete` to `/api/client/...`).
6. **Proxy (standalone):** `vite.config.js` → `server.proxy` for `/api` and auth routes to Laravel.

This is the full transaction path from frontend to backend for the citrine-dashboard project.
