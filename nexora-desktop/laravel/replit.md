# Gran Fornecedores

A Laravel 12 + Filament 4.5 supplier/product management system.

## Overview

This is a web application for managing suppliers, products, and clients. It includes:
- Main frontend interface with navigation for Products, Suppliers, and Clients
- Filament admin panel at `/admin` for backend management
- SQLite database for data storage

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Admin Panel**: Filament 4.5
- **Frontend Assets**: Vite + Tailwind CSS
- **Database**: SQLite

## Project Structure

```
app/
  Http/Controllers/      # Controllers for Clients, Products, Suppliers
  Models/                # Eloquent models (Client, Product, Supplier, User)
  Providers/             # Service providers including Filament panel
config/                  # Laravel configuration files
database/
  migrations/            # Database migrations
  seeders/               # Database seeders
public/                  # Public assets including Filament JS/CSS
resources/               # Blade views, CSS, JS
routes/                  # Route definitions
storage/                 # Storage for sessions, cache, logs
```

## Running Locally

The application runs on port 5000:
```bash
php artisan serve --host=0.0.0.0 --port=5000
```

## Database

The application uses SQLite stored at `database/database.sqlite`. Migrations are automatically applied during setup.

## Admin Panel

Access the Filament admin panel at `/admin`. You'll need to create a user account to log in.
