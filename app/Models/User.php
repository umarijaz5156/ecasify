<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'plan',
        'lang',
        'avatar',
        'created_by',
        'email_verified_at',
        'google_calendar_id',
        'stripe_id',
        'role_title',
        'subscription_id',
        'plan_active_date',
        'plan_expire_date',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function creatorId()
    {
        if ($this->type == 'company' || $this->type == 'super admin') {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }
    // get company name bu user id
    public static function getCompanyName($id,$return = 'name')
    {
        // dd($return);
        $user = User::find($id);
      
            if($user->type = 'company'){
                return $user->$return;
            }else{
                //  call getCompanyName again $user->created_by
                return User::getCompanyName($user->created_by,$return);
            }


    }

    public static function getTeams($id)
    {
        $advName = User::whereIn('id', explode(',', $id))->pluck('name')->toArray();
        return implode(', ', $advName);
    }

    public static function getUser($id)
    {
        $advName = User::find($id);
        return $advName;
    }
    public function currentLanguage()
    {
        return $this->lang;
    }

    public function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return '#' . sprintf("%05d", $number);
    }

    public static function dateFormat($date)
    {
        $settings = Utility::settings();
        return date($settings['site_date_format'], strtotime($date));
    }

    public function assignPlan($planID)
    {
        $plan = Plan::find($planID);
        if ($plan) {
            $this->plan = $plan->id;
            if ($plan->duration == 'month') {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->duration == 'year') {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            } else {
                $this->plan_expire_date = null;
            }
            $this->save();

            $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'employee')->get();
            $employees = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', 'employee')->get();

            $userCount = 0;
            foreach ($users as $user) {
                $userCount++;
                if ($userCount <= $plan->max_users) {
                    $user->is_active = 1;
                    $user->save();
                } else {
                    $user->is_active = 0;
                    $user->save();
                }
            }
            $employeeCount = 0;
            foreach ($employees as $employee) {
                $employeeCount++;
                if ($employeeCount <= $plan->max_employees) {
                    $employee->is_active = 1;
                    $employee->save();
                } else {
                    $employee->is_active = 0;
                    $employee->save();
                }
            }

            return ['is_success' => true];
        } else {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function assignPlanCommand($planID)
    {
        $plan = Plan::find($planID);
        if ($plan) {
            $this->plan = $plan->id;
            if ($plan->duration == 'month') {
                $this->plan_active_date = Carbon::now()->isoFormat('YYYY-MM-DD');

                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->duration == 'year') {
                $this->plan_active_date = Carbon::now()->isoFormat('YYYY-MM-DD');

                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            } else {
                $this->plan_expire_date = null;
            }
            $this->save();
            return ['is_success' => true];
            
        } else {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function countPaidCompany()
    {
        return User::where('type', '=', 'company')->whereNotIn(
            'plan',
            [
                0,
                1,
            ]
        )->where('created_by', '=', \Auth::user()->id)->count();
    }

    public function getPlan()
    {
        $user = User::find($this->creatorId());

        return Plan::find($user->plan);
    }

    public static function MakeRole($id,$role_name = 'advocate')
    {
        $data = [];
        // swicth case

        if($role_name == 'advocate'){
            $role_permission = [
                "show dashboard",
    
                "show group",
                "manage group",
    
                "manage cause",
                "create cause",
                "delete cause",
                "edit cause",
    
                "manage case",
                "create case",
                "edit case",
                "view case",
                "delete case",
    
                "create tasks",
                "edit tasks",
                "view tasks",
                "delete tasks",
                "manage tasks",
    
                "manage bill",
                "create bill",
                "edit bill",
                "delete bill",
                "view bill",
    
                "manage diary",
    
                "manage timesheet",
                "create timesheet",
                "edit timesheet",
                "delete timesheet",
                "view timesheet",
    
                "manage expense",
                "create expense",
                "edit expense",
                "delete expense",
                "view expense",
    
                "manage feereceived",
                "create feereceived",
                "edit feereceived",
                "delete feereceived",
                "view feereceived",
    
                "view calendar",
    
                "manage document",
                "create document",
                "edit document",
                "delete document",
                "view document",
            ];
        }elseif($role_name == 'client'){
            $role_permission = [
                "show dashboard",
                "manage case",
                "view case",
                "view tasks",
                "manage tasks",
                'tasks case',
            ];            
        }elseif($role_name == 'staff'){
            $role_permission = [
                "show dashboard",
                "show group",
                "manage group",
                "manage case",
                "view case",
                "create tasks",
                "edit tasks",
                "view tasks",
                "delete tasks",
                "manage tasks",
                "manage bill",
                "create bill",
                "edit bill",
                "delete bill",
                "view bill",
                "manage diary",
                "manage timesheet",
                "create timesheet",
                "edit timesheet",
                "delete timesheet",
                "view timesheet",
                "manage expense",
                "create expense",
                "edit expense",
                "delete expense",
                "view expense",
                "manage feereceived",
                "create feereceived",
                "edit feereceived",
                "delete feereceived",
                "view feereceived",
                "view calendar",
                "manage appointment",
                "create appointment",
                "edit appointment",
                "delete appointment",
            ];
        }elseif($role_name == 'co-Admin'){
            $role_permission = [
                "buy plan",
                "calendar case",
                "calendar create case",
                "calendar delete case",
                "calendar edit case",
                "create appointment",
                "create bill",
                "create cause",
                "create contact",
                "create document",
                "create doctype",
                "create expense",
                "create feereceived",
                "create group",
                "create member",
                "create permission",
                "create role",
                "create staff",
                "create team",
                "create tax",
                "create timesheet",
                "create user",
                "delete appointment",
                "delete bill",
                "delete case",
                "delete cause",
                "delete contact",
                "delete document",
                "delete doctype",
                "delete expense",
                "delete feereceived",
                "delete group",
                "delete member",
                "delete permission",
                "delete role",
                "delete staff",
                "delete team",
                "delete tax",
                "delete timesheet",
                "delete user",
                "edit appointment",
                "edit bill",
                "edit case",
                "edit cause",
                "edit contact",
                "edit document",
                "edit doctype",
                "edit expense",
                "edit feereceived",
                "edit group",
                "edit member",
                "edit permission",
                "edit role",
                "edit staff",
                "edit team",
                "edit tax",
                "edit timesheet",
                "edit user",
                "manage appointment",
                "manage bill",
                "manage calendar",
                "manage case",
                "manage cause",
                "manage contact",
                "manage dashboard",
                "manage document",
                "manage doctype",
                "manage expense",
                "manage feereceived",
                "manage group",
                "manage highcourt",
                "manage member",
                "manage permission",
                "manage role",
                "manage setting",
                "manage staff",
                "manage team",
                "manage tax",
                "manage tasks",
                "manage timesheet",
                "manage user",
                "manage tasks",
                "show calendar",
                "show dashboard",
                "show group",
                "show member",
                "show permission",
                "show role",
                "show staff",
                "show team",
                "show user",
                "timeline case",
                "view appointment",
                "view bill",
                "view calendar",
                "view case",
                "view cause",
                "view contact",
                "view dashboard",
                "view document",
                "view doctype",
                "view expense",
                "view feereceived",
                "view group",
                "view highcourt",
                "view member",
                "view permission",
                "view role",
                "view staff",
                "view team",
                "view tax",
                "view tasks",
                "view timesheet",
                "view user"
            ];
        }

        $advocate_role = Role::where('name', $role_name)->where('created_by', $id)->where('guard_name', 'web')->first();

        if (empty($advocate_role)) {
            $advocate_role                   = new Role();
            $advocate_role->name             = $role_name;
            $advocate_role->guard_name       = 'web';
            $advocate_role->created_by       = $id;
            $advocate_role->save();

            foreach ($role_permission as $permission_s) {
                $permission = Permission::where('name', $permission_s)->first();
                $advocate_role->givePermissionTo($permission);
            }
        }

        $data['advocate_role'] = $advocate_role;

        return $data;
    }


    public function userDetails()
    {
        return $this->hasOne(UserDetail::class);
    }
    // hasPermission
    public function hasPermission($permission)
    {
        $permission = Permission::where('name', $permission)->first();
        // role_has_premissons
        $role_has_premissons = DB::table('role_has_permissions')->where('permission_id', $permission->id)->where('role_id', $this->roles[0]->id)->first();


        if ($permission) {

            dd($this->hasRole(7));
            return true;
        } else {
            return false;
        }
    }

    public function coAdminIds()
{
    // Find all users with the creator ID
    $users = User::where('created_by', $this->creatorId())->get();

    $coAdminIds = [];

    // Loop through the users and check their type
    foreach ($users as $user) {
        if ($user->type == 'co admin') {
            $coAdminIds[] = $user->id;
        }
    }

    return $coAdminIds;
}



}
