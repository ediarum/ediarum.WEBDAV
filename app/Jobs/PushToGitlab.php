<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class PushToGitlab implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Project $project
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Pushing to gitlab");
        $dataRepo = $this->project->data_folder_location;

        $auth = $this->project->gitlab_username . ":" . $this->project->gitlab_personal_access_token . "@";
        $url = $this->project->gitlab_url;

        $url = Str::replace("https://", "https://$auth", $url);

        Log::info("Here is the url again $url");

        $command = "cd $dataRepo && git push $url";
        Log::info("Pushing now: $command");

        $result = Process::timeout(10)->run($command);

        if($result->failed()){
            Log::info("Push failed:" . $result->errorOutput());
            throw new \Exception("Push to gitlab failed: " . $result->errorOutput());
        }
    }
}
