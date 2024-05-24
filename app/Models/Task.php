<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];
  

 

    public function taskData()
    {
        return $this->belongsTo(TaskData::class);
    }

    public function taskLog()
    {
        return $this->hasMany(TaskLog::class, 'task_id', 'id');
    }
    public function SubtaskLog()
    {
        return $this->hasMany(TaskLog::class, 'subtask_id', 'id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'tasks', 'task_team');
    }

    public function subtasks()
    {
        return $this->hasMany(subTask::class);
    }

    public function taskLogs()
    {
        return $this->hasMany(TaskLog::class);
    }

    public function subtaskLogs()
    {
        return $this->hasMany(SubtaskLog::class);
    }


   

    
}
