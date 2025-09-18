# Development

## Local Development with Docker

## Local Development without Docker

### Requirements

* `php8.1`
* A mysql database connection
* The application works with `node 20`

### Installation

* `cp .env.example .env`. Then set up the database connection by modifying the `.env`.
* Install php dependencies with `composer install`
* Install frontend dependencies with `npm install`
* Start laravel server: `php artisan serve`
* Start the queue: `php artisan queue:listen --queue=default,gitlab,ediarum-backend,exist-db`
* Compile frontend assets `npm run serve`

### Setting up the Database

If starting from an empty database:

* `php artisan migrate:install`
* `php artisan migrate`

Make sure you have set the initial user, email, and password in the `.env` file. Then run:

* `php artisan db:seed`

## Usage

* For usage see [USAGE.md](USAGE.md).
