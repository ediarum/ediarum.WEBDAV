<?php

namespace App\Jobs;

use App\Events\DataChange;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushToExistDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    private PendingRequest $httpClient;

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
    }

    private function handleRes(Response $res)
    {
        if ($res->failed()) {
            $url = $res->effectiveUri();
            $status = $res->status();
            $body = $res->body();
            throw new HttpResponse("Request to $url with status $status and response $body.");
        }
    }

    private function putFile(PendingRequest $httpClient, $path)
    {

        $dataFolder = $this->project->data_folder_location;

        $content = File::get(realpath($dataFolder) . "/" . $path);
        $res = $httpClient
            ->withOptions(['debug' => true])
            ->withBody($content, "application/xml")
            ->put($path);
        $this->handleRes($res);
    }

    private function deletePath(PendingRequest $httpClient, $path)
    {
        $res = $httpClient
            ->withOptions(['debug' => true])
            ->delete($path);
        $this->handleRes($res);
    }

    private function putMovedFiles(PendingRequest $httpClient, $path)
    {
        $dataFolder = realpath($this->project->data_folder_location);
        $newNode = realpath($dataFolder) . "/" . $path;

        if (File::isFile($newNode)) {
            Log::debug("Moving single file: $path");
            $this->putFile($httpClient, $path);
            return;
        }

        $files = File::allFiles($newNode);

        foreach ($files as $f) {
            Log::debug("Move file from directory move $path: $f");
            $relativePath = substr($f->getRealPath(), strlen($dataFolder));
            Log::debug("Here is the file to push: $relativePath");
            $this->putFile($httpClient, $relativePath);

        }


    }

    /**
     * Execute the job.
     */
    public
    function handle(): void
    {
        $baseUrl = rtrim($this->project->exist_base_url, "/");
        $dataPath = trim($this->project->exist_data_path, "/");
        $url = implode("/", [$baseUrl, "rest", $dataPath]);
        $httpClient = Http::withBasicAuth($this->project->exist_username, $this->project->exist_password)
            ->baseUrl($url);

        switch ($this->webDavEvent) {
            case "afterCreateFile":
                Log::debug("Request to create File: {$this->sourcePath}");
            case "afterWriteContent":
                Log::debug("Request to write content: {$this->sourcePath}");
                $this->putFile($httpClient, $this->sourcePath);
                break;
            case "afterMove":
                Log::debug("Detected move from {$this->sourcePath} to {$this->destinationPath}.");
                Log::debug("Creating {$this->destinationPath}");
                $this->putMovedFiles($httpClient, $this->destinationPath);
                break;
            case "afterUnbind":
                Log::debug("Request to delete: {$this->sourcePath}");
                $this->deletePath($httpClient, $this->sourcePath);
                break;
            default:
                throw new UnknownWebDavHook($this->webDavEvent);
        }

    }
}
