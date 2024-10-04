<?php

namespace App\Listeners;

use App\Events\DataChange;
use App\Jobs\PushToGitlab;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class MakeGitCommit
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DataChange $event): void
    {
        // The git commit is taken care of by the delete event when files are moved.
        if ($event->webDavEvent == "afterMove") {
            return;
        }
        Log::info("Making git commit from {$event->user} for {$event->sourcePath}.");

        $dataRepo = $event->project->data_folder_location;

        $command = "cd $dataRepo &&git add . ";

        $command .= " && git -c 'user.name=telotawebdev' -c 'user.email=none@bbaw.de'";
        $command .= " commit -m 'Webdav Change: $event->sourcePath'";

        $result = Process::run($command);


        if ($result->successful()
            && $event->project->gitlab_url
            && $event->project->gitlab_username
            && $event->project->gitlab_personal_access_token
        ) {
            PushToGitlab::dispatch($event->project)->onQueue('gitlab');
        }

        if ($result->failed()) {
            Log::error("Git command failed. Error Output: "
                . $result->errorOutput()
                . ".Standard output: {$result->output()}");
        }


        //
    }
}
