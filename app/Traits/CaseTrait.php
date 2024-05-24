<?php

namespace App\Traits;
use Illuminate\Support\Facades\Auth;
use App\Models\Cases;
use App\Models\Utility;

trait CaseTrait
{
    public function isAssignedCase($caseId)
    {
        $user = Auth::user();
        if(!$this->isCompanyCase($caseId, $user->creatorId())){
            return false;
        }
        if ($this->isAllowedToViewCases($user)) {
            return true;
        } else {
            $userId = $user->id;

            if ($this->isUserAssignedToCase($caseId, $userId)) {
                return true;
            }
        }

        return false;
    }

    protected function isAllowedToViewCases($user)
    {
        return in_array($user->type, ['company', 'co admin']) ||
            ($user->type !== 'client' && Utility::getValByName('viewCases') === 'all');
    }

    protected function isUserAssignedToCase($caseId, $userId)
    {
        $case = Cases::find($caseId);
        $team = $case->your_team ?? '';
        $advocates = $case->your_advocates ?? '';

        return in_array($userId, explode(',', $team)) || in_array($userId, explode(',', $advocates));
    }
    protected function isCompanyCase($caseId, $userId)
    {
        $case = Cases::withTrashed()->find($caseId);
        if ($case && $case->created_by === intval($userId)) {
            return true;
        }
        return false;
    }
}