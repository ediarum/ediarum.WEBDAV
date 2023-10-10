<?php

namespace App\Jobs;

use App\Events\DataChange;
use App\Exceptions\HttpResponse;
use App\Exceptions\UnknownWebDavHook;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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

    private PendingRequest $httpClient;
    private string $url;
    /**
     * Create a new job instance.
     */
    public function __construct(
        public DataChange $change
    )
    {
        $this->url = $this->change->project->ediarum_backend_url;
        $this->httpClient = Http::withToken($this->change->project->ediarum_backend_api_key);
    }
    private function handleRes(Response $res)
    {
        if ($res->failed()) {
            $status = $res->status();
            $body = $res->body();
            throw new HttpResponse("Request to Ediarum Backend failed with status $status and response $body");
        }
    }

    private function createFile($path)
    {
        $res = $this->httpClient->post($this->url, ['path' => $path]);
        $this->handleRes($res);
    }

    private function editFile($path)
    {
        $res = $this->httpClient->put($this->url, ['path' => $path]);
        $this->handleRes($res);
    }

    private function movePath($source, $destination)
    {
        $res = $this->httpClient->put($this->url . "move", [
            'source_path' => $source,
            'destination_path' => $destination,
        ]);
        $this->handleRes($res);
    }

    private function deletePath($path)
    {
        $res = $this->httpClient->delete($this->url, ['path' => $path]);
        $this->handleRes($res);
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {

            switch ($this->change->webDavEvent) {
                case "afterCreateFile":
                    $this->createFile($this->change->sourcePath);
                    break;
                case "afterWriteContent":
                    $this->editFile($this->change->sourcePath);
                    break;
                case "afterMove":
                    $this->movePath($this->change->sourcePath, $this->change->destinationPath);
                    break;
                case "afterUnbind":
                    $this->deletePath($this->change->sourcePath);
                    break;
                default:
                    throw new UnknownWebDavHook($this->change->webDavEvent);
            }

    }
}
