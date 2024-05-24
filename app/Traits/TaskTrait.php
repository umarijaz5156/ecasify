<?php

namespace App\Traits;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskData;
use App\Models\Utility;

trait TaskTrait
{
    public function isAssignedTask($taskId)
    {
        $user = Auth::user();
        $userId = $user->id;
        if($this->isAllowedToViewTask()) {
            // can access all tasks
            return true;
        }
        
        if ($this->isAssignedTaskCreatedByUser($taskId, $userId)) {
            // Users can access tasks they created
            return true;
        }

        if ($this->isAssignedTaskByCase($taskId, $userId)) {
            // Users can access tasks associated with cases they are part of
            return true;
        }

        return false;
    }

    protected function isAllowedToViewTask()
    {
        $user = Auth::user();
        return in_array($user->type, ['company', 'co admin']) ||
            ($user->type !== 'client' && Utility::getValByName('viewTasks') === 'all');
    }

    protected function isAssignedTaskCreatedByUser($taskId, $userId)
    {
        return TaskData::where('id', $taskId)
            ->where('created_by', $userId)
            ->exists();
    }

    protected function isAssignedTaskByCase($taskId, $userId)
    {
                return TaskData::where('id', $taskId)
                ->where(function ($query) use ($userId) {
                    $query->whereHas('associatedCase', function ($caseQuery) use ($userId) {
                        $caseQuery->whereRaw("(find_in_set('" . $userId . "', your_team) OR find_in_set('" . $userId . "', your_advocates))");
                    })
                    ->orWhere('created_by', $userId);
                })
                ->exists();

    }
}
