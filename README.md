# Profile Perpustakaan Jawa Tengah

A CodeIgniter 3 MVC web application for the provincial library profile website of Jawa Tengah. This project combines a public-facing content management system, an admin dashboard, and a survey/reporting module for library data collection and analytics.

## Overview

This application is designed to support:

- Public website content such as news, albums, videos, downloads, static pages, and contact forms
- Administrative content management and module permissions
- Survey data collection with validation, reporting, export, and chart-based statistics

For a deeper technical description of the architecture and modules, see [project-overview.md](project-overview.md).

## Technology Stack

- PHP (CodeIgniter 3 compatible)
- CodeIgniter 3.1.13
- MySQL / MariaDB
- Apache via XAMPP/WAMP/LAMP
- HTML, CSS, JavaScript, Bootstrap, jQuery, DataTables, and Chart.js

## Project Structure

- `application/controllers/` — MVC controllers for public pages and admin actions
- `application/models/` — database access and business query logic
- `application/views/` — templates for the public site and admin panel
- `application/config/` — routing, database, and application configuration
- `asset/` — static assets, CSS, JavaScript, images, and third-party libraries
- `system/` — CodeIgniter framework core files

## Getting Started

### Prerequisites

- PHP 7.4 or newer (the current workspace is using PHP 8.1.25)
- Apache and MySQL via XAMPP/WAMP/LAMP
- Composer (optional, for framework dependency management)

### Local Setup

1. Place the project in your local web server document root, for example:
   - `C:\xampp\htdocs\profile.perpus.jatengprov.go.id`
2. Start Apache and MySQL in XAMPP.
3. Create a MySQL database named `testdatabaseperpus`.
4. Update the database connection settings in `application/config/database.php` if needed.
5. Open the project in your browser:
   - `http://localhost/profile.perpus.jatengprov.go.id`

### Optional Composer Step

If dependencies are not installed yet, run:

```bash
composer install
```

## Main Modules

### Public CMS
- News and categories
- Albums and galleries
- Video playlists
- Downloads and static pages
- Polling and contact handling

### Admin Panel
- CRUD management for content modules
- User and permission management
- Survey configuration and reporting tools

### Survey / Pendataan Module
- Dynamic form generation
- Validation and security checks
- Submission storage and reporting
- Export and chart-based statistics

## Development Notes

- The default front controller is `index.php`.
- The application uses CodeIgniter’s standard MVC structure.
- The project is suitable for local development, testing, and maintenance within an Apache + MySQL environment.

## License

This project uses the CodeIgniter framework license as defined in `composer.json`.
