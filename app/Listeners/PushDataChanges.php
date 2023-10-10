<?php

namespace App\Listeners;

use App\Events\DataChange;
use App\Jobs\PushToEdiarumBackend;
use App\Jobs\PushToExistDb;
use App\Jobs\PushToGitlab;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class PushDataChanges
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(DataChange $event): void
    {
        $project = $event->project;
        if ($project->ediarum_backend_url && $project->ediarum_backend_api_key) {
            PushToEdiarumBackend::dispatch($project
            )->onQueue('ediarum-backend');
        }
        if ($project->exist_base_url
            && $project->exist_data_path
            && $project->exist_username
            && $project->exist_password) {
            PushToExistDb::dispatch(
                $event->project,
                $event->webDavEvent,
                $event->sourcePath,
                $event->destinationPath,
            )->onQueue('exist-db');
        }

    }
}
