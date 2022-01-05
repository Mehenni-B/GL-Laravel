<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'duration', 'project_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function previous()
    {
        return $this->hasMany(Previous::class, 'task_id', 'id');
    }
}
