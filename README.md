# Ediarum Webdav

## Description

This is an application allows users to edit files over a WevDAV connection and tracks those changes via automatic git commits.

It includes the following features:
* User Management (Create Users, Manage Passwords, Add to Projects)
* Configure a WevDAV connection to multiple different Projects 
* When files are edited, the user can push those files to an exist-db 
* When files are edited, the user can push those files to Ediarum.Backend (soon to be released).


## Local Development

### Requirements:
* `php8.1`
* a mysql or sqlite database connection, 
* The application works with `node 20`

### Installation

* `cp .env.example .env`. Then set up the database connection by modifying the `.env`
* Install php dependencies with `composer install`
* Install frontend dependencies with `npm install`
* Start laravel server: `php artisan serve`
* Compile frontend assets `npm run serve`

### Setting up the Datase

If starting from an empty database:
`php artisan migrate:install`
`php artisan migrate`

Make sure you have set the initial user, email, and password in the `.env` file. Then run:
`php artisan db:seed`

## Usage

* Go to `localhost:8000`, and click register to set up an account.
* Once you have an account, use the email and password you entered to log in to the webdav, located at the `webdav` subroot, so for example: localhost:8000/webdav

## Deployment
One can use the docker files as examples if one wants to do a docker deployment, but it is easier and preferable to not use docker.

On the server, you need to specify the php version, so:
`php8.1 /usr/local/bin/composer install`
`php8.1 artisan migrate:install` etc.

### And the server specific commands:
`npm run build`
Generate a key: `php8.1 artisan key:generate`

### Pushing to Gitlab

If one sets up a project with Gitlab remote url, Gitlab username, and Gitlab Personal Access Token (PAT), then all new commits are automatically pushed to gitlab.

