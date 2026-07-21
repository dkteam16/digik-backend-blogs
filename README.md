# DigiK Backend Blogs

A Laravel 10 blog backend with an admin panel and REST API, built with Sanctum token-based authentication.

## Requirements

- PHP ^8.1
- Composer
- MySQL
- Node.js & NPM (for frontend assets)

## Installation

```bash
git clone <repo-url>
cd digik-backend-blogs
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials, then:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
```

## Running the App

```bash
php artisan serve
```

- Admin panel: [http://localhost:8000/admin](http://localhost:8000/admin)
- API base URL: `http://localhost:8000/api`
- API docs: [http://localhost:8000/api-docs](http://localhost:8000/api-docs)

## Testing

```bash
php artisan test
```

## Tech Stack

- **Framework:** Laravel 10
- **Auth:** Laravel Sanctum (API tokens), Laravel UI (web sessions)
- **Image handling:** Intervention Image
- **Assets:** Vite
- **Testing:** PHPUnit 10
