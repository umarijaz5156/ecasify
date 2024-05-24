<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\LoginDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Catch_;
use App\Mail\UserLoginNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $lang = 'en')
    {

        $preferredLanguage = $request->getPreferredLanguage();
           
        if(!empty($preferredLanguage)){
            
            $lang  = explode('_', $preferredLanguage)[0];
        }else{

            $lang = Utility::getValByName('default_language');
        }
        

        if ($lang == 'ar' || $lang == 'he') {
            $value = 'on';
        } else {
            $value = 'off';
        }
        DB::insert(
            'insert into settings (`value`, `name`,`created_by`) values ( ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
            [
                $value,
                'SITE_RTL',
                1,

            ]
        );
        App::setLocale($lang);
        return view('auth.login', compact('lang'));
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

      

        if (env('RECAPTCHA_MODULE') == 'yes') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        } else {
            $validation = [];
        }

        $this->validate($request, $validation);

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
       
        $ip = $_SERVER['REMOTE_ADDR']; // your ip address here
        $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
        $query2 = @unserialize(file_get_contents('http://ip-api.com/php'));
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
        if ($whichbrowser->device->type == 'bot') {
            return;
        }
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;
        /* Detect extra details about the user */
        $query['browser_name'] = $whichbrowser->browser->name ?? null;
        $query['os_name'] = $whichbrowser->os->name ?? null;
        $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $query['device_type'] = self::get_device_type($_SERVER['HTTP_USER_AGENT']);
        $query['referrer_host'] = !empty($referrer['host']);
        $query['referrer_path'] = !empty($referrer['path']);

        isset($query['timezone']) ? date_default_timezone_set($query['timezone']) : '';

        $json = json_encode($query);
        // flush tmp folder
        $sourceFolderPath = storage_path('app/public/uploads/case_docs/tmp/'. Auth::user()->id.'-case-docs');
        // delete folder if exits
        if (File::exists($sourceFolderPath)) {
            File::deleteDirectory($sourceFolderPath);
        }

        $user = Auth::user();
       
        // sending mail to notify login
        $mailData = array();
        //  username,ipAddress,device,browser,location,appName,companyEmail
        $mailData['username'] = $user->name;
        $mailData['ipAddress'] = $ip;
       $mailData['device'] = $query['device_type'];
        $mailData['browser'] = $query['browser_name'];
        $mailData['location'] = $query2['city'] ?? '--' . ',' . $query2['country'] ?? '--';
        $mailData['loginTime'] = date('Y-m-d H:i:s');
        $mailData['companyName'] = env('APP_NAME');
        $mailData['companyEmail'] = env('MAIL_FROM_ADDRESS');
        

       
       
        try {
            Mail::to($user->email)->send(new UserLoginNotification($mailData));

        } catch (\Exception $e) {
          
        }

        if ($user->type != 'company' && $user->type != 'super admin') {
            $login_detail = LoginDetail::create([
                'user_id' =>  $user->id,
                'ip' => $ip,
                'date' => date('Y-m-d H:i:s'),
                'details' => $json,
                'created_by' => Auth::user()->creatorId(),
            ]);
        }

    
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $sourceFolderPath = storage_path('app/public/uploads/case_docs/tmp/'. Auth::user()->id.'-case-docs');
        // delete folder if exits
        if (File::exists($sourceFolderPath)) {
            File::deleteDirectory($sourceFolderPath);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    function get_device_type($user_agent)
    {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {
            if (preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }
}
