<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $projects = $user->projects;
        foreach ($projects as $project) {
            foreach ($project->tasks as $task) {
                $task->previous;
            }
        }
        return response(['message' => 'All projects', 'projects' => $projects], 200);
    }

    public function project(Request $request, Project $project)
    {
        $newProject = new Project();
        $newProject->verifyUserProject($request, $project);
        foreach ($project->tasks as $task) {
            $task->previous;
        }
        return response(['message' => 'Project', 'project' => $project], 200);
    }

    public function create(ProjectRequest $request)
    {
        $newProject = new Project();
        $validation = $newProject->verifyPrevious($request);
        if ($validation)
            return $validation;

        $project = DB::transaction(function () use ($request, $newProject) {
            $project = Project::create([
                'name' => $request->name,
                'user_id' => $request->user()->id
            ]);
            $newProject->createTasks($request, $project);
            return $project;
        });

        if ($project) {
            foreach ($project->tasks as $task) {
                $task->previous;
            }
            return response(['message' => 'Project created successfully', 'project' => $project], 201);
        } else
            return response(['message' => 'Project not created successfully'], 500);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $newProject = new Project();
        $newProject->verifyUserProject($request, $project);
        $validation = $newProject->verifyPrevious($request);
        if ($validation)
            return $validation;

        $response = DB::transaction(function () use ($request, $project, $newProject) {
            $newProject->deleteTasks($project);
            $project->update(['name' => $request->name]);
            $newProject->createTasks($request, $project);
            return $project;
        });

        if ($response) {
            $project = Project::find($project->id);
            foreach ($project->tasks as $task) {
                $task->previous;
            }
            return response(['message' => 'Project updated successfully', 'project' => $project], 200);
        } else
            return response(['message' => 'Project not updated successfully'], 500);
    }

    public function delete(Request $request,  Project $project)
    {
        $newProject = new Project();
        $newProject->verifyUserProject($request, $project);
        $response = DB::transaction(function () use ($request, $project, $newProject) {
            $newProject->deleteTasks($project);
            $project->delete();
            return 'success';
        });
        if ($response)
            return response(['message' => 'Project deleted successfully'], 200);
        else
            return response(['message' => 'Project not deleted successfully'], 500);
    }
}
