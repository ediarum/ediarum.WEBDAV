<?php

namespace App\Http\Controllers;

use App\Helpers\ExistDbClient;
use App\Models\Project;
use App\Services\ExistDBSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ProjectServicesController extends Controller
{
    public function enableMaintenanceMode(Request $request, $projectId){
        $locks =
            DB::table("locks_$projectId");

        if($locks->count() > 0){
            return redirect()->route("projects.show", ["project" => $projectId])
                ->withErrors(["Cannot enable maintenance mode while there are active locks. Please release all locks first."]);
        }
        $project = Project::findOrFail($projectId);
        $project->is_in_maintenance_mode = true;
        $project->save();

        return redirect()->route("projects.show", ["project" => $project->id]);
    }

    public function disableMaintenanceMode(Request $request, $projectId){
        $project = Project::findOrFail($projectId);
        $project->is_in_maintenance_mode = false;
        $project->save();

        return redirect()->route("projects.show", ["project" => $project->id]);
    }
    public function pushFilesToExistdb(Request $request, $projectId)
    {

        $folder= $request['folder'] ?? null;

        $project = Project::findOrFail($projectId);

        $service = new ExistDbSyncService(
            new ExistDbClient($project->exist_base_url, $project->exist_username, $project->exist_password),
            $project->data_folder_location,
            $project->exist_data_path
        );

        set_time_limit(0);
        ignore_user_abort(true);

        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null) {
                echo "event: app-error\n";
                echo "data: [FATAL ERROR] {$error['message']} in {$error['file']}:{$error['line']}\n\n";
                @ob_flush();
                @flush();
            }
        });

        return response()->stream(function () use ($service, $folder) {
            try {

                $service->syncFolder($folder, function ($message) {
                    echo "data:  {$message}\n\n"; // SSE format
                    ob_flush();
                    flush();
                });
                echo "data: ExistDBSyncIsComplete! \n\n";
                ob_flush();
                flush();

                echo "data: [DONE]\n\n";
                ob_flush();
                flush();
            } catch (\Exception $e) {
                echo "event: app-error\n";
                echo "data: Error syncing project: {$e->getMessage()}\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection'   => 'keep-alive',
        ]);
    }
}
