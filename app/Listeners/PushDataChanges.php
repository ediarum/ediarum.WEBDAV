<?php

namespace App\Listeners;

use App\Events\DataChange;
use App\Jobs\PushToEdiarumBackend;
use App\Jobs\PushToExistDb;

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
            PushToEdiarumBackend::dispatch(
                $event->project,
                $event->webDavEvent,
                $event->sourcePath,
                $event->destinationPath,
            );
        }
        if ($project->exist_base_url
            && $project->exist_data_path
            && $project->exist_username
//            && $project->exist_password
        ) {
            PushToExistDb::dispatch(
                $event->project,
                $event->webDavEvent,
                $event->sourcePath,
                $event->destinationPath,
            )->onQueue('exist-db');
        }

    }
}
