<?php

namespace App\Helpers;

use DOMDocument;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ExistDBResourceNotFound;

class ExistDbClient
{
    private PendingRequest $httpClient;

    public function __construct(string $existBaseUrl, string $existDbUser, ?string $existDbPassword)
    {
        $this->httpClient = Http::withBasicAuth($existDbUser, $existDbPassword ?? '')
            ->baseUrl(rtrim($existBaseUrl, " /"));
    }


    /**
     * @throws ConnectionException
     */
    private function getUrl($url)
    {
        Log::debug("Getting $url.");
        $response = $this->httpClient->get($url);
        if ($response->notFound()) {
            throw new ExistDBResourceNotFound("Resource not found at " . $response->effectiveUri());
        }
        if ($response->failed()) {
            throw new \Exception(
                "Response  to " .
                $response->effectiveUri() .
                " failed with error code: " .
                $response->status() .
                " and message: ' {$response->body()}'");
        }
        return $response;
    }

    public function getDirectory($path): FolderContents
    {
        $trimmed = trim($path, ' /');
        $existDBFolder = new FolderContents();
        try {
            $response = $this->getUrl("rest/$trimmed");
            $existDBFolder->parseExistDBCollectionContents($response->body());
        } catch (ExistDBResourceNotFound $e) {
            Log::debug("Resource not found at $path");
        }
        return $existDBFolder;

    }

    public function deleteResource($path)
    {
        $trimmed = trim($path, ' /');
        $response = $this->httpClient->delete("rest/$trimmed");
        if ($response->failed()) {
            if ($response->status() == 404) {
                throw new ExistDBResourceNotFound("Resource not found at " . $response->effectiveUri());
            } else {
                throw new \Exception(
                    "Response  to " .
                    $response->effectiveUri() .
                    " failed with error code: " .
                    $response->status() .
                    " and message: ' {$response->body()}'");
            }
        }
    }

    public function createResource($existPath, $filePath)
    {
        $trimmed = trim($existPath, ' /');
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $contentType = match ($extension) {
            'xml' => 'application/xml',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream',
        };

        $content = File::get($filePath);
        Log::debug("Here ist he file path $filePath");
        Log::debug("Here is the contentType $contentType");

        Log::debug("Folder contents: $content.");

        $response = $this->httpClient->withOptions(['debug'=>true])
            ->withBody($content, $contentType)
            ->put("rest/$trimmed");

        if ($response->failed()) {

            throw new \Exception(
                "Response to " .
                $response->effectiveUri() .
                " failed with error code: " .
                $response->status() .
                " and message: '{$response->body()}'"
            );
        }
    }


}
