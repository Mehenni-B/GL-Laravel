<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Previous extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'task_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
