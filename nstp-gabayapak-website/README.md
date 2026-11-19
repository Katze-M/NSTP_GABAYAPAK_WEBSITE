# NSTP GabaYapak

A project management and monitoring system for SACSI's NSTP program (ROTC, LTS, and CWTS components). Students submit community service project proposals for review; staff (Formators, Coordinators, the Program Officer, and the SACSI Director) endorse, approve, reject, or archive them through a staged approval workflow.

## What It Does

- **Account registration & approval** — students and staff register, staff accounts go through an approval flow, and only one SACSI Director account is allowed at a time.
- **Project proposals** — students submit a project (team info, goals, target community, planned activities, and budget), save it as a draft, and resubmit if rejected.
- **Staged staff review** — NSTP Formators endorse, NSTP Coordinators approve, and the Program Officer/SACSI Director can oversee the whole pipeline. Rejected projects route back to the student with feedback and can be resubmitted.
- **Project lifecycle views** — Current, Pending, Rejected, and Archived project listings, organized by NSTP component and section.
- **Dashboard & reports** — an overview of projects and upcoming activities for staff, plus a reports page with per-component breakdowns and activity-completion progress bars.
- **Public landing page** — visitors can read about SACSI/NSTP and see the list of NSTP Formators without logging in first.
- **File uploads** — project logos are stored in S3-compatible object storage rather than on the app server.

## Tech Stack

- **Backend**: PHP 8.2+ / Laravel 12
- **Frontend**: Blade templates, Tailwind CSS 4, Vite, vanilla JS (SweetAlert2 for alerts)
- **Database**: PostgreSQL, hosted on [Neon](https://neon.tech)
- **File storage**: Neon Object Storage (S3-compatible)
- **Hosting**: Docker container (Apache + PHP) on [Render](https://render.com)
- **AI-assisted coding**: GitHub Copilot, Gemini, and Claude were used throughout for debugging, refactoring, and implementing features — hehe

## How to Run It

1. Clone the repository
2. Run `composer install` and `npm install`
3. Copy `.env.example` to `.env` and fill in:
   - A PostgreSQL connection (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) — e.g. a [Neon](https://neon.tech) project. If you're on Neon, use the pooled host for `DB_HOST` and set `DB_HOST_DIRECT` to the non-pooled host (used only for migrations).
   - S3-compatible storage credentials (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_DEFAULT_REGION`)
4. Run `php artisan key:generate`
5. Run `php artisan migrate` to create the tables (use `--database=pgsql_direct` if you're on Neon)
6. Run `npm run build` (or `npm run dev` while developing)
7. Run `php artisan serve` to start the development server

Seeding is optional and not fully set up — `php artisan migrate:fresh --seed` works but isn't required.

## Notes for Future Developers

- `AccountController` handles the Profile page; `LoginController` handles login and redirects staff to the Dashboard and students to the Upload Project page.
- `CheckRoles` (not the older `CheckRole`) is the active role-checking middleware and supports multiple roles.
- `app/Console/Commands/CleanupOrphanedMedia.php` deletes project logos left behind by deleted projects.
- `scripts/` contains old one-off debugging scripts kept around in case they're useful again — some predate the current Postgres schema and shouldn't be trusted as-is.
- The `pgsql_direct` database connection (`config/database.php`) is only for migrations — Neon's pooled connection doesn't reliably hold session state (like `search_path`) across a migration run. The app's normal runtime traffic uses the default pooled `pgsql` connection.
