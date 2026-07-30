# Deploying Peoples Scholar

Laravel 13 · PHP 8.3+ · MySQL/MariaDB · Blade + AdminLTE 2 (server-rendered)

---

## Which host

**DigitalOcean.** Netlify cannot run this application at all — it serves static
sites and JS/Go/Rust serverless functions, with no PHP runtime and no
persistent MySQL connection model. This app is server-rendered PHP with
file-based sessions, so the mismatch is absolute rather than a matter of
preference.

Applicant photo uploads are stored on **DigitalOcean Spaces** (S3-compatible,
fully public bucket/CDN), not local disk — see "Object storage (Spaces)"
below. That means App Platform's ephemeral filesystem is no longer a blocker;
either option below works.

Two DigitalOcean options:

| | Droplet | App Platform (recommended) |
|---|---|---|
| PHP version | you choose | needs a custom Dockerfile |
| Uploads | Spaces (see below) | Spaces (see below) |
| Cost | from $6/mo | from ~$5/mo + managed DB |
| Ops | you patch the OS | managed |

App Platform is now the simpler fit: no OS patching, no server to secure, and
uploads already live in object storage. Bring your own Dockerfile (PHP 8.3+
with the `pdo_mysql` and standard Laravel extensions) targeting
`app/scholarship/public` as the document root; App Platform's managed MySQL
add-on replaces the self-hosted database.

If you'd rather run a Droplet, a **$12/mo Droplet (2GB RAM)** plus **daily
backups ($2.40/mo)** is a sensible starting point. 1GB works but leaves
little headroom for MySQL alongside PHP-FPM.

---

## Object storage (Spaces)

Applicant photos upload straight to a DigitalOcean Spaces bucket via
Laravel's `spaces` filesystem disk (`config/filesystems.php`) — nothing is
written to local disk, which is what makes App Platform's ephemeral
filesystem safe to use. The bucket/folder must be **fully public** (Spaces
CDN endpoint, not the private origin), since photo URLs are rendered directly
in `<img>`/CSS `background-image` with no signed-URL logic.

Required env vars:

```ini
DO_SPACES_KEY=
DO_SPACES_SECRET=
DO_SPACES_REGION=        # e.g. blr1 — the region slug from the endpoint
DO_SPACES_ENDPOINT=      # https://<region>.digitaloceanspaces.com
DO_SPACES_CDN_ENDPOINT=  # https://<bucket>.<region>.cdn.digitaloceanspaces.com
DO_SPACES_BUCKET=
DO_SPACES_FOLDER=        # per-project prefix inside the shared bucket
FILESYSTEM_DISK=spaces
```

`DO_SPACES_FOLDER` may contain spaces (it's just an S3 key prefix) — quote it
in `.env` (`DO_SPACES_FOLDER="peoples scholar"`) or dotenv parsing fails.
`Person::photo_cdn_url` (`app/Person.php`) builds the public URL and
percent-encodes it manually, since the disk's own URL generator does not
encode spaces in the root prefix — required because a raw space breaks
unquoted CSS `url(...)` and any non-browser HTTP client.

The `league/flysystem-aws-s3-v3` Composer package provides the S3-compatible
driver and is already in `composer.json` — `composer install` pulls it in,
no extra step needed.

`FileController`/the `/storage/uploads/{filename}` route (`routes/web.php`)
are unused now that photos resolve straight to the CDN, but are left in
place — harmless dead code, not wired into any view.

---

## The one thing you must not get wrong

**The web root must be `app/scholarship/public` — nothing above it.**

The old cPanel deployment served the whole `app/` directory, which put
`app/scholarship/.env` (database credentials), the entire source tree, and two
multi-hundred-megabyte zip backups inside the web root. Anyone could have
fetched them over HTTP.

Correct:

```
root /var/www/peoples-scholar/app/scholarship/public;
```

Verify after deploying — every one of these must fail:

```bash
curl -I https://your-domain/.env                  # expect 404
curl -I https://your-domain/scholarship/.env      # expect 404
curl -I https://your-domain/composer.json         # expect 404
curl -I https://your-domain/app.zip               # expect 404
```

Also delete the leftover archives; they do not belong on the server:

```bash
rm -f app.zip app/scholorship.zip ppf_scholership.sql
```

---

## App Platform configuration

Sections 1–6 below describe the **Droplet** path. On App Platform there is no
server to set up: configuration is entirely environment variables set in the
app's console (Settings → your component → Environment Variables).

**Never copy your local `.env` into these variables.** `DB_HOST=127.0.0.1`
means "this container", and the database is not in this container — the
connection is refused no matter what the port says. The same applies to
`APP_URL` and `APP_DEBUG`.

The application runs on MongoDB. Point it at the Atlas cluster with the
connection string from Atlas (Connect → Drivers → PHP):

```ini
DB_CONNECTION=mongodb
MONGODB_URI="mongodb+srv://user:password@cluster.xxxxx.mongodb.net/?retryWrites=true&w=majority"
MONGODB_DATABASE=ppf_scholership
```

Mark `MONGODB_URI` as **encrypted** — it contains the database password.

> **Atlas network access.** App Platform containers do not have a stable
> outbound IP, so an Atlas allowlist naming specific addresses will drop
> the connection after any redeploy or scaling event. Either allow
> `0.0.0.0/0` and rely on the connection string's credentials plus TLS,
> or attach a dedicated egress IP to the app and allowlist that. A
> refused connection that "worked yesterday" is almost always this.

The rest:

```ini
APP_ENV=production
APP_DEBUG=false                 # see the warning below
APP_KEY=base64:...              # generate once, never change; mark encrypted
APP_URL=https://your-app.ondigitalocean.app
LOG_CHANNEL=stderr              # App Platform captures stdout/stderr, not files
LOG_LEVEL=error
SESSION_SECURE_COOKIE=true
```

Plus the `FILESYSTEM_DISK` and `DO_SPACES_*` values from "Object storage"
above. Mark every secret as **encrypted**.

> **`APP_DEBUG=true` in production is a data leak, not just untidy.** Laravel's
> debug error page publishes the full stack trace, absolute file paths,
> database host, port and name, the loaded configuration, and the visitor's IP
> — to anyone who can trigger an error on a public URL. On an app holding
> applicant personal data, treat having shipped it as an incident: set it to
> `false`, redeploy, and rotate any credential that appeared on a page a
> stranger could have loaded.

### Moving the data from MySQL

The application was migrated from MySQL to MongoDB. `php artisan mongo:import`
copies every table across, and is meant to be run once, from a machine that
can reach both databases:

```bash
# .env needs the MySQL credentials (DB_HOST/DB_PORT/...) as the source
# and MONGODB_URI/MONGODB_DATABASE as the destination.
php artisan mongo:import --fresh
```

`--fresh` drops each destination collection first, so the command is safe to
re-run: it always produces a clean copy rather than duplicating documents. It
prints a row count per collection and exits non-zero if any collection does
not match the source, so a partial import cannot pass unnoticed.

Two details it takes care of, both of which the application depends on:

- **Ids are preserved.** Each row's integer `id` becomes the document `_id`,
  so the foreign keys already stored in other tables still resolve. MongoDB
  has no auto-increment, so a `counters` collection is seeded with the highest
  id per collection and new records continue from there.
- **Numeric columns stay numeric.** PDO returns most values as strings, and
  MongoDB's `sum()` silently ignores non-numeric values — left as strings,
  every grant total on the reports would read zero.

Do not point the application at MongoDB until the import has been run and
verified; the schema is not created by `artisan migrate`.

### Build-time vs run-time caching

`php artisan config:cache` bakes the *current* environment into
`bootstrap/cache/config.php`. If it runs during the **build** while the
database variables are scoped run-time-only, the cache is written from
defaults and the app connects to `127.0.0.1:3306` forever after — the cached
file wins over the real environment at runtime.

Either scope those variables to **RUN_AND_BUILD_TIME**, or move the caching
into the run command so it executes with the real environment:

```
php artisan config:cache && php artisan route:cache && php artisan view:cache && heroku-php-apache2 app/scholarship/public/
```

`view:cache` is safe at build time; `config:cache` is the one that bites.

---

## 1. Server setup

Ubuntu 24.04 LTS Droplet, as root:

```bash
apt update && apt upgrade -y
apt install -y nginx mariadb-server certbot python3-certbot-nginx unzip git \
  php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl

mysql_secure_installation
```

Firewall — only SSH and HTTP(S):

```bash
ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw --force enable
```

Create a deploy user so the app does not run as root:

```bash
adduser --disabled-password --gecos "" deploy
usermod -aG www-data deploy
```

## 2. Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE ppf_scholership CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ppf'@'localhost' IDENTIFIED BY 'use-a-long-random-password';
GRANT ALL PRIVILEGES ON ppf_scholership.* TO 'ppf'@'localhost';
FLUSH PRIVILEGES;
```

Import the existing data:

```bash
mysql -u ppf -p ppf_scholership < ppf_scholership.sql
```

The schema comes from this dump, **not** from `artisan migrate` — the repo has
only two migrations and they do not describe the real tables. Do not run
`migrate:fresh` against production; it would drop everything.

## 3. Application

```bash
cd /var/www
git clone <your-repo> peoples-scholar
cd peoples-scholar/app/scholarship

composer install --no-dev --optimize-autoloader

cp .env.example .env
php artisan key:generate
```

Edit `.env` — the values that matter:

```ini
APP_ENV=production
APP_DEBUG=false                 # never true in production
APP_URL=https://your-domain
LOG_LEVEL=error

DB_DATABASE=ppf_scholership
DB_USERNAME=ppf
DB_PASSWORD=the-password-you-set

SESSION_SECURE_COOKIE=true      # requires HTTPS

FILESYSTEM_DISK=spaces
DO_SPACES_KEY=...
DO_SPACES_SECRET=...
DO_SPACES_REGION=...
DO_SPACES_ENDPOINT=...
DO_SPACES_CDN_ENDPOINT=...
DO_SPACES_BUCKET=...
DO_SPACES_FOLDER=...
```

See "Object storage (Spaces)" above for what each of these means.

`APP_KEY` must be generated once and then never changed — it encrypts session
cookies, so rotating it logs everyone out.

Permissions — only `storage` and `bootstrap/cache` are writable by the server:

```bash
chown -R deploy:www-data /var/www/peoples-scholar
find /var/www/peoples-scholar -type d -exec chmod 755 {} \;
find /var/www/peoples-scholar -type f -exec chmod 644 {} \;
chmod -R 775 app/scholarship/storage app/scholarship/bootstrap/cache
chmod 640 app/scholarship/.env
```

Applicant photos live on Spaces, not local disk, so there is nothing to
rsync — just cache everything:

```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 4. nginx

`/etc/nginx/sites-available/peoples-scholar`:

```nginx
server {
    listen 80;
    server_name your-domain www.your-domain;

    # The public directory only — never the project root.
    root /var/www/peoples-scholar/app/scholarship/public;
    index index.php;

    charset utf-8;
    client_max_body_size 12M;          # applicant photo uploads

    add_header X-Frame-Options SAMEORIGIN always;
    add_header X-Content-Type-Options nosniff always;
    add_header Referrer-Policy strict-origin-when-cross-origin always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Belt and braces: never serve dotfiles or stray archives.
    location ~ /\.(?!well-known).* { deny all; }
    location ~* \.(sql|zip|bak|log)$ { deny all; }

    location ~* \.(jpg|jpeg|png|gif|svg|ico|css|js|woff2?|ttf|eot)$ {
        expires 30d;
        access_log off;
    }

    error_page 404 /index.php;
}
```

```bash
ln -s /etc/nginx/sites-available/peoples-scholar /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

Raise PHP's upload limits to match `client_max_body_size` in
`/etc/php/8.3/fpm/php.ini`:

```ini
upload_max_filesize = 12M
post_max_size = 12M
```

```bash
systemctl restart php8.3-fpm
```

## 5. HTTPS

```bash
certbot --nginx -d your-domain -d www.your-domain
```

Certbot installs the renewal timer itself. Confirm with
`systemctl list-timers | grep certbot`.

Only set `SESSION_SECURE_COOKIE=true` **after** HTTPS works, or you will not be
able to log in.

## 6. Backups

The database is the irreplaceable part on the server itself — applicant
photos live on Spaces, which DigitalOcean backs separately. Enable Spaces
bucket versioning or a periodic Spaces-to-Spaces sync if you want a second
copy of the photos too.

```bash
cat > /usr/local/bin/ppf-backup <<'SH'
#!/bin/bash
set -euo pipefail
D=/var/backups/ppf; mkdir -p "$D"
S=$(date +%F)
mysqldump -u ppf -p"$DB_PASS" ppf_scholership | gzip > "$D/db-$S.sql.gz"
find "$D" -mtime +30 -delete
SH
chmod +x /usr/local/bin/ppf-backup
```

Run it nightly via cron, and enable DigitalOcean's Droplet backups as a second
layer. Copies that live only on the same Droplet are not backups — push them to
Spaces or another host.

---

## Deploying an update

```bash
cd /var/www/peoples-scholar
git pull
cd app/scholarship
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
systemctl reload php8.3-fpm
```

Always `optimize:clear` before re-caching — a stale config cache will keep
serving the previous `.env`.

---

## Post-deploy checklist

```bash
curl -I https://your-domain/.env                 # 404
curl -I https://your-domain/scholarship/.env     # 404
curl -sI https://your-domain/ | head -1          # 200
curl -s https://your-domain/up                   # framework health check
```

- [ ] Web root is `app/scholarship/public`
- [ ] `APP_DEBUG=false` and `APP_ENV=production`
- [ ] `.env` is `chmod 640`, owned by deploy, and not in git
- [ ] Zip archives and the SQL dump deleted from the server
- [ ] Admin login works; the sidebar counts render
- [ ] Submit a test application end to end, with a photo
- [ ] The uploaded photo displays afterwards
- [ ] Category dropdown loads courses (the admin AJAX path)
- [ ] Backups run and restore

---

## Known items worth scheduling

Not blockers, but they are real:

- **Change the admin password.** The current hash dates from 2018.
- **Mixed table collations.** `district` and `unit` are `latin1_swedish_ci`
  while the rest are `utf8mb3`/`utf8mb4`. Joins are on integer ids so nothing
  breaks today, but non-ASCII place names in those two tables risk mojibake.
  Converting them is a data migration to do deliberately, with a backup.
- **Single admin account.** There is no user management UI and registration is
  deliberately absent; new accounts must be inserted manually.
- **No automated tests.** Only Laravel's example stubs exist, so every change
  currently needs manual verification.
- **Dead views retained.** `layouts/app.blade.php`,
  `admin/settings.blade.php`, `admin/meetings/view-application.blade.php` and
  the two modals it includes are unreachable and reference routes that do not
  exist. They are harmless while nothing renders them, but they will fail if
  ever wired up.
