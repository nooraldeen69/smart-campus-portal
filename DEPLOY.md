# Putting the Smart Campus Portal on the internet

Two ways. **Option A** gives a permanent link (recommended for the professor). **Option B** is a 2-minute link from your own PC.

---

## Option A - Render.com (free, permanent link)

The repository already contains everything: `Dockerfile`, `docker/entrypoint.sh` and `render.yaml`.

### 1. Push the project to GitHub (clean up first)

Your repo currently tracks `.env` (with the app key), `vendor/`, the SQLite database and log files. Remove them from git
(they stay on your disk) and push:

```bash
git rm -r --cached --ignore-unmatch .env vendor database/database.sqlite storage/logs storage/framework/views bootstrap/cache/packages.php bootstrap/cache/services.php
git add -A
git commit -m "Hosting support: Dockerfile, Render blueprint, bug fixes"
git push
```

> The old `APP_KEY` was public on GitHub. The hosted site does **not** use it (it creates its own), but treat it as compromised
> and run `php artisan key:generate` for your local `.env` when convenient.

### 2. Deploy

1. Create a free account at <https://render.com> (sign in with GitHub).
2. **New +  ->  Blueprint**, choose the `smart-campus-portal` repository, click **Apply**.
3. Wait 5-10 minutes for the first build. Your address will look like `https://smart-campus-portal.onrender.com`.
4. **Admin password:** Render dashboard -> your service -> **Environment** -> `ADMIN_PASSWORD` (generated randomly).
   Log in as `ADMIN001` with it. Give that password to the professor only if you want them to see the admin panel.
5. Students: `20260001` (Layla), `20260002` (Omar), `20260003` (Sara) - password `password`. The login page shows these as one-click buttons
   while `DEMO_MODE=true`. Set `DEMO_MODE` to `false` in Render to hide them.

### What to know about the free plan

* The service **falls asleep after ~15 minutes without visitors**; the first request afterwards takes 30-60 s. Open the link yourself a few minutes before the professor does.
* The database is a SQLite file inside the container, so **data is reset on every restart / redeploy** and the demo data is re-created automatically. Perfect for a demo, not for real records.
* Every push to GitHub redeploys automatically.

For real, permanent data use a paid instance with a persistent disk (mount it and set `DB_DATABASE=/data/database.sqlite`) or switch to a hosted MySQL/PostgreSQL database (`DB_CONNECTION`, `DB_HOST`, ...).

### Other hosts

Any Docker host works the same way (Railway, Fly.io, Koyeb, a VPS): build the `Dockerfile`, expose the `PORT` it gives you and set the variables listed in `.env.production.example`.

---

## Option B - share your own PC for a while (tunnel)

Keep the portal running (`start-portal.bat` or `php artisan serve`) and in a second window:

```bash
# Cloudflare (no account needed)
cloudflared tunnel --url http://localhost:8000

# or ngrok
ngrok http 8000
```

It prints an `https://...trycloudflare.com` (or ngrok) address that works from anywhere while your PC and the two windows stay open.
`TRUSTED_PROXIES=*` in your `.env` makes the pages load correctly over the tunnel's HTTPS.

---

## Security checklist before sharing a public link

* Keep `APP_DEBUG=false` (set by the Dockerfile / `render.yaml`).
* Set a strong `ADMIN_PASSWORD`; the default `password` only exists for local demos.
* Demo student accounts are public knowledge while `DEMO_MODE=true` - don't put real personal data in the demo.
* Logins are limited to 5 wrong attempts per minute per account/IP.
