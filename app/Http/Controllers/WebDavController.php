<?php

namespace App\Http\Controllers;

use App\Events\DataChange;
use App\Models\Project;
use App\Sabre\Sapi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sabre\DAV;

/*
 *
 * Adapted from: https://github.com/monicahq/laravel-sabre/blob/main/src/Sabre/Server.php
 *
 * */

class WebDavController extends Controller
{
    //
    public function __invoke(Request $request)
    {
//        Log::info($request->getMethod() . ": " . $request->fullUrl());
//        Log::info("Authorization header: " . $request->header('Authorizatjon'));
        $project = Project::where('slug', $request->projectSlug)->firstOrFail();

        if (!Gate::allows('edit-project-files', $project->id)) {
            abort(403);
        }


        $rootDirectory = new DAV\FS\Directory($project->data_folder_location);
        $server = new DAV\Server($rootDirectory);
        $server->debugExceptions = true;

        $sapi = new Sapi();
        $server->sapi = $sapi;

        $server->setBaseUri(env("APP_SUBPATH") . '/webdav/' . $request->projectSlug);
        $server->setLogger(Log::getLogger());

        //Not sure if this is necessary...
//        $query = $request->getQueryString();
//        $url = Str::finish($request->getPathInfo(), '/');
//
//        $fullPath = is_null($query) ? $url : $url.'?'.$query;
//
//        $server->httpRequest->setUrl($fullPath);

        //Note sure why errors are not getting logged...
        if (!Storage::disk('local')->exists('webdav-locks')) {
            Storage::disk('local')->makeDirectory('webdav-locks');
        }
        $pdo = DB::getPdo();
        $lockBackend = new DAV\Locks\Backend\PDO($pdo);
        $lockPlugin = new DAV\Locks\Plugin($lockBackend);

        $server->addPlugin($lockPlugin);

        $server->addPlugin(new DAV\Browser\Plugin());

        $server->on('beforeLock', function ($path, \Sabre\DAV\Locks\LockInfo $lock) use ($request) {
            $lock->owner = $request->user()->email;
        });
        $server->on('beforeUnLock', function ($path, \Sabre\DAV\Locks\LockInfo $lock) use ($request) {
            $lock->owner = $request->user()->email;
        });

        $events = ['afterCreateFile', 'afterWriteContent', 'afterUnbind', 'afterMove',];
        foreach ($events as $event) {
            $server->on($event, function ($path, $destinationPath = null) use ($event, $project) {
                if ($event == "afterMove") {
                    DataChange::dispatch(Auth::user()->email, $project, $event, $path, $destinationPath);
                } else {
                    DataChange::dispatch(Auth::user()->email, $project, $event, $path);
                }
            });
        }
        $server->start();

        // Transform to Laravel response
        /** @var resource|string|null */
        $body = $server->httpResponse->getBody();
        $status = $server->httpResponse->getStatus();
        $headers = $server->httpResponse->getHeaders();

        if (is_null($body) || is_string($body)) {
            return response($body, $status, $headers);
        }

        $contentLength = $server->httpResponse->getHeader('Content-Length');

        return response()->stream(function () use ($body, $contentLength): void {
            if (is_numeric($contentLength) || (!is_null($contentLength) && ctype_digit($contentLength))) {
                echo stream_get_contents($body, intval($contentLength));
            } else {
                echo stream_get_contents($body);
            }
        }, $status, $headers);

    }
}
