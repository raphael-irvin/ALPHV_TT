# ALPHV Technical Assessment — Web Application

A full-stack web application built for the ALPHV Work-Based Learning (WBL) internship selection assessment. The system consists of a RESTful API backend and a vanilla frontend that communicates with it, featuring token-based authentication and a real-time public dashboard.

---

## Table of Contents

- [ALPHV Technical Assessment — Web Application](#alphv-technical-assessment--web-application)
  - [Table of Contents](#table-of-contents)
  - [Project Overview](#project-overview)
  - [Architecture](#architecture)
  - [Tech Stack](#tech-stack)
  - [Prerequisites](#prerequisites)
  - [Quick Start](#quick-start)
  - [Troubleshooting](#troubleshooting)
  - [Manual Setup](#manual-setup)
  - [Project Structure](#project-structure)
  - [API Reference](#api-reference)
    - [Public Endpoints](#public-endpoints)
      - [`GET /records`](#get-records)
      - [`POST /login`](#post-login)
    - [Protected Endpoints](#protected-endpoints)
      - [`POST /records`](#post-records)
      - [`PUT /records/{id}`](#put-recordsid)
      - [`DELETE /records/{id}`](#delete-recordsid)
      - [`POST /logout`](#post-logout)
  - [Authentication Flow](#authentication-flow)
  - [Real-Time Updates](#real-time-updates)
  - [Automated Testing](#automated-testing)
    - [Running the Tests](#running-the-tests)
    - [Test Coverage](#test-coverage)
  - [Design Decisions](#design-decisions)
  - [Known Limitations](#known-limitations)

---

## Project Overview

The application is split into two portals:

**User Portal (Public)** — A live dashboard anyone can view without logging in. It displays a table of records, each containing a name, a geometric shape, and a color. The table refreshes automatically every 3 seconds to reflect changes made by the administrator in real time.

**Admin Portal (Protected)** — A management interface restricted to authenticated administrators. It allows creating, editing, and deleting records through a form. All write operations require a valid bearer token obtained via login.

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     CLIENT BROWSER                      │
│                                                         │
│  ┌─────────────────┐       ┌─────────────────────────┐  │
│  │   user.html     │       │      admin.html         │  │
│  │  (Public View)  │       │  (Protected — token     │  │
│  │                 │       │   required)             │  │
│  │  Polls every    │       │  Full CRUD via          │  │
│  │  3 seconds      │       │  Bearer Token           │  │
│  └────────┬────────┘       └───────────┬─────────────┘  │
│           │                            │                │
│           │         login.html         │                │
│           │    (Issues Bearer Token)   │                │
│           └──────────────┬─────────────┘                │
└──────────────────────────┼──────────────────────────────┘
                           │ HTTP / JSON
                           ▼
┌─────────────────────────────────────────────────────────┐
│               LARAVEL 12 API BACKEND                    │
│                   localhost:8000                        │
│                                                         │
│  ┌───────────────┐   ┌────────────────────────────────┐ │
│  │ AuthController│   │       RecordController         │ │
│  │               │   │                                │ │
│  │  POST /login  │   │  GET    /api/records  (public) │ │
│  │  POST /logout │   │  POST   /api/records  (auth)   │ │
│  └───────────────┘   │  PUT    /api/records/{id}(auth)│ │
│                      │  DELETE /api/records/{id}(auth)│ │
│  Laravel Sanctum     └────────────────────────────────┘ │
│  (Token Auth)                                           │
└──────────────────────────┬──────────────────────────────┘
                           │ Eloquent ORM
                           ▼
┌─────────────────────────────────────────────────────────┐
│                  MySQL DATABASE                         │
│                   (XAMPP / Port 3306)                   │
│                                                         │
│   users  │  records  │  personal_access_tokens          │
└─────────────────────────────────────────────────────────┘
```

The backend and frontend are completely decoupled. The frontend is plain HTML/CSS/JavaScript served by a local PHP server — it has no knowledge of Laravel's internals and communicates exclusively through the public API.

---

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Backend Framework | Laravel | 12.x |
| Backend Language | PHP | 8.2+ |
| Authentication | Laravel Sanctum | 4.x |
| Database | MySQL (via XAMPP) | 8.x |
| Frontend | HTML, CSS, Vanilla JavaScript | — |
| Frontend Server | PHP Built-in Server | 8.2+ |
| Dependency Manager (PHP) | Composer | 2.x |
| Testing | PHPUnit | 11.x |

---

## Prerequisites

Before running the application, ensure the following are installed on your machine:

- **XAMPP** (with Apache and MySQL components) — [https://www.apachefriends.org](https://www.apachefriends.org)
- **PHP 8.2 or higher** — included with XAMPP, or installed separately
- **Composer** — [https://getcomposer.org](https://getcomposer.org)

To verify your PHP and Composer installations, open a terminal and run:

```bash
php --version
composer --version
```

---

## Quick Start

This is the recommended way to run the project. The launcher script handles all setup steps automatically.

**Step 1 — Start XAMPP services**

Open the XAMPP Control Panel and click **Start** next to both **Apache** and **MySQL**.

**Step 2 — Create the database**

Navigate to `http://localhost/phpmyadmin` in your browser and create a new, empty database named exactly:

```
alphv_db
```

**Step 3 — Configure environment credentials**

Open `alphv-backend/.env` (or copy `.env.example` to `.env` if it does not exist) and ensure the following values are set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alphv_db
DB_USERNAME=root
DB_PASSWORD=
```

**Step 4 — Run the launcher**

Double-click `start_app.bat` from the project root. The script will:

- Install Composer dependencies (first run only)
- Generate the application encryption key
- Run all database migrations
- Seed the admin account
- Start both the backend and frontend servers
- Open the browser automatically to the login page

> Keep both terminal windows that the launcher opens running for as long as you use the application.

**Default admin credentials:**

| Field | Value |
|---|---|
| Email | admin@alphv.com |
| Password | admin |

---

## Troubleshooting

### MySQL fails to start in XAMPP (port conflict)

This is the most common setup issue. If the MySQL row in the XAMPP Control Panel stays red or immediately turns red after clicking **Start**, another MySQL process is already occupying port 3306. This typically happens on machines that have a standalone MySQL installation (e.g. MySQL Community Server, MySQL Workbench, or a previous XAMPP installation) running as a Windows service in the background.

**How to identify the conflict**

Open a terminal and run:

```bash
netstat -ano | findstr :3306
```

If any line appears, port 3306 is already in use. Note the PID in the last column and open Task Manager to identify the process.

**Fix A — Task Manager (quickest)**

1. Press `Ctrl + Shift + Esc` to open Task Manager.
2. Click the **Details** tab (or search for the process name).
3. Find any process named **mysqld.exe** or **MySQL**.
4. Right-click → **End Task** (repeat for all MySQL processes found).
5. Return to XAMPP and click **Start** next to MySQL.

> This stops the process for the current session only. It will start again on next reboot if it is registered as a Windows service. Use Fix B below to make the change permanent.

**Fix B — Stop the conflicting Windows service (permanent)**

1. Press `Win + R`, type `services.msc`, and press Enter.
2. Scroll to find a service named **MySQL**, **MySQL80**, or similar.
3. Right-click → **Stop**.
4. Right-click → **Properties** → set **Startup type** to **Manual** to prevent it from auto-starting on reboot.
5. Return to XAMPP and click **Start** next to MySQL.

**Fix C — Change XAMPP MySQL to a different port**

If you need both MySQL instances running simultaneously:

1. In the XAMPP Control Panel, click **Config** next to MySQL and open `my.ini`.
2. Find the `port=3306` line under both `[mysqld]` and `[client]` and change both to `3307` (or another free port).
3. Open `alphv-backend/.env` and update:
   ```env
   DB_PORT=3307
   ```
4. Restart XAMPP MySQL.

> The application also prints a port conflict warning at startup — if `php artisan migrate` fails, read the error output in the launcher window for step-by-step guidance.

---

## Manual Setup

If you prefer to run each step yourself, or if the launcher does not work on your system:

**1. Install PHP dependencies**

```bash
cd alphv-backend
composer install
```

**2. Configure the environment**

```bash
cp .env.example .env
```

Edit `.env` with your MySQL credentials as shown in Step 3 above.

**3. Generate the application key**

```bash
php artisan key:generate
```

This fills in the `APP_KEY` value in your `.env` file. Laravel uses it to encrypt sessions and tokens. The application will not start without it.

**4. Run database migrations**

```bash
php artisan migrate
```

**5. Seed the admin account**

```bash
php artisan db:seed
```

**6. Start the backend server**

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`.

**7. Start the frontend server**

Open a second terminal window:

```bash
cd alphv-frontend
php -S localhost:5500
```

**8. Open the application**

Navigate to `http://localhost:5500/login.html` in your browser.

---

## Project Structure

```
project-root/
│
├── start_app.bat               # One-click launcher (Windows)
│
├── alphv-backend/              # Laravel 12 API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AuthController.php      # Login / Logout
│   │   │       └── RecordController.php    # CRUD for records
│   │   └── Models/
│   │       ├── User.php                    # Sanctum-enabled user model
│   │       └── Record.php                  # Shape/color record model
│   ├── database/
│   │   ├── factories/
│   │   │   └── RecordFactory.php           # Generates fake records for testing/seeding
│   │   ├── migrations/
│   │   │   ├── ..._create_records_table.php
│   │   │   ├── ..._create_users_table.php
│   │   │   └── ..._create_personal_access_tokens_table.php
│   │   └── seeders/
│   │       ├── DatabaseSeeder.php          # Orchestrates all seeders
│   │       └── RecordSeeder.php            # Seeds 25 fake records (idempotent)
│   ├── routes/
│   │   └── api.php                         # All API route definitions
│   └── tests/
│       └── Feature/
│           └── RecordApiTest.php           # Automated feature tests (8 tests)
│
└── alphv-frontend/             # Plain HTML/CSS/JS frontend
    ├── login.html              # Login page
    ├── admin.html              # Protected admin portal
    ├── user.html               # Public live dashboard
    ├── shared.js               # Shared utilities: fetch engine, pagination, sort headers
    └── styles.css              # Shared stylesheet
```

---

## API Reference

All endpoints are served from `http://127.0.0.1:8000/api`.

### Public Endpoints

No authentication required.

---

#### `GET /records`

Returns a paginated, sortable list of records. **10 records per page.** All query parameters are optional.

**Query parameters:**

| Parameter | Type | Default | Allowed values | Description |
|---|---|---|---|---|
| `page` | integer | `1` | Any positive integer | The page number to retrieve |
| `sort_by` | string | `updated_at` | `name`, `shape`, `color`, `updated_at` | Column to sort by |
| `sort_dir` | string | `desc` | `asc`, `desc` | Sort direction |

Any value outside the allowed set is silently ignored and the default is used instead.

**Response `200 OK`:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Sample Shape",
      "shape": "circle",
      "color": "blue",
      "created_at": "2026-05-30T10:00:00.000000Z",
      "updated_at": "2026-05-30T10:00:00.000000Z"
    }
  ],
  "first_page_url": "http://127.0.0.1:8000/api/records?page=1",
  "from": 1,
  "last_page": 3,
  "last_page_url": "http://127.0.0.1:8000/api/records?page=3",
  "next_page_url": "http://127.0.0.1:8000/api/records?page=2",
  "path": "http://127.0.0.1:8000/api/records",
  "per_page": 10,
  "prev_page_url": null,
  "to": 10,
  "total": 25
}
```

The `data` array contains the records for the requested page. The surrounding fields are Laravel's standard pagination metadata used by the frontend to render navigation controls and sort indicators.

---

#### `POST /login`

Authenticates a user and returns a bearer token.

**Request body:**
```json
{
  "email": "admin@alphv.com",
  "password": "admin"
}
```

**Response `200 OK`:**
```json
{
  "message": "Login Successful",
  "token": "1|abc123...",
  "user": "Admin User"
}
```

**Response `401 Unauthorized`** (invalid credentials):
```json
{
  "message": "Invalid Login Credentials"
}
```

---

### Protected Endpoints

All protected endpoints require the following header:

```
Authorization: Bearer {token}
```

---

#### `POST /records`

Creates a new record.

**Request body:**
```json
{
  "name": "My Shape",
  "shape": "triangle",
  "color": "red"
}
```

**Response `201 Created`:**
```json
{
  "message": "Record successfully created!",
  "data": {
    "id": 2,
    "name": "My Shape",
    "shape": "triangle",
    "color": "red",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

**Response `422 Unprocessable Entity`** (validation failure):
```json
{
  "message": "The name field is required.",
  "errors": { ... }
}
```

**Response `401 Unauthorized`** (missing or invalid token):
```json
{
  "message": "Unauthenticated."
}
```

---

#### `PUT /records/{id}`

Updates an existing record by ID.

**Request body:**
```json
{
  "name": "Updated Shape",
  "shape": "square",
  "color": "green"
}
```

**Response `200 OK`:**
```json
{
  "message": "Record successfully updated!",
  "data": { ... }
}
```

**Response `404 Not Found`** (record does not exist):
```json
{
  "message": "No query results for model [App\\Models\\Record] {id}"
}
```

---

#### `DELETE /records/{id}`

Deletes a record by ID.

**Response `200 OK`:**
```json
{
  "message": "Record successfully deleted!"
}
```

---

#### `POST /logout`

Invalidates the current bearer token.

**Response `200 OK`:**
```json
{
  "message": "Successfully logged out"
}
```

---

## Authentication Flow

The application uses **Laravel Sanctum** with API token (bearer token) authentication. The flow works as follows:

```
1. User submits credentials via login.html
       │
       ▼
2. POST /api/login — credentials validated against the database
       │
       ▼
3. Laravel issues a Sanctum token and returns it in the response
       │
       ▼
4. Frontend stores the token in localStorage
       │
       ▼
5. Every subsequent protected request sends the token in the
   Authorization header: Bearer {token}
       │
       ▼
6. The auth:sanctum middleware validates the token on each request
       │
       ▼
7. On logout, POST /api/logout deletes the token from the database,
   permanently invalidating it. The frontend clears localStorage
   and redirects to the login page.
```

The admin page performs a client-side token presence check on load. If no token is found in `localStorage`, the user is immediately redirected to the login page before any content renders.

---

## Real-Time Updates

The User Portal achieves live synchronisation through **short polling**. Every 3 seconds, the page fires a `GET /api/records` request and re-renders the table with the latest data from the server.

The polling interval is both page-aware and sort-aware — it always refreshes whichever page the user is currently viewing with the currently active sort order applied:

```javascript
let currentPage    = 1;
let currentSortBy  = 'updated_at';
let currentSortDir = 'desc';

fetchRecords(1);
setInterval(() => fetchRecords(currentPage), 3000);
```

This approach was chosen for its simplicity and reliability within the scope of this assessment. It requires no additional server infrastructure and works across all browsers without configuration.

A production-grade alternative would be **WebSockets** (via Laravel Echo + Pusher or a self-hosted socket server), which would push changes to all connected clients the moment they occur, eliminating the polling interval and reducing server load at scale.

---

## Automated Testing

The project includes a PHPUnit feature test suite covering the full lifecycle of the records API.

### Running the Tests

From the `alphv-backend` directory:

```bash
php artisan test
```

Or to run only the record tests specifically:

```bash
php artisan test --filter RecordApiTest
```

### Test Coverage

| Test | Description | Expected Result |
|---|---|---|
| `test_unauthenticated_user_cannot_modify_records` | Attempts to create a record without a token | `401 Unauthorized` |
| `test_api_can_create_a_record` | Authenticated admin creates a new record | `201 Created`, record exists in database |
| `test_api_can_update_a_record` | Authenticated admin updates an existing record | `200 OK`, updated values in database |
| `test_api_can_delete_a_record` | Authenticated admin deletes a record | `200 OK`, record absent from database |
| `test_api_deny_invalid_data_on_create` | Authenticated admin submits an invalid shape/color on create | `422 Unprocessable Entity` |
| `test_api_deny_invalid_data_on_update` | Authenticated admin submits an invalid shape/color on update | `422 Unprocessable Entity` |
| `test_records_index_returns_paginated_response_structure` | Seeds 15 records, hits page 1 — verifies full paginator envelope and 10-item page | `200 OK`, correct `current_page`, `last_page`, `total`, `per_page`, 10 items in `data` |
| `test_records_index_page_2_returns_correct_slice` | Seeds 15 records, hits `?page=2` — verifies the second page returns the remaining 5 | `200 OK`, `current_page=2`, 5 items in `data` |

Tests use an **in-memory SQLite database** (configured in `phpunit.xml`) so they run in complete isolation from your development MySQL database. The `RefreshDatabase` trait rolls back all changes after each test, keeping the test environment clean between runs.

`Sanctum::actingAs()` is used to simulate an authenticated user, allowing tests to hit protected routes without needing to go through the real login flow.

`Record::factory()` is used in the pagination tests to generate any number of fake records instantly, with all values constrained to the API's valid shape/color enums.

---

## Design Decisions

**Why a separated frontend and backend?**

Keeping the two layers entirely separate enforces a clean API contract between them. The frontend only ever communicates via HTTP/JSON — it has no access to Laravel internals, Blade templates, or Eloquent. This mirrors real-world SPA architectures and makes each side independently replaceable.

**Why Laravel Sanctum over Passport?**

Sanctum is the right tool for this scope. Passport is built for OAuth2 flows involving third-party apps and refresh tokens. Sanctum handles simple SPA and API token authentication with minimal setup, which is exactly what this application needs.

**Why short polling over WebSockets?**

Short polling requires no additional infrastructure (no Pusher account, no socket server, no Redis queue). For an assessment environment running locally, it is the most reliable option. The 3-second interval is fast enough to feel real-time for a human observer.

**Why store the token in localStorage?**

For a simple assessment SPA without a server-side session, localStorage is the most straightforward place to keep the token. The trade-off is that it is readable by JavaScript on the page, making it theoretically vulnerable to XSS attacks. In a production application, storing tokens in `HttpOnly` cookies is the more secure pattern.

**Why `paginate()` over `simplePaginate()`?**

Laravel offers two built-in pagination helpers. `simplePaginate()` produces only previous/next links with no total count, which is slightly more efficient. `paginate()` (used here) issues an additional `COUNT(*)` query to return the `total` and `last_page` fields. These two fields are required to display the "Page X of Y (N total)" summary in the frontend controls, so the small overhead is justified.

**Why server-side sorting over client-side sorting?**

With pagination active, client-side sorting would only reorder the 10 records already on the current page — the globally first record in the sorted order might be sitting on page 2 or 3, making the result actively misleading. Server-side sorting passes `sort_by` and `sort_dir` query parameters to `GET /api/records`, and Laravel's `orderBy()` sorts the full dataset before paginating it, ensuring the sort order is consistent across all pages. Clicking a column header always resets to page 1 so the user sees the sorted result from the beginning.

The `sort_by` column is validated against an explicit allowlist (`name`, `shape`, `color`, `updated_at`) before being passed to `orderBy()`. This prevents SQL column-injection — an attacker cannot force an arbitrary expression into the query by manipulating the query parameter.

---

## Known Limitations

These are acknowledged trade-offs made given the scope of the assessment:

- **Pagination page size is hardcoded** — The 10-records-per-page limit is set directly in `RecordController::index()`. A production API would expose this as a configurable query parameter (e.g. `?per_page=25`) with a sensible maximum cap.
- **Weak admin password** — The seeded password `admin` is intentionally simple for ease of evaluation. In any real deployment, a strong password policy would apply.
- **localStorage token storage** — As described above, `HttpOnly` cookies are the production-recommended alternative.
- **No rate limiting** — The login endpoint does not limit failed attempts. Laravel's built-in `throttle` middleware could be applied to the login route to prevent brute-force attacks.
- **Short polling vs. WebSockets** — The polling approach creates one HTTP request per client every 3 seconds. At scale, WebSockets or Server-Sent Events would be far more efficient.