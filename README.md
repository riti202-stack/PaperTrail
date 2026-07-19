
# PaperTrail

**A Document/Certificate Courier Tracking System**

PaperTrail is a role-based web application that lets students and staff request official documents (transcripts, certificates, admit cards) to be physically couriered from an issuing office to their address — with live GPS tracking, in-app chat, and a Daraz-style delivery status pipeline, similar to how modern courier services (Pathao Courier, Sundarban Courier) operate.

Built as a semester project for **CSE 3100 — Web Programming Laboratory**, KUET.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [User Roles](#user-roles)
- [Database Schema](#database-schema)
- [Delivery Status Pipeline](#delivery-status-pipeline)
- [Project Structure](#project-structure)
- [Setup Instructions](#setup-instructions)
- [Environment Configuration](#environment-configuration)
- [Testing Guide](#testing-guide)
- [Known Limitations](#known-limitations)
- [Future Enhancements](#future-enhancements)

---

## Features

- **Role-based authentication** (Admin, Requester, Runner) via Laravel Breeze
- **Document request submission** with map-pin delivery location selection (Leaflet.js)
- **Admin approval workflow** — approve, reject, and assign requests to runners
- **Workload-aware runner assignment page** — shows every runner's pending/ongoing/completed task counts and zone coverage, so admins can make informed assignment decisions
- **Zone-based coverage system** — a genuine many-to-many relationship between runners and delivery zones
- **Runner self-registration** with admin approval gate before dashboard access is granted
- **Task notification system** — runners see new assignments with an accept/decline flow; declined tasks return to the admin for reassignment
- **Live GPS tracking** — runners share live location via the browser's Geolocation API; requesters see a live-updating map with the runner's position
- **ETA calculation** — Haversine-formula distance estimate with a rough time-to-arrival
- **Daraz-style status stepper** — visual step-by-step progress (Requested → Approved → Assigned → Accepted → Picked up → In transit → Delivered)
- **In-app chat** — real-time-feeling messaging between requester and assigned runner, scoped per request
- **Archive/history system** — completed and rejected requests can be moved out of the active queue into a dedicated history page
- **Fully async JavaScript** — all live features (status, location, chat) use `fetch()` + `async/await` polling, no page reloads required

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel 12 (PHP 8.2) |
| Authentication | Laravel Breeze (Blade stack) |
| API authentication | Laravel Sanctum (stateful, cookie-based) |
| Database | MySQL (via XAMPP) |
| Frontend | Blade templates + Tailwind CSS |
| Maps | Leaflet.js + OpenStreetMap (free, no API key required) |
| Live updates | Vanilla JavaScript, `fetch()` + `async/await` polling |
| Build tooling | Vite |
| Fonts | Fraunces (display), IBM Plex Sans (body), IBM Plex Mono (tracking IDs) |

---

## User Roles

### Admin
- Approves or rejects incoming document requests
- Views a dedicated assignment page listing every runner with workload stats (pending / ongoing / completed) and zone-coverage indicators
- Approves pending runner self-registrations
- Creates runner accounts directly, with multi-zone coverage selection
- Archives completed/rejected requests to the history page

### Requester
- Submits a document request: document type, pickup location, delivery address, and an exact delivery pin dropped on a map
- Tracks request progress via a visual status stepper
- Views the assigned runner's live location on a map once dispatched
- Chats with the assigned runner directly on the tracking page

### Runner
- Applies via public self-registration (pending admin approval) or is created directly by an admin
- Sees new task notifications with Accept/Decline actions
- Shares live location via the browser's Geolocation API while delivering
- Advances delivery status step-by-step (collected → in transit → delivered)
- Chats with the requester during delivery

---

## Database Schema

### Core tables

| Table | Purpose |
|---|---|
| `users` | Base auth table (Breeze), extended with a `role` enum: `admin`, `requester`, `runner` |
| `runners` | Runner profile: phone, vehicle, availability, live coordinates, approval status |
| `zones` | Delivery coverage areas (e.g. Boyra, Khulna Sadar) |
| `runner_zone` | Pivot table — many-to-many between `runners` and `zones` |
| `document_requests` | The central entity: one row per courier request, including delivery coordinates and current status |
| `document_status_history` | Append-only log of every status change per request (audit trail) |
| `chat_messages` | Messages scoped to a specific `document_request`, sent by either party |

### Relationship types

| Relationship | Type |
|---|---|
| `users` ↔ `runners` | One-to-one |
| `users` → `document_requests` | One-to-many (as requester) |
| `runners` → `document_requests` | One-to-many (as assigned runner) |
| `zones` → `document_requests` | One-to-many |
| `document_requests` → `document_status_history` | One-to-many |
| `document_requests` → `chat_messages` | One-to-many |
| `users` → `chat_messages` | One-to-many (as sender) |
| **`runners` ↔ `zones`** | **Many-to-many** (via `runner_zone`) |

The `runners ↔ zones` many-to-many is the schema's centerpiece: a runner can cover multiple delivery zones, and each zone can be served by multiple runners. This powers the admin assignment page's "Covers zone" indicator and (in an earlier iteration) the eligible-runner filter.

---

## Delivery Status Pipeline

```
requested → approved → assigned → accepted → picked_up → in_transit → delivered
                     ↘ rejected
```

- **requested** — submitted by requester, awaiting admin review
- **approved** — admin has approved; awaiting runner assignment
- **assigned** — admin has assigned a runner; awaiting runner's response
- **accepted** — runner has acknowledged the task
- **picked_up** — runner has collected the document from the distribution center
- **in_transit** — runner is en route; live location sharing is active
- **delivered** — request complete; runner's availability resets
- **rejected** — admin declined the request at review stage

Every transition is logged in `document_status_history` with a timestamp, giving a full audit trail per request.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DocumentRequestController.php   # approve/reject/assign/archive/history
│   │   │   └── RunnerController.php            # runner management + pending approvals
│   │   ├── Api/
│   │   │   ├── LocationController.php          # live GPS push/pull
│   │   │   ├── ChatController.php              # chat send/fetch
│   │   │   └── StatusController.php            # status + ETA polling
│   │   ├── RequesterController.php
│   │   ├── RunnerDashboardController.php
│   │   └── RunnerRegistrationController.php    # public self-registration
│   └── Middleware/
│       ├── EnsureUserIsAdmin.php
│       ├── EnsureUserIsRunner.php               # also gates on is_approved
│       └── EnsureCanAccessRequestChat.php       # per-request chat authorization
├── Models/
│   ├── User.php, Runner.php, Zone.php
│   ├── DocumentRequest.php
│   ├── DocumentStatusHistory.php
│   └── ChatMessage.php
resources/views/
├── layouts/app.blade.php                        # shared shell: nav, footer, design tokens
├── admin/requests/  (index, assign, history)
├── admin/runners/   (index, create, pending)
├── requester/       (index, create, track)
├── runner/          (dashboard, active)
└── auth/register-runner.blade.php               # public runner sign-up
routes/
├── web.php
└── api.php
```

---

## Setup Instructions

### Prerequisites

- PHP 8.2+ (via XAMPP or standalone)
- Composer
- MySQL (via XAMPP)
- Node.js + npm

### 1. Clone and install dependencies

```bash
git clone https://github.com/<your-username>/papertrail.git
cd papertrail
composer install
npm install
```

### 2. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=papertrail_db
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000,localhost:8000
SESSION_DOMAIN=127.0.0.1
```

### 3. Create the database

In phpMyAdmin (or via CLI), create a database named `papertrail_db`.

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Register API routing (if not already present)

Laravel 12 requires explicit API route registration. Confirm `bootstrap/app.php` includes:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

If missing, run:
```bash
php artisan install:api
```

### 6. Seed at least one zone (required for request submission)

```bash
php artisan tinker
```
```php
\App\Models\Zone::create(['name' => 'Boyra']);
\App\Models\Zone::create(['name' => 'Khulna Sadar']);
exit
```

### 7. Build frontend assets and run the servers

In one terminal:
```bash
npm run dev
```

In a second terminal:
```bash
php artisan serve
```

Visit `http://127.0.0.1:8000`.

### 8. Create your first admin account

Register normally via `/register`, then promote the account to admin via tinker:

```bash
php artisan tinker
```
```php
$user = \App\Models\User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();
exit
```

---

## Environment Configuration

| Variable | Purpose |
|---|---|
| `SANCTUM_STATEFUL_DOMAINS` | Required for cookie-based API authentication from the same-origin Blade frontend |
| `SESSION_DOMAIN` | Must match the domain used to access the app (`127.0.0.1`, not `localhost`, if that's what you use) |
| `APP_URL` | Should match your local dev URL |

No external API keys are required anywhere in this project — map tiles are served free via OpenStreetMap, and there is no third-party geocoding or SMS service integrated.

---

## Testing Guide

Because the app has three distinct roles, testing requires **separate browser sessions** (not just tabs — Laravel sessions are shared across tabs in the same browser profile). Use one of:

- Two different browsers (e.g. Chrome for admin, Firefox for requester)
- A normal window + an Incognito window
- Multiple separate Incognito windows for 3-way testing

### Suggested test sequence

1. **Requester** submits a new request with a delivery pin
2. **Admin** approves it, then opens the assignment page and picks a runner
3. **Runner** sees the task notification, accepts it
4. **Runner** opens the active delivery page, starts location sharing, advances through pickup → in transit → delivered
5. **Requester** watches the status stepper update and the live map marker move (see note below on GPS)
6. Either party sends chat messages and confirms they appear on both sides

### Testing live location without a GPS-equipped device

Most laptops lack GPS hardware, so `watchPosition()` may return a static Wi-Fi-estimated location. For demo purposes, a debug route can simulate movement:

```php
// routes/web.php — remove before final submission
Route::get('/debug/simulate-movement/{runner}', function (\App\Models\Runner $runner) {
    $runner->update([
        'current_lat' => 22.8456 + (mt_rand(-100, 100) / 10000),
        'current_lng' => 89.5403 + (mt_rand(-100, 100) / 10000),
        'location_updated_at' => now(),
    ]);
    return 'Nudged.';
});
```

---

## Known Limitations

- **ETA is straight-line distance**, not road-routing distance (no routing API is integrated)
- **Chat and status updates use polling**, not WebSockets — sufficient for this scope, but introduces a few seconds of latency
- **No email notifications** — all status changes are visible only within the app
- **Debug simulate-movement route** must be removed or restricted before production use

## Future Enhancements

- Real road-distance ETA via a routing API (e.g. OSRM)
- WebSocket-based (Laravel Reverb / Pusher) live updates instead of polling
- Email/SMS notifications on status changes
- Runner delivery history page with performance stats
- Mobile-responsive layout pass

---

## License

Academic project — CSE 3100, Khulna University of Engineering & Technology.
