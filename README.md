# ediarum.WEBDAV

## Description

This is an application allows users to edit files over a WevDAV connection and tracks those changes via automatic git commits.

It includes the following features:
* User Management (Create Users, Manage Passwords, Add to Projects)
* Configure a WevDAV connection to multiple different Projects 
* When files are edited, the user can push those files to an exist-db 
* When files are edited, the user can push those files to Ediarum.Backend (soon to be released).


## Local Development with Docker

## Local Development without Docker

### Requirements:
* `php8.1`
* a mysql database connection, 
* The application works with `node 20`

### Installation

* `cp .env.example .env`. Then set up the database connection by modifying the `.env`
* Install php dependencies with `composer install`
* Install frontend dependencies with `npm install`
* Start laravel server: `php artisan serve`
* Start the queue: `php artisan queue:listen --queue=default,gitlab,ediarum-backend,exist-db`
* Compile frontend assets `npm run serve`

### Setting up the Database

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
Make sure to set up the queue with supervisor. The Supervisor file looks something like this:
```
[program:ediarum-webdav-laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php8.1 path_to_ediarum.webdav/artisan queue:work --queue=default,gitlab,ediarum-backend,exist-db
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
stdout_logfile=/path_to_ediarum.webdav/storage/logs/queue.log
stopwaitsecs=3600
```

Make sure to set up a cronjob for the scheduler:
```
php8.1 /path_to_ediarum.webdav/artisan schedule:run >> /dev/null 2>&1 
```

Finally, we are experimenting with pruning failed jobs. If we decide to do this, this will be integrated into the schedule.
At the moment it has to be manually run:
```
php8.1 /path_to_ediarum.webdav/artisan queue:prune-failed
```

### And the server specific commands:
`npm run build`
Generate a key: `php8.1 artisan key:generate`


### Proxying

If you want users to access the webdav service via a different url, you can use the following nginx configuration to set up a reverse proxy:
```
location /your/desired/base/webdav/uri {
	proxy_pass https://actualwebaddress.com/webdav/;
    proxy_set_header X-WebDAV-Proxy-Root "/your/desired/base/webdav/uri";

}
```
Now, users can access the webdav service via `https://somedomain.com/your/desired/base/webdav/uri`, although it is proxied to
`https://actualwebaddress.com/webdav/`.

Middleware picks up the `X-WebDAV-Proxy-Root` header and modifies the Webdav Requests and Responses as needed.

### Pushing to Gitlab

If one sets up a project with Gitlab remote url, Gitlab username, and Gitlab Personal Access Token (PAT), then all new commits are automatically pushed to gitlab.

## License

ediarum.WEBDAV is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

ediarum.WEBDAV is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with ediarum.DB If not, see http://www.gnu.org/licenses/.
