# Deployment Checklist

Use this checklist when moving Akatsuki Devs from XAMPP/local development to hosting.

## Render Deployment

This repo is now prepared for Render with:

- `Dockerfile`
- `docker/nginx.conf`
- `docker/start.sh`
- `render.yaml`
- PostgreSQL support through `DATABASE_URL`
- automatic migrations on boot
- automatic admin seeding on boot

### 1. Push The Repo

Push the full project to GitHub, including the new Render files. Do not commit `.env`.

### 2. Create From Blueprint

In Render:

1. Choose **New > Blueprint**.
2. Connect the GitHub repo.
3. Render will read `render.yaml`.
4. It will create:
   - a Docker web service
   - a PostgreSQL database
   - a persistent disk mounted at `/var/www/html/storage`

### 3. Set Required Environment Variables

In the Render web service environment tab, set:

```env
APP_URL=https://your-render-service.onrender.com
ASSET_URL=https://your-render-service.onrender.com
APP_KEY=base64:your-generated-key
```

Generate `APP_KEY` locally with:

```bash
php artisan key:generate --show
```

Render provides `DATABASE_URL` automatically from the blueprint database. Keep:

```env
DB_CONNECTION=pgsql
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
BROADCAST_CONNECTION=log
```

Optional but recommended:

```env
YOUTUBE_API_KEY=your-youtube-api-key
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_CONTACT_TO_ADDRESS=your-support-email
```

### 4. First Boot

The Docker startup script runs:

```bash
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Admin login seeded on Render:

```text
Email: naruto@gmail.com
Password: nasiru12390
```

Change this password from the app after first login if the site is public.

### 5. Render Notes

- Health check path: `/up`
- Web port: `10000`
- Public uploads use the mounted Render disk at `/var/www/html/storage`.
- Realtime chat is left as `BROADCAST_CONNECTION=log` for Render simplicity. Add a separate worker/Reverb service later if you want production realtime chat.

## 1. Server

- PHP 8.2 or newer is installed.
- Required PHP extensions are enabled: `bcmath`, `ctype`, `curl`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`.
- The web server document root points to `public`.
- `storage` and `bootstrap/cache` are writable.
- HTTPS is enabled.

## 2. Environment

Copy `.env.production.example` to `.env` on the server and set real values.

Critical values:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `APP_KEY`
- `DB_*`
- `MAIL_*`
- `MAIL_CONTACT_TO_ADDRESS`
- `MAIL_CONTACT_TO_NAME`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_DOMAIN=.your-domain.com`

Optional services:

- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`, `GITHUB_REDIRECT_URI`
- `YOUTUBE_API_KEY`
- `REVERB_*` and `BROADCAST_CONNECTION=reverb`

For cPanel email, create an email account in cPanel first, then use the SMTP details from cPanel:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.your-domain.com
MAIL_PORT=465
MAIL_SCHEME=ssl
MAIL_USERNAME=no-reply@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
MAIL_CONTACT_TO_ADDRESS=support@your-domain.com
MAIL_CONTACT_TO_NAME="${APP_NAME} Support"
```

If your host uses TLS on port 587, set `MAIL_PORT=587` and `MAIL_SCHEME=tls` instead.

## 3. Install And Build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Only run `php artisan key:generate --force` for a new production install. Do not rotate `APP_KEY` after users exist unless you understand the impact on encrypted data and sessions.

## 4. Workers

For database queues:

```bash
php artisan queue:work --tries=3 --sleep=3
```

Use your host's process manager, Supervisor, systemd, or hosting dashboard to keep it running.

For realtime chat with Reverb:

```bash
php artisan reverb:start
```

If your host does not support long-running workers, keep `BROADCAST_CONNECTION=log` and realtime chat will be disabled gracefully.

## 5. Preflight Checks

Run before launch:

```bash
composer test
composer audit
npm audit --audit-level=moderate
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Then visit:

- `/`
- `/login`
- `/register`
- `/post`
- `/my-profile`
- `/chat`
- `/forgotpassword`

## 6. After Launch

- Confirm mail delivery works with password reset.
- Confirm uploaded profile photos and post media display.
- Confirm OAuth redirect URLs exactly match the production domain.
- Confirm `APP_DEBUG=false` by checking that errors do not show stack traces.
- Monitor `storage/logs/laravel.log`.
