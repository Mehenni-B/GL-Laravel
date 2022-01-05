<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'user_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    public function verifyPrevious(Request $request)
    {
        foreach ($request->tasks as $task) {
            if (array_key_exists('previous', $task))
                if (in_array($task['name'], $task['previous']))
                    return response(['task' => $task['name'], 'errors' => 'The task cannot be previous for herself'], 422);
        }
    }

    public function verifyUserProject(Request $request, Project $project)
    {
        $user = $request->user();
        $projects = $user->projects;
        if (!$projects->contains($project))
            return response(['message' => 'Project not belong to this user'], 422);
    }

    public function createTasks(Request $request, Project $project)
    {
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
    }

    public function deleteTasks(Project $project)
    {
        foreach ($project->tasks as $task) {
            if ($task->previous)
                foreach ($task->previous as $previous) {
                    $previous->delete();
                }
            $task->delete();
        }
    }
}
