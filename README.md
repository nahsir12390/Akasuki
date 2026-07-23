# Akatsuki Devs

Akatsuki Devs is a Laravel social media app for developers, with posts, comments, likes, friends-only chat, profiles, social login, password reset, and tutorial video pages.

## Requirements

- PHP 8.2+
- Composer 2+
- Node.js 18+
- MySQL or compatible database
- A web server pointed at the `public` directory
- A queue worker for production notifications/jobs
- Optional: Laravel Reverb for realtime chat

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

Run the test suite:

```bash
composer test
```

## Production Setup

Use `.env.production.example` as the production template. Never deploy your local `.env`.

Required production values:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `APP_KEY` generated on the server
- database credentials
- mail credentials
- OAuth redirect URLs if Google/GitHub login is enabled
- `YOUTUBE_API_KEY` if tutorial videos should load
- Reverb values if realtime chat is enabled

Typical cPanel SMTP values:

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

The app uses email for password reset links, contact form delivery, and optional new-message alerts controlled by each user's notification preferences.

Production build/deploy commands:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run a queue worker in production:

```bash
php artisan queue:work --tries=3
```

If using Reverb:

```bash
php artisan reverb:start
```

## Deployment Notes

- The server document root must be `public`, not the project root.
- Rotate OAuth secrets if they were ever exposed during testing.
- Make sure `storage` and `bootstrap/cache` are writable by the web server.
- Keep `composer.lock` and `package-lock.json` committed/deployed.
- Use `composer audit` and `npm audit --audit-level=moderate` before launch.

## Current Verification

The app has feature coverage for auth, posts, comments, likes, friendships, chat, password reset, and profile settings. Run `composer test` before every deploy.
