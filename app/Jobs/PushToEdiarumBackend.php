<?php

namespace App\Jobs;

use App\Exceptions\HttpResponse;
use App\Exceptions\UnknownWebDavHook;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushToEdiarumBackend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $url;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Project $project,
        public string  $webDavEvent,
        public string  $sourcePath,
        public ?string $destinationPath = null,
    )
    {
        $this->url = $this->project->ediarum_backend_url;
    }

    private function handleRes(Response $res)
    {
        if ($res->failed()) {
            $status = $res->status();
            $body = $res->body();
            throw new HttpResponse("Request to Ediarum Backend failed with status $status and response $body");
        } else {
            Log::debug("Request to " . $res->effectiveUri() . "successful: " . $res->body());
        }
    }

    private function pushFile($path)
    {
        $absolute_path = $this->project->data_folder_location . "/" . $path;
        $res = Http::withToken($this->project->ediarum_backend_api_key)
            ->attach('file', file_get_contents($absolute_path), $path)
            ->attach('path', $path)
            ->post($this->url . "/files");

        $this->handleRes($res);
    }

    //TODO: Implement this
    private function movePath($source, $destination)
    {
    }

    private function deletePath($path)
    {

        $res = Http::withToken($this->project->ediarum_backend_api_key)
            ->delete($this->url . "/files", ['path' => $path]);
        $this->handleRes($res);
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->webDavEvent) {
            case "afterCreateFile":
                $this->pushFile($this->sourcePath);
                break;
            case "afterWriteContent":
                $this->pushFile($this->sourcePath);
                break;
            case "afterMove":
                $this->movePath($this->sourcePath, $this->destinationPath);
                break;
            case "afterUnbind":
                $this->deletePath($this->sourcePath);
                break;
            default:
                throw new UnknownWebDavHook($this->webDavEvent);
        }

    }
}
