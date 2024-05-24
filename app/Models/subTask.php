<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subTask extends Model
{
    use HasFactory;
    protected $table = 'sub_tasks';
    protected $guarded = [];

    public function subtaskLogs()
    {
        return $this->hasMany(SubTaskLog::class, 'subtask_id'); 
    }

    
    
}
