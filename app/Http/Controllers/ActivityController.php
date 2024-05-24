<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Cases;
use App\Models\TaskData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    //


    public function index(){

        $today = Carbon::today();

        if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {


            $tab = 'all';
            $company_id = Auth::user()->creatorId();
            $documentActivities = Activity::whereNotNull('file')
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();

            $caseActivities = Activity::where('target_type', 'Case')
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();

            $taskActivities = Activity::where('target_type', 'Task')
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();

            $userActivities = Activity::where('target_type', 'User')
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();

            $allActivities = Activity::where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get(); // Adjust the number as needed
            
            $timerActivities = Activity::where('target_type', 'Timer')
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'desc')
            ->get();

            // login details
            $objUser = Auth::user();
         
                $loginUsersDetails = DB::table('login_details')
                    ->join('users', 'login_details.user_id', '=', 'users.id')
                    ->select(DB::raw('login_details.*, users.name as user_name , users.email as user_email'))
                    ->where(['login_details.created_by' => $objUser->id])->orderBy('created_at', 'desc');

            $loginUsersDetails = $loginUsersDetails->get();

        } else {
            $user = Auth::user()->id;

            $tab = 'all';
            // $caseActivities = [];
            // $documentActivities = [];
            // $taskActivities = [];
            // $userActivities = [];
            // $allActivities = [];
            // $loginUsersDetails = [];
            // $timerActivities= [];
                $user_id = Auth::user()->id;

                $documentActivities = Activity::whereNotNull('file')
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $caseActivities = Activity::where('target_type', 'Case')
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $taskActivities = Activity::where('target_type', 'Task')
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $userActivities = Activity::where('target_type', 'User')
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $allActivities = Activity::where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get(); // Adjust the number as needed

                $timerActivities = Activity::where('target_type', 'Timer')
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Login details
                $objUser = Auth::user();

                $loginUsersDetails = DB::table('login_details')
                    ->join('users', 'login_details.user_id', '=', 'users.id')
                    ->select(DB::raw('login_details.*, users.name as user_name, users.email as user_email'))
                    ->where(['login_details.user_id' => $user_id])
                    ->orderBy('created_at', 'desc')
                    ->get();


        }

    return view('activity.index', compact('tab','timerActivities','loginUsersDetails','allActivities','userActivities' ,'taskActivities' , 'caseActivities' ,'documentActivities'));

        // return view('activity.index',compact('tab','documentActivities'));
    }

}
