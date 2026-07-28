# Deploying Peoples Scholar

Laravel 13 · PHP 8.3+ · MySQL/MariaDB · Blade + AdminLTE 2 (server-rendered)

---

## Which host

**DigitalOcean.** Netlify cannot run this application at all — it serves static
sites and JS/Go/Rust serverless functions, with no PHP runtime, no persistent
MySQL connection model, and no persistent disk for uploads. This app is
server-rendered PHP with file-based sessions and a 70MB uploads directory, so
the mismatch is absolute rather than a matter of preference.

Two DigitalOcean options:

| | Droplet (recommended) | App Platform |
|---|---|---|
| PHP version | you choose | needs a custom Dockerfile |
| Uploads | persistent disk, works as-is | ephemeral — must move to Spaces first |
| Cost | from $6/mo | from ~$5/mo + managed DB |
| Ops | you patch the OS | managed |

The Droplet is the better fit: uploads land on local disk today, and moving
them to object storage is a code change you do not need in order to ship.

A **$12/mo Droplet (2GB RAM)** plus **daily backups ($2.40/mo)** is a sensible
starting point. 1GB works but leaves little headroom for MySQL alongside
PHP-FPM.

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
```

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

Copy the uploaded applicant photos across (they are not in git — 70MB of
personal data), then cache everything:

```bash
rsync -av storage/uploads/ deploy@your-droplet:/var/www/peoples-scholar/app/scholarship/storage/uploads/

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

The uploads directory and the database are the irreplaceable parts.

```bash
cat > /usr/local/bin/ppf-backup <<'SH'
#!/bin/bash
set -euo pipefail
D=/var/backups/ppf; mkdir -p "$D"
S=$(date +%F)
mysqldump -u ppf -p"$DB_PASS" ppf_scholership | gzip > "$D/db-$S.sql.gz"
tar czf "$D/uploads-$S.tar.gz" -C /var/www/peoples-scholar/app/scholarship/storage uploads
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
