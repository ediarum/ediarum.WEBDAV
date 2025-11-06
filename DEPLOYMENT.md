
# Deployment

## 1. Install PHP Version

On the server, you need to specify the php version, so:
`php8.3 /usr/local/bin/composer install`
`php8.3 artisan migrate:install` etc.

## 2. Install Nginx

The application must be served over nginx or apache.  The file `nginx.conf` shows how to serve the application from a subpath.

## 3. Add a queue for pushing to eXist-db

Pushes to an *eXist-db* instance are handled via a queue.

Set up the queue with supervisor. The Supervisor file looks something like this:

```text
[program:ediarum-webdav-laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php8.3 path_to_ediarum.webdav/artisan queue:work --queue=default,gitlab,ediarum-backend,exist-db
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
stdout_logfile=/path_to_ediarum.webdav/storage/logs/queue.log
stopwaitsecs=3600
```

We are experimenting with pruning failed jobs. If we decide to do this, this will be integrated into the schedule.
At the moment it has to be manually run:

```bash
php8.3 /path_to_ediarum.webdav/artisan queue:prune-failed
```

## 4. Add a Laravel schedule for WebDAV locking system

The WebDAV locking system requires a scheduler to prevent WebDAV Locks from expiring.

Make sure to set up a cronjob for the scheduler:

```bash
php8.3 /path_to_ediarum.webdav/artisan schedule:run >> /dev/null 2>&1 
```

## 5. Run server specific commands

* Build the frontend assetss: `npm run build`
* Generate a Laravel application key: `php8.3 artisan key:generate`

## 6. Add proxying

If you want users to access the webdav service via a different url, you can use the following nginx configuration to set up a reverse proxy:

```text
location /your/desired/base/webdav/uri {
    proxy_pass https://actualwebaddress.com/webdav/;
    proxy_set_header X-WebDAV-Proxy-Root "/your/desired/base/webdav/uri";

}
```

Now, users can access the webdav service via `https://somedomain.com/your/desired/base/webdav/uri`, although it is proxied to
`https://actualwebaddress.com/webdav/`.

Middleware picks up the `X-WebDAV-Proxy-Root` header and modifies the Webdav Requests and Responses as needed.

## 7. Add pushing to Gitlab

If one sets up a project with GitLab remote url, GitLab username, and Gitlab Personal Access Token (PAT), then all new commits are automatically pushed to gitlab.

## Usage

* For usage see [USAGE.md](USAGE.md).
