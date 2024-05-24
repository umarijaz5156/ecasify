<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Cases;
use App\Models\Document;
use App\Models\Hearing;
use App\Models\Order;
use App\Models\Plan;
use App\Models\TaskData;
use App\Models\DashboardWidgetPosition;
use App\Models\ToDo;
use App\Models\User;
use App\Models\Utility;
use App\Models\Activity;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;



class DashboardController extends Controller
{
    public function index()
    {





        if (Auth::check()) {
            if (Auth::user()->can('show dashboard')) {

                Artisan::call('optimize:clear');

                $hearings = Hearing::where('created_by', Auth::user()->creatorId())->orderBy('date', 'ASC')->get();
                $all = Hearing::where('created_by', Auth::user()->creatorId())->orderBy('date', 'ASC')->pluck('case_id')->toArray();
                // $cases = Cases::where('created_by',Auth::user()->creatorId())->whereIn('id',$all)->get();
                $today = Carbon::today();


                if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {


                    $cases = Cases::where('created_by', Auth::user()->creatorId())
                        ->where('draft', 0)
                        ->orderBy('created_at', 'desc')
                        ->get();


                        $PieGraphcases = DB::table("cases")
                        ->selectRaw("practice_area as area")
                        ->selectRaw("COUNT(*) as count")
                        ->where('draft', 0)
                        ->where('created_by', Auth::user()->creatorId())
                        ->whereDate('open_date', now())
                        ->groupBy('area') // Group by practice_area
                        ->get();
                       
                        
                    // Transform the data into the structure expected by ApexCharts
                    $piechartData = [];
                    foreach ($PieGraphcases as $case) {
                        $piechartData[$case->area] = $case->count;
                    }
    
                    $Graphcases = DB::table("cases")
                    ->selectRaw("practice_area as category")
                    ->selectRaw("COUNT(*) as value")
                    ->where('created_by', Auth::user()->creatorId())
                    ->where('draft', 0)
                    ->whereDate('open_date', now())
                    ->groupBy('category') 
                    ->get();
    
                $chartData = [];
                foreach ($Graphcases as $case) {
                    $chartData[] = [
                        'category' => $case->category,
                        'value' => $case->value,
                    ];
                }

                $lineGraphCases = DB::table("cases")
                    ->selectRaw("practice_area as category")
                    ->selectRaw("YEAR(open_date) as year")
                    ->selectRaw("MONTH(open_date) as month")
                    ->selectRaw("COUNT(*) as value")
                    ->where('created_by', Auth::user()->creatorId())
                    ->where('draft', 0)
                    ->groupBy('category', 'year', 'month', 'open_date')
                    ->get();

                $lineChartData = [];
                foreach ($lineGraphCases as $case) {
                    $lineChartData[] = [
                        'category' => $case->category,
                        'year' => $case->year,
                        'month' => $case->month,
                        'value' => $case->value,
                    ];
                }


            

                

                    // $Graphcases = Cases::selectRaw("DATE_FORMAT(open_date, '%M') as month")
                    //     ->selectRaw("practice_area as area")
                    //     ->selectRaw("COUNT(*) as count")
                    //     ->groupBy('month', 'area')
                    //     ->where('created_by', Auth::user()->creatorId())
                    //     ->where('draft', 0)
                    //     ->get();

                    // $chartData = $Graphcases->groupBy('area')->map(function ($areaCases) {
                    //     return $areaCases->groupBy('month')->map(function ($monthCases) {
                    //         return $monthCases->sum('count');
                    //     });
                    // });


                    $draft_cases = Cases::where('created_by', Auth::user()->creatorId())
                        ->where('draft', 1)
                        ->orderBy('created_at', 'desc')
                        ->count();

                    $upcoming_case = Cases::where('created_by', Auth::user()->creatorId())
                        ->whereDate('open_date', '>=', $today)
                        ->where('draft', 0)
                        ->orderBy('open_date')
                        ->limit(5)
                        ->get();

                    $todos = TaskData::with(['associatedCase', 'createdByUser'])->where('created_by', Auth::user()->creatorId())
                        ->where('priority', 'High')
                        ->orderBy('created_at', 'desc')
                        ->get();

                        $totalTodos = TaskData::count();

                    

                    $upcoming_todo = TaskData::with(['associatedCase', 'createdByUser'])
                        ->where('created_by', Auth::user()->creatorId())
                        ->whereDate('date', '>=', $today)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

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
                        ->paginate(10); // Adjust the number as needed

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


                    // timer time login

                    $entries = TimeSheet::whereDate('created_at', today())->get();
                    $totalTimes = [];  
                    foreach ($entries as $entry) {
                        $user_id = $entry->member; // Assuming 'user_id' is the correct field
                        $time = $entry->time;
                    
                        if (preg_match('/(\d+m\s\d+s|\d+h\s\d+m\s\d+s)/', $time, $matches)) {
                            $timeParts = preg_split('/\s/', $matches[0]);
                            $minutes = 0;
                            $seconds = 0;
                    
                            foreach ($timeParts as $part) {
                                if (strpos($part, 'm') !== false) {
                                    $minutes = intval($part);
                                } elseif (strpos($part, 's') !== false) {
                                    $seconds = intval($part);
                                }
                            }
                    
                            $totalTimeInSeconds = $minutes * 60 + $seconds;
                        } else {
                            $timeParts = explode(":", $time);
                            $hours = intval($timeParts[0] ?? 0);
                            $minutes = intval($timeParts[1] ?? 0);
                            $seconds = intval($timeParts[2] ?? 0);
                            $totalTimeInSeconds = $hours * 3600 + $minutes * 60 + $seconds;
                        }
                    
                        if (!isset($totalTimes[$user_id])) {
                            $totalTimes[$user_id] = 0;
                        }
                    
                        $totalTimes[$user_id] += $totalTimeInSeconds;
                    }
                    // Find the user with the maximum total time
                    $maxUser = null;
                    $maxTime = 0;

                    foreach ($totalTimes as $user_id => $totalTime) {
                        if ($totalTime > $maxTime) {
                            $maxUser = User::find($user_id);
                            $maxTime = $totalTime;
                        }
                    }
                    
                    // Display the result
                    $maxLoggedMember = $maxUser ? $maxUser->name : 'No user found';
                    $maxLoggedTime = floor($maxTime / 3600) . "h " . floor(($maxTime % 3600) / 60) . "m " . ($maxTime % 60) . "s";
                    
                } else {
                    $user = Auth::user()->id;

                    $tab = 'all';
                    $caseActivities = [];
                    $documentActivities = [];
                    $taskActivities = [];
                    $userActivities = [];
                    
                    $loginUsersDetails = [];
                    $timerActivities= [];
                    $lineChartData = [];
                    

                    $allActivities = Activity::where('user_id', $user)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10); 


                    $cases = DB::table("cases")
                        ->select("cases.*")
                        ->where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                                ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                        })
                        ->where('draft', 0)
                        ->orderBy('id', 'DESC')
                        ->get();

                  
                   

                    $PieGraphcases = DB::table("cases")
                    ->selectRaw("practice_area as area")
                    ->selectRaw("COUNT(*) as count")
                    ->where(function ($query) use ($user) {
                        $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                            ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                    })
                    ->where('draft', 0)
                    ->groupBy('area') // Group by practice_area
                    ->get();
                
                    
                // Transform the data into the structure expected by ApexCharts
                $piechartData = [];
                foreach ($PieGraphcases as $case) {
                    $piechartData[$case->area] = $case->count;
                }

                $Graphcases = DB::table("cases")
                ->selectRaw("practice_area as category")
                ->selectRaw("COUNT(*) as value")
                ->where(function ($query) use ($user) {
                    $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                        ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                })
                ->where('draft', 0)
                ->groupBy('category') 
                ->get();

            $chartData = [];
            foreach ($Graphcases as $case) {
                $chartData[] = [
                    'category' => $case->category,
                    'value' => $case->value,
                ];
            }


                    $draft_cases = DB::table("cases")
                        ->select("cases.*")
                        ->where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                                ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                        })
                        ->where('draft', 1)
                        ->orderBy('id', 'DESC')
                        ->count();


                    $upcoming_case = DB::table("cases")
                        ->where('created_by', Auth::user()->created_by)
                        ->where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                                ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                        })
                        ->where('draft', 0)
                        ->whereDate('open_date', '>=', $today)
                        ->orderBy('open_date')
                        ->limit(5)
                        ->get();


                    $todos = TaskData::with(['associatedCase', 'createdByUser'])
                        ->where('created_by', Auth::user()->created_by)
                        ->where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', (select your_team from cases where id = task_data.cases_id))")
                                ->orWhereRaw("find_in_set('" . $user . "', (select your_advocates from cases where id = task_data.cases_id))");
                        })
                        ->where('priority', 'High')
                        ->orderBy('created_at', 'desc')
                        ->get();

                        $totalTodos = TaskData::where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', (select your_team from cases where id = task_data.cases_id))")
                                ->orWhereRaw("find_in_set('" . $user . "', (select your_advocates from cases where id = task_data.cases_id))")
                                ->orWhereRaw("find_in_set('" . $user . "', task_team)");
                        })
                        ->count();
                    

                       


                    $upcoming_todo = TaskData::with(['associatedCase', 'createdByUser'])
                        ->where('created_by', Auth::user()->created_by)
                        ->where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', (select your_team from cases where id = task_data.cases_id))")
                                ->orWhereRaw("find_in_set('" . $user . "', (select your_advocates from cases where id = task_data.cases_id))");
                        })
                        ->whereDate('date', '>=', $today)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();


                        // login details for login user

                        $entries = TimeSheet::where('member', $user)->get(); // Retrieve entries for the authenticated user
                        $totalTimes = [];
                        $totalTimeInSeconds = 0;

                        foreach ($entries as $entry) {
                            $time = $entry->time;
                            $member = $entry->member;

                            // Parse the time in the format "Xm Ys"
                                if (preg_match('/(\d+m\s\d+s|\d+h\s\d+m\s\d+s)/', $time, $matches)) {

                                    
                                    $timeParts = preg_split('/\s/', $matches[0]);
                                    $minutes = 0;
                                    $seconds = 0;
                            
                                    foreach ($timeParts as $part) {
                                        if (strpos($part, 'm') !== false) {
                                            $minutes = intval($part);
                                        } elseif (strpos($part, 's') !== false) {
                                            $seconds = intval($part);
                                        }
                                    }
                            
                                    $totalTimeInSeconds = $minutes * 60 + $seconds;

                            } else {
                              
                                // Parse the time in the format "XXh:YYm:ZZs"
                                $timeParts = explode(":", $time);
                                $hours = intval($timeParts[0] ?? 0);
                                $minutes = intval($timeParts[1] ?? 0);
                                $seconds = intval($timeParts[2] ?? 0);
                                $totalTimeInSeconds += $hours * 3600 + $minutes * 60 + $seconds;
                            }

                            if (!isset($totalTimes[$member])) {
                                $totalTimes[$member] = 0;
                            }
                            
                            $totalTimes[$member] += $totalTimeInSeconds;
                        }

                        $maxMember = null;
                        $maxTime = 0;
                        // dd($member);

                        foreach ($totalTimes as $member => $totalTime) {
                            if ($totalTime > $maxTime) {
                                $maxMember = $member;
                                $maxTime = $totalTime;
                            }
                        }
                        // dd($maxMember);

                        // Display the result
                        $maxLoggedMember = User::where('id', $user)->pluck('name')->first();
                        $maxLoggedTime =  floor($maxTime / 3600) . "h " . floor(($maxTime % 3600) / 60) . "m " . ($maxTime % 60) . "s";
    
                }


                                
                $attorneys = Advocate::where('created_by', Auth::user()->creatorId())->get();
                $clients = User::where('type', 'client')->where('created_by', Auth::user()->creatorId())->get();
                $staffMembers = User::where(function ($query) {
                    $query->where('type', 'team')
                        ->orWhere('type', 'co admin');
                })
                    ->where('created_by', Auth::user()->creatorId())
                    ->get();
                // $todos = ToDo::where('created_by', Auth::user()->creatorId())->orderBy('start_date','ASC')->get();
                $docs = Document::where('created_by', Auth::user()->creatorId())->get();

                $todayTodos = [];
                // $total_diary_data = count(DiaryController::index(request())->todos) + count(DiaryController::index(request())->cases);

                $total_diary_data = 0;
                $todayHear = Hearing::where('created_by', Auth::user()->creatorId())->where('date', date('Y-m-d'))->get();
                $hearings = Hearing::where('created_by', Auth::user()->creatorId())->where('date', date('Y-m-d'))->pluck('case_id')->toArray();
                $todatCases = Cases::where('created_by', Auth::user()->creatorId())->whereIn('id', $hearings)->get();

                $users = User::find(\Auth::user()->creatorId());
                $plan = Plan::find($users->plan);

                if ($plan->storage_limit > 0) {
                    $storage_limit = ($users->storage_limit / $plan->storage_limit) * 100;
                } else {
                    $storage_limit = 0;
                }
                // widgets Positions
                $widgetsPositions = DashboardWidgetPosition::where('user_id', Auth::user()->id)->pluck('position', 'widget_name')
                ->toArray();
                return view('dashboard', compact('tab','lineChartData','piechartData','totalTodos' , 'widgetsPositions', 'chartData', 'maxLoggedTime', 'maxLoggedMember', 'timerActivities', 'loginUsersDetails', 'allActivities', 'userActivities', 'taskActivities', 'caseActivities', 'documentActivities', 'upcoming_case', 'draft_cases', 'cases', 'upcoming_todo', 'attorneys', 'staffMembers', 'clients', 'todos', 'total_diary_data', 'docs', 'todatCases', 'todayTodos', 'users', 'plan', 'storage_limit', 'todayHear'));
            } elseif (Auth::user()->can('manage super admin dashboard')) {

                $user = Auth::user();
                $user['total_user'] = User::where('type', '=', 'company')->where('created_by', Auth::user()->creatorId())->count();
                $user['total_paid_user'] = $user->countPaidCompany();
                $user['total_orders'] = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan'] = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->total : 0);
                $chartData = $this->getOrderChart(['duration' => 'week']);

                return view('admin_dash', compact('user', 'chartData'));
            } else {

                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            // if (!file_exists(storage_path() . "/installed")) {
            //     header('location:install');
            //     die;
            // } else {
            $settings = Utility::settings();
            return redirect('login');

            // }

        }
    }



    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_week = strtotime("-2 week +1 day");
                for ($i = 0; $i < 14; $i++) {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask = [];
        $arrTask['label'] = [];
        $arrTask['data'] = [];
        foreach ($arrDuration as $date => $label) {

            $data = Order::select(DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][] = $data->total;
        }

        return $arrTask;
    }

    // updateWidgetPosition
    public function updateWidgetPositions(Request $request)
    {
        $positionsToSave = $request->all();
        // Ensure that the request is properly validated before proceeding.
        // You should validate the data, check authentication, etc.
    
        // For each item in $positionsToSave, update the positions in the database.
        foreach ($positionsToSave as $positionData) {
            $widgetName = $positionData['widget_name'];
            $newPosition = $positionData['position'];
            $dashboardType = $positionData['dashboard_type'] ?? 'company';
    
            // Check if a row with the same user_id, widget_name, and dashboard_type exists
            $widgetPosition = DashboardWidgetPosition::where('user_id', Auth::user()->id)
                ->where('widget_name', $widgetName)
                ->where('dashboard_type', $dashboardType)
                ->first();
            if ($widgetPosition) {
                // If the row exists, update the position
                $widgetPosition->position = $newPosition;
                $widgetPosition->save();
            } else {
                // If the row doesn't exist, create a new one
                $widgetPosition = new DashboardWidgetPosition;
                $widgetPosition->user_id = Auth::user()->id;
                $widgetPosition->widget_name = $widgetName;
                $widgetPosition->dashboard_type = $dashboardType;
                $widgetPosition->position = $newPosition;
                $widgetPosition->save();
            }
            
        }
    
        return response()->json(['success' => __('Widget positions updated successfully')]);
    }

    public function getTimesheetData($startDate = null, $endDate = null)
    {
        $currentTime = now();
        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        
        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        if ($startDate && $endDate) {
            // If start and end dates are provided, use them
            $entries = TimeSheet::whereBetween('created_at', [$startDate, $endDate])->get();
        } elseif ($startDate) {
            // If only start date is provided, use it
            $entries = TimeSheet::whereDate('created_at', $startDate)->get();
        } elseif ($endDate) {
            // If only end date is provided, use it
            $entries = TimeSheet::whereDate('created_at', $endDate)->get();
        } else {
            // Default case: fetch all entries
            $entries = TimeSheet::all();
        }

    
        $totalTimes = [];

        foreach ($entries as $entry) {
            $user_id = $entry->member;
            $time = $entry->time;
        
            // Extract minutes and seconds from the time format "Xm Ys"
            preg_match('/(\d+)m (\d+)s/', $time, $matches);
        
            $minutes = isset($matches[1]) ? (int)$matches[1] : 0;
            $seconds = isset($matches[2]) ? (int)$matches[2] : 0;
        
            // Calculate total time in seconds
            $totalTimeInSeconds = $minutes * 60 + $seconds;
        
            if (!isset($totalTimes[$user_id])) {
                $totalTimes[$user_id] = 0;
            }
        
            $totalTimes[$user_id] += $totalTimeInSeconds;
        }
        
        // Find the user with the maximum total time
        $maxUser = null;
        $maxTime = 0;
        
        foreach ($totalTimes as $user_id => $totalTime) {
            if ($totalTime > $maxTime) {
                $maxUser = User::find($user_id);
                $maxTime = $totalTime;
            }
        }
        
        // Display the result
        $maxLoggedMember = $maxUser ? $maxUser->name : 'No user found';
        $maxLoggedTime = floor($maxTime / 3600) . "h " . floor(($maxTime % 3600) / 60) . "m " . ($maxTime % 60) . "s";
        
        return response()->json([
            'maxLoggedMember' => $maxLoggedMember,
            'maxLoggedTime' => $maxLoggedTime,
        ]);
    }

    public function updateGraph($startDate = null, $endDate = null)
    {
        // Initialize start and end dates
        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
    
        $pieGraphCases = DB::table("cases")
            ->selectRaw("practice_area as area")
            ->selectRaw("COUNT(*) as count")
            ->where('draft', 0)
            ->where('created_by', Auth::user()->creatorId())
            ->whereBetween('open_date', [$startDate, $endDate]) // Filter by date range
            ->groupBy('area')
            ->get();

            
        $piechartData = [];
        foreach ($pieGraphCases as $case) {
            $piechartData[$case->area] = $case->count;
        }
    
        $graphCases = DB::table("cases")
            ->selectRaw("practice_area as category")
            ->selectRaw("COUNT(*) as value")
            ->where('created_by', Auth::user()->creatorId())
            ->where('draft', 0)
            ->whereBetween('open_date', [$startDate, $endDate]) // Filter by date range
            ->groupBy('category')
            ->get();
    
        $chartData = [];
        foreach ($graphCases as $case) {
            $chartData[] = [
                'category' => $case->category,
                'value' => $case->value,
            ];
        }
    
        $lineGraphCases = DB::table("cases")
        ->selectRaw("practice_area as category")
        ->selectRaw("YEAR(open_date) as year")
        ->selectRaw("MONTH(open_date) as month")
        ->selectRaw("COUNT(*) as value")
        ->where('created_by', Auth::user()->creatorId())
        ->whereBetween('open_date', [$startDate, $endDate])
        ->where('draft', 0)
        ->groupBy('category', 'year', 'month', 'open_date');
    
    $lineGraphCases = $lineGraphCases->get();
    
    $lineChartData = [];
    
    foreach ($lineGraphCases as $case) {
        // Formatting the date for x-axis with month and year
        $formattedDate = date('M Y', mktime(0, 0, 0, $case->month, 1, $case->year));
    
        $lineChartData[$case->category][] = [
            'x' => $formattedDate,
            'y' => $case->value,
        ];
    }
    
    $transformedLineChartData = [];
    foreach ($lineChartData as $category => $dataPoints) {
        $transformedLineChartData[] = [
            'name' => $category,
            'data' => $dataPoints,
        ];
    }

    $lineChartData = $transformedLineChartData;

    // $lineChartData = [];
    // foreach ($lineGraphCases as $case) {
    //     $lineChartData[] = [
    //         'category' => $case->category,
    //         'year' => $case->year,
    //         'month' => $case->month,
    //         'value' => $case->value,
    //     ];
    // }
    
        return response()->json([
            'piechartData' => $piechartData,
            'chartData' => $chartData,
            'lineChartData' => $lineChartData,
        ]);
    }
    
    
    

}
