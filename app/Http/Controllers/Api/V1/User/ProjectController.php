<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Previous;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $projects = $user->projects;
        foreach ($projects as $project) {
            $project->tasks;
            foreach ($project->tasks as $task) {
                $task->previous;
            }
        }
        return response(['message' => 'All projects', 'projects' => $projects], 200);
    }

    public function project(Request $request, Project $project)
    {
        $user = $request->user();
        $projects = $user->projects;
        if (!$projects->contains($project))
            return response(['message' => 'Project not created successfully'], 422);

        $project->tasks;
        foreach ($project->tasks as $task) {
            $task->previous;
        }
        return response(['message' => 'Project', 'project' => $project], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3',
            'tasks' => 'required|array',
            'tasks.*.name' => 'required|string|max:255',
            'tasks.*.duration' => 'required|integer|min:1',
            'tasks.*.previous' => 'array',
            'tasks.*.previous.*' => 'string|max:255',
        ]);
        if ($validator->fails())
            return response(['errors' => $validator->errors()->all()], 422);

        foreach ($request->tasks as $task) {
            if (array_key_exists('previous', $task))
                if (in_array($task['name'], $task['previous']))
                    return response(['task' => $task['name'], 'errors' => 'The task cannot be previous for herself'], 422);
        }

        $project = DB::transaction(function () use ($request) {
            $project = Project::create([
                'name' => $request->name,
                'user_id' => $request->user()->id
            ]);
            foreach ($request->tasks as $task) {
                $newTask = Task::create([
                    'name' => $task['name'],
                    'duration' => $task['duration'],
                    'project_id' => $project->id
                ]);
                if (array_key_exists('previous', $task)) {
                    $taskPrevious = array_unique($task['previous']);
                    foreach ($taskPrevious as $previous) {
                        Previous::create([
                            'name' => $previous,
                            'task_id' => $newTask->id
                        ]);
                    }
                }
            }
            return $project;
        });

        if ($project) {
            $project->tasks;
            foreach ($project->tasks as $task) {
                $task->previous;
            }
            return response(['message' => 'Project created successfully', 'project' => $project], 201);
        } else
            return response(['message' => 'Project not created successfully'], 500);
    }

    public function update(Request $request, Project $project)
    {
        return $project;
    }

    public function delete(Request $request,  Project $project)
    {
        return $project;
    }
}
