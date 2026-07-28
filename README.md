# Peoples Scholar

Scholarship application and grant-management system for Peoples Foundation.
Applicants submit online; administrators verify, place applications on meetings,
grant amounts, and track instalments.

**Stack:** Laravel 13 · PHP 8.3+ · MySQL/MariaDB · Blade + AdminLTE 2 (Bootstrap 3)

Deployment instructions live in [DEPLOYMENT.md](DEPLOYMENT.md).

---

## Layout

```
app/
├─ index.php, css/, js/, images/, …   legacy cPanel web root — superseded,
│                                     see the docroot note below
└─ scholarship/                       the Laravel application
   ├─ app/
   │  ├─ Http/Controllers/            public + Admin/ controllers
   │  ├─ Support/AdminLte/            in-repo replacement for the abandoned
   │  │                               jeroennoten/laravel-adminlte package
   │  ├─ Support/Html/                in-repo replacement for laravelcollective/html
   │  └─ *.php                        Eloquent models (App\ root namespace)
   ├─ resources/views/                Blade templates
   │  └─ vendor/adminlte/             the AdminLTE 2 theme layouts
   ├─ public/                         ← the real web root
   └─ storage/uploads/                applicant photos (not in git)
```

### Web root

Serve **`app/scholarship/public`**. The older layout served the whole `app/`
directory, which exposed `.env`, the source tree and backup archives over HTTP.
The top-level `app/index.php` and its duplicated asset folders are the remains
of that setup and are not used when the docroot is set correctly.

## Local development

Requires PHP 8.3+, Composer, and MySQL/MariaDB.

```bash
cd app/scholarship
composer install
cp .env.example .env
php artisan key:generate
```

Point `.env` at your database, then load the schema and data from the SQL dump
(`artisan migrate` will **not** build the schema — the two migrations in the
repo do not describe the real tables):

```bash
mysql -u root -p your_database < ../../ppf_scholership.sql
php artisan serve
```

Work against a copy of the data rather than the live database:

```bash
mysqldump -u root -p ppf_scholership | mysql -u root -p ppf_dev
```

## Things worth knowing before changing anything

- **Models live in `App\`**, not `App\Models\` (`App\Application`, `App\Person`).
- **Uploads** go to `storage/uploads/` — a custom directory, not
  `storage/app/public` — and are served through `FileController`, which
  constrains the filename to a single path segment. Keep that constraint.
- **The sidebar** is built in `AppServiceProvider::registerAdminMenu()`, which
  listens for `App\Events\BuildingMenu`. Items declared in
  `config/adminlte.php` render first.
- **`Form::select` / `Form::radio`** in the views resolve to
  `App\Support\Html\FormBuilder`, which reproduces the old package's markup
  exactly — including a quirk that emits `<option value="1" >` with a trailing
  space. Do not "tidy" that without re-checking the rendered output.
- **Pagination is pinned to Bootstrap 3** in `AppServiceProvider`. The
  framework default is Tailwind, which renders unstyled against AdminLTE 2.
- **`route()` parameter names must match the route definition.** Laravel used
  to fill mismatched names positionally; it now throws. Several views relied on
  that and have been corrected.
- **Registration and password reset do not exist.** The single admin account is
  managed directly in the database.

## Useful commands

```bash
php artisan route:list                  # all routes
php artisan optimize:clear              # drop cached config/routes/views
php artisan test                        # only example stubs today
./vendor/bin/pint                       # code style
```
