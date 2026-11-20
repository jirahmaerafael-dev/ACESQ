# ACESQ

ACESQ (A Simple Campus Office Queuing System) is a web-based queue for the ACES Tagum College Registrar's Office. Students select a service window, agree to the queue terms, receive a number, and follow their live queue status without creating an account.

## Application behavior

- Each active window has an independent daily queue. The seeded windows are Registrar, Cashier, Window 3, Window 4, and Window 5.
- Queue numbers are sequential per window and day. A student's private ticket link identifies the queue record; no student profile is stored.
- A called number has a five-minute grace period. The first missed call moves it back five waiting positions, the second moves it back ten, and the third forfeits it.
- The student's status page refreshes every eight seconds. It shows the ticket, waiting position, current number, estimated wait, and grace-period countdown.
- Staff sign in with a username and password and can manage only their assigned window.
- Administrators manage windows and staff accounts and can monitor all queues and missed calls. Admin routes do not provide queue actions.
- Grace-period expiry is processed by the scheduler and by queue status/dashboard requests, so it does not depend on a staff member taking action.

The ACESQ domain schema is `windows`, `staff_users`, `queue_records`, and `missed_calls`. The original Laravel scaffold's `users` and `roles` tables are no longer used by ACESQ authentication; existing tables are left intact to avoid deleting data from an existing installation.

## Requirements

- PHP 8.3 or later
- Composer
- MySQL
- Node.js and npm (for Laravel's frontend tooling)

## Local setup

1. Install PHP and Composer dependencies:

   ```powershell
   composer install
   ```

2. Create the local environment file and application key:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Configure `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` for MySQL. Set a private `ACESQ_ADMIN_PASSWORD` there before seeding. The corresponding admin username defaults to `admin`; `ACESQ_ADMIN_USERNAME` can override it.

4. Create the schema and seed the five default windows plus the configured admin account:

   ```powershell
   php artisan migrate --seed
   ```

   If `ACESQ_ADMIN_PASSWORD` is empty, the seeder intentionally does not create an administrator. Set it in `.env` and run `php artisan db:seed --class=AdminSeeder` to provision the admin.

5. Start the Laravel development server:

   ```powershell
   php artisan serve
   ```

   Open the URL printed by Artisan. Students use the home page. Staff and administrators use `/login`.

6. In a separate terminal, run Laravel's scheduler worker so missed-call penalties are applied even when nobody is viewing a queue:

   ```powershell
   php artisan schedule:work
   ```

   Queue status and staff/admin page requests also check for expired calls.

## Roles and routes

- **Student:** no login; selects an active window, accepts terms, then follows the private ticket link.
- **Staff:** username/password login; assigned-window queue dashboard at `/staff`.
- **Admin:** username/password login; window, account, and all-window monitoring panel at `/admin`.

Passwords are hashed. Admins can add, rename, open/close, and remove unused windows; create, edit, reassign, reset passwords, and delete accounts; and review today's queue and missed-call history.
