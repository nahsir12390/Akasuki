# Hosting Laravel Reverb on Render

Use this when the main Laravel app is hosted on cPanel/Whogohost and Reverb is hosted as a separate Render web service.

## 1. Create shared Reverb credentials

Use the same values in both the main Laravel app and the Render Reverb service.

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=akatsuki-devs
REVERB_APP_KEY=generate-a-long-random-key
REVERB_APP_SECRET=generate-a-long-random-secret
```

Generate random values with:

```bash
php -r "echo bin2hex(random_bytes(16)).PHP_EOL;"
```

## 2. Render Reverb service env vars

After Render creates the `akatsuki-reverb` service, set:

```env
APP_KEY=your-main-laravel-app-key
REVERB_APP_ID=same-as-main-app
REVERB_APP_KEY=same-as-main-app
REVERB_APP_SECRET=same-as-main-app
REVERB_SERVER_HOST=0.0.0.0
REVERB_HOST=akatsuki-reverb.onrender.com
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_ALLOWED_ORIGINS=https://your-cpanel-domain.com
```

## 3. Main cPanel app env vars

Set these in the cPanel Laravel `.env`:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=same-as-render
REVERB_APP_KEY=same-as-render
REVERB_APP_SECRET=same-as-render
REVERB_HOST=akatsuki-reverb.onrender.com
REVERB_PORT=443
REVERB_SCHEME=https
```

Then clear config cache on cPanel:

```bash
php artisan config:clear
php artisan config:cache
```

## How it works

The main Laravel app still receives `POST /chat/send`, saves the message in the database, and broadcasts the event. Render Reverb only keeps the WebSocket connection open and pushes the event to online users.
