# MediCare — Agent Instructions

## Stack
- **Backend**: PHP 8 (custom MVC, not Laravel/Symfony)
- **Frontend**: Vanilla JS + fetch API, SPA with modal dialogs
- **Database**: MySQL (PDO)
- **Server**: Apache via XAMPP

## Running the App (XAMPP)

1. Start XAMPP Control Panel → Start **Apache** and **MySQL**
2. Import database: Open `http://localhost/phpmyadmin` → Import → Select `sql/medicare.sql`
3. Access the app: **http://localhost/medapp2/public/**

## Key Paths
- **Entry point**: `public/index.php`
- **Base URL**: `$baseUrl = '/medapp2/public'` (CRITICAL - do NOT use `/medapp2/medapp2/public`)
- **Database config**: `config/database.php`
- **Database schema**: `sql/medicare.sql`
- **Core classes**: `app/Core/` (Router, Controller, Model, Database, Validator)
- **Controllers**: `app/Controllers/`
- **Models**: `app/Models/`
- **Views**: `app/Views/` (server-side PHP templates)
- **JS modules**: `public/js/modules/` (SPA functionality)
- **CSS**: `public/css/style.css`

## Database Credentials
- Host: `localhost`
- Name: `medicare`
- User: `root`
- Password: (empty)

## Architecture Notes
- `public/index.php` defines ALL routes (web + API) — specific routes must be registered BEFORE parameterized routes
- Routes: `/api/rendezvous/stats` must come BEFORE `/api/rendezvous/{id}` to avoid 404
- Controllers handle both server-rendered pages (`index()`, `create()`, `edit()`) and JSON APIs (`apiIndex()`, `apiStore()`, etc.)
- **View files**: Must set `$baseUrl = '/medapp2/public'` at the top
- Modals are used for create/edit forms (not separate pages)
- No Composer, npm, or build system — plain PHP files

## Common Issues
- **404 on API**: Route ordering - specific routes before parameterized routes
- **404 on navigation**: Check `$baseUrl` in view files - should be `/medapp2/public`
- **Modal not centered**: CSS uses correct `#modal-overlay` and `#modal` selectors with z-index: 9999
- **CORS errors**: Access via `http://localhost/...`, not `file://`
- **"Connexion DB échouée"**: Verify MySQL is running in XAMPP

## UI Interactions
- "Ajouter" buttons open modals via JS `onclick="ModuleName.openNew()"`
- "Annuler" buttons close modals via `onclick="Modal.close()"`
- Forms are submitted via AJAX, not page navigation