# WordPress Plugin Portfolio Demo

This repository is a small WordPress learning project focused on custom plugin development with PHP.

It uses Docker Compose to run WordPress, MySQL, and phpMyAdmin locally, then mounts the custom plugin from this repo into the WordPress container.

## What it demonstrates

- WordPress plugin structure
- Admin menu creation with `add_menu_page`
- Settings registration and sanitization
- Public shortcode rendering with `[dev_profile]`
- Frontend asset registration/enqueueing
- Escaping output with `esc_html`, `esc_url`, and `esc_textarea`
- Local WordPress development with Docker Compose

## Requirements

- Docker Desktop with Docker Compose

Docker Desktop is installed on this machine. If an older terminal cannot find `docker`, open a new PowerShell window or use Docker's full CLI path:

```powershell
& 'C:\Program Files\Docker\Docker\resources\bin\docker.exe' compose up -d
```

## Start

```powershell
cd C:\Users\pod_h\Desarrollo\wordpress
Copy-Item .env.example .env
docker compose up -d
```

Open WordPress:

```text
http://localhost:8080
```

Open phpMyAdmin:

```text
http://localhost:8081
```

## Database

The local database settings are copied from `.env.example` into `.env`.

```text
Database: wordpress
User: wordpress
Password: wordpress
Host: db
```

phpMyAdmin uses:

```text
User: root
Password: wordpress_root
```

## Stop

```powershell
docker compose down
```

To delete the local database and WordPress runtime volume, run:

```powershell
docker compose down -v
```

## Plugin

The custom plugin lives in:

```text
src/wp-content/plugins/wp-dev-portfolio
```

After WordPress starts:

1. Open `http://localhost:8080/wp-admin`.
2. Finish the WordPress installer if needed.
3. Go to **Plugins** and activate **WP Dev Portfolio**.
4. Go to **Dev Profile** in the admin menu and edit the profile fields.
5. Create a page that contains this shortcode:

```text
[dev_profile]
```

That page will render the public developer profile card using the plugin data.
