<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\FailedJob;
use App\Models\Lock;
use App\Models\Project;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('projects.index', [
            'projects' => Project::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.edit', [
            "project" => new Project(),
            "new" => true,

        ]);
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {

        $validated = $request->validated();
        $project = new Project();
        $project->fill($validated);
        $project->save();
        return redirect()->route("projects.show", ["project" => $project->id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $p = Project::with('users')->findOrFail($id);

        $users = User::whereDoesntHave('projects',
            function (Builder $q) use ($id) {
                $q->where('project_id', $id);
            })
            ->orderBy("name")
            ->get();

        $gitlab = $p->gitlab_url && $p->gitlab_username && $p->gitlab_personal_access_token;
        $ediarum = $p->ediarum_backend_url && $p->ediarum_backend_api_key;
        $exist = $p->exist_base_url && $p->exist_data_path && $p->exist_username && $p->exist_password;
        $locks = Lock::where('owner', 'like', "$id:%")
            ->orderBy('created')
            ->get()
            ->map(function ($l) {
                $lock['id'] = $l->id;
                $lock['created'] = Carbon::createFromTimestampUTC($l->created)->setTimezone(config('app.timezone'));
                $lock['timeElapsed'] = CarbonInterval::seconds(time() - $l->created)->cascade()->forHumans();
                $lock['owner'] = trim(substr($l->owner, strpos($l->owner, ":") + 1));
                $lock['file'] = $l->uri;
                return $lock;

            });

        $failedJobs = FailedJob::all()
            ->filter(function ($job) use ($p) {
                if($job->queue !== "exist-db"){
                    return false;
                }
                $payload = json_decode($job->payload);
                $data = unserialize( $payload->data->command );
                return $data->project->id == $p->id;
            })->map(function ($job) {
                $payload = json_decode($job->payload);
                $data = unserialize( $payload->data->command );
                return [
                    "uuid" => $payload->uuid,
                    "file" => $data->sourcePath,
                    "time" => $job->failed_at,
                ];
            });



        return view('projects.show', [
            "project" => $p,
            "users" => $users,
            "gitlab_push" => $gitlab,
            "ediarum_push" => $ediarum,
            "exist_push" => $exist,
            "locks" => $locks,
            "failed_jobs" => $failedJobs,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('projects.edit', [
            "project" => Project::findOrFail($id),
            "new" => false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        $validated = $request->validated();
        $project = Project::findOrFail($id);
        $project->fill($validated);
        $project->save();
        return redirect()->route("projects.show", ["project" => $project->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function removeLock(int $projectId, int $lockId)
    {
        $lock = Lock::find($lockId);
        $lock->delete();
        return redirect()->route("projects.show", ["project" => $projectId]);

    }
}
