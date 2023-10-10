<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectUserController extends Controller
{
    public function attach(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $project->users()->attach($request->user_id);
        return redirect()->route("projects.show", ["project" => $request->project_id]);
    }

    public function detach(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $project->users()->detach($request->user_id);
        return redirect()->route("projects.show", ["project" => $request->project_id]);
    }
}
