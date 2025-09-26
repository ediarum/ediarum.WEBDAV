# Development

## Local Development with Docker

## Local Development without Docker

### Requirements

* **PHP 8.1**
  * Check php version with `php -v`.
* **A running MySQL database connection.** If you having problems to install mysql you can use a docker container. For a local installation see the following:
  1. Check MySQL Version with `mysql --version`.
  2. Check if MySQL is running `sudo systemctl status mysql`. If not, start MySQL with `sudo systemctl start mysql`.
  3. Check if you can connect to MySQL: `mysql -u root -p` with user `root`. For [creating users](https://dev.mysql.com/doc/refman/8.4/en/create-user.html) and [setting password](https://dev.mysql.com/doc/refman/8.4/en/set-password.html) please read the [MySQL Reference Manual](https://dev.mysql.com/doc/refman/8.4/en/)
* **Node.js Version 20**
  * Check node version with `node --version`.

### Installation

1. Run `cp .env.example .env` to copy default settings.
2. Set up the database connection by modifying `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in the `.env` file.
3. Run `composer install` to install php dependencies.
4. Run `npm install` to install frontend dependencies.
5. Run `php artisan serve` to start the laravel server.
6. If you are using the synchronizing jobs to GitLab, ediarum.BACKEND or eXist-db, please start the queue with `php artisan queue:listen --queue=default,gitlab,ediarum-backend,exist-db`.
7. Run `npm run serve` to compile frontend assets.

### Setting up the Database

If starting from an empty database:

1. Create the database in MySQL:
   1. Run `mysql -u root -p` to login to the database via command tool:
   2. Run `CREATE DATABASE ediarum_webdav;`. ediarum.WEBDAV is using the database specified as `DB_DATABASE` in `.env`, e.g. `ediarum_webdav`.
2. Run `php artisan migrate:install` to create a migrations table in the database.
3. Run `php artisan migrate` to execute the migrations from `/database/migrations` for setting up the database.
4. Set the `INITIAL_USER_NAME`, `INITIAL_USER_EMAIL`, `INITIAL_USER_PASSWORD` in the `.env` file.
5. Run `php artisan db:seed` to add the initial data to the database.
6. Run `php artisan key:generate` to generate an `APP_KEY` and update the `.env` file.

#### Inspect the database

With `mysql -u root -p` you can login the database via command tool:

* `SHOW DATABASES;` Shows the current databases.
* To check or edit the database run `USE ediarum_webdav`. Then run `SHOW TABLES;` to list the tables:

```txt
+--------------------------+
| Tables_in_ediarum_webdav |
+--------------------------+
| failed_jobs              |
| jobs                     |
| migrations               |
| password_reset_tokens    |
| personal_access_tokens   |
| project_user             |
| projects                 |
| users                    |
+--------------------------+
```

After adding a project with `ID` there should be a new table named `locks_ID` for tracking the webdav locks for this project, e.g.:

```txt
+--------------------------+
| Tables_in_ediarum_webdav |
+--------------------------+
| failed_jobs              |
| jobs                     |
| locks_1                  |
| migrations               |
| password_reset_tokens    |
| personal_access_tokens   |
| project_user             |
| projects                 |
| users                    |
+--------------------------+
```

## Usage

* For usage see [USAGE.md](USAGE.md).
