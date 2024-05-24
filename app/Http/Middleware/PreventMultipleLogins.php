<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
class PreventMultipleLogins
{
    /** 
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
     

            if (Auth::check()) {
                $user = Auth::user();
    
                
                if ($user->type !== 'super admin') {

                    if($user->type == 'company'){

                        if ($user->plan === 1) {
                            $planActiveDate = Carbon::parse($user->plan_active_date);
                            $currentDate = Carbon::now();
                            $daysDifference = $planActiveDate->diffInDays($currentDate);
                            if ($daysDifference > 15) {

                                $urlPath = $request->path();
                               
                                if (strpos($urlPath, 'settings') !== false || strpos($urlPath, 'settings') !== false || strpos($urlPath, 'plan') !== false || strpos($urlPath, 'plans') !== false) {
                                    return $next($request);
                                }
                                return redirect()->route('settings.index')->with('trialExpired', true);

                            }
                        }

                    }else{

                       
                        $loginUser = User::findOrfail($user->created_by);

                        
                        if ($loginUser->plan === 1) {
                            $planActiveDate = Carbon::parse($loginUser->plan_active_date);
                            $currentDate = Carbon::now();
                            $daysDifference = $planActiveDate->diffInDays($currentDate);
                            if ($daysDifference > 15) {
                                Auth::logout();
                                return redirect()->route('login')->with('error', 'Your Law Firm trial plan of 15 days has expired kindly contact your Law Firm.');
                            }
                        }

                    }


                    $sessions = DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->whereRaw('id <> (SELECT id FROM sessions WHERE user_id = ? ORDER BY last_activity DESC LIMIT 1)', [$user->id])
                        ->orderBy('last_activity', 'desc')
                        ->get();
            
                    $sessionIdsToDelete = [];
            
                    foreach ($sessions as $session) {
                        if (Carbon::parse($session->last_activity)->diffInMinutes(Carbon::now()) <= 7) {
                            Auth::logout();
                            return redirect()->route('login')->with('error', 'You are already logged in on another device.');
                        } else {
                            $sessionIdsToDelete[] = $session->id;
                        }
                    }
                    
                    DB::table('sessions')->whereIn('id', $sessionIdsToDelete)->delete();
                }

            }


            return $next($request);
     
    }
}
