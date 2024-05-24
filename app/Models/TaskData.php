<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskData extends Model
{
    use HasFactory;
    protected $table = 'task_data';

    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany(Task::class)->with('subtasks');
    }

    public function associatedCase()
    {
        return $this->belongsTo(Cases::class, 'cases_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

      public function taskLogs()
    {
        return $this->hasMany(TaskLog::class, 'task_id', 'id')->with('user');
    }


    public function taskDataLogs()
    {
        return TaskLog::whereIn('task_id', $this->tasks()->pluck('id'))->get();
    }

    public function subTaskDataLogs()
    {
        return SubTaskLog::whereIn('task_id', $this->tasks()->pluck('id'))->get();
    }

}
