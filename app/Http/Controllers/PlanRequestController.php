<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanRequest;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class PlanRequestController extends Controller
{
    public $currancy;
    public $currancy_symbol;
    public $stripe_secret;
    public $stripe_key;
 
    public function planpaymentSetting()
    {
        $admin_payment_setting = Utility::payment_settings();
        $this->currancy_symbol = isset($admin_payment_setting['currency_symbol'])?$admin_payment_setting['currency_symbol']:'';
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        $this->stripe_secret = isset($admin_payment_setting['stripe_secret'])?$admin_payment_setting['stripe_secret']:'';
        $this->stripe_key = isset($admin_payment_setting['stripe_key'])?$admin_payment_setting['stripe_key']:'';
    }

    public function index()
    {
        if (Auth::user()->type == 'super admin') {
            $plan_requests = PlanRequest::all();
           
            return view('plan_request.index', compact('plan_requests'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userRequest($plan_id)
    {
        $objUser = Auth::user();
        if (Auth::user()->type == 'company') {

            $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);

            if (!empty($planID)) {
                PlanRequest::create([
                    'user_id' => $objUser->id,
                    'plan_id' => $planID,

                ]);

                $objUser['requested_plan'] = $planID;
                $objUser->update();

                return redirect()->back()->with('success', __('Request Send Successfully.'));
            } else {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        } else {
            return redirect()->back()->with('error', __('You already send request to another plan.'));
        }
    }

    public function acceptRequest($id, $response)
    {
        
            $plan_request = PlanRequest::find($id);

            if (!empty($plan_request)) {
                $user = User::find($plan_request->user_id);

                if ($response == 1) {

                    $arrDuration = Plan::$arrDuration;
                    $plan = PlanRequest::find($id);
                   
                    return view('plan.editCompany', compact('plan', 'arrDuration'));
                } else {
                    $user['requested_plan'] = '0';
                    $user->update();

                    $plan_request->delete();

                    return redirect()->back()->with('success', __('Request Rejected Successfully.'));
                }
            } else {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
    }

    
public function acceptCompany(Request $request, $id)
{
   
    try {
        $plan_request = PlanRequest::find($id);
        // get uers where id = $plan_request->user_id;
        $user = User::find($plan_request->user_id);
        if($user->type == 'co admin'){
            $plan_request->user_id = $user->creatorId();
        }


        $admin_payment_setting = Utility::set_payment_settings();

           

        if (!empty($admin_payment_setting) && ($admin_payment_setting['is_stripe_enabled'] == 'on' || $admin_payment_setting['is_paypal_enabled'] == 'on' || $admin_payment_setting['is_paystack_enabled'] == 'on' || $admin_payment_setting['is_flutterwave_enabled'] == 'on' || $admin_payment_setting['is_razorpay_enabled'] == 'on' || $admin_payment_setting['is_mercado_enabled'] == 'on' || $admin_payment_setting['is_paytm_enabled'] == 'on' || $admin_payment_setting['is_mollie_enabled'] == 'on' || $admin_payment_setting['is_skrill_enabled'] == 'on' || $admin_payment_setting['is_coingate_enabled'] == 'on' || $admin_payment_setting['is_paymentwall_enabled'] == 'on' || $admin_payment_setting['is_manually_enabled'] == 'on' || $admin_payment_setting['is_bank_enabled'] == 'on' || $admin_payment_setting['is_paytab_enabled'] == 'on' ))
        {
            
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|unique:plans',
                    'price' => 'required|numeric|min:0',
                    'duration' => 'required',
                    'max_users' => 'required|numeric',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }


            $currency  = $setting['site_currency'] ?? 'USD';

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));


            $stripe = new \Stripe\StripeClient($admin_payment_setting['stripe_secret']);
            $stripeProduct =   $stripe->products->create([
            'name' => $request->name,
            ]);


         

            $stripePrice = $stripe->prices->create([
                'unit_amount' =>  100 * $request->price,
                'currency' => $currency,
                'recurring' => ['interval' => $request->duration],
                'product' => $stripeProduct->id
            ]);


           
          

            $plan = new Plan();

            $plan->name = $request->name;
            $plan->price = $request->price;
            $plan->duration = $request->duration;
            $plan->max_users = $request->max_users;
            $plan->description = $request->description;
            $plan->user_id = $plan_request->user_id;
            $plan->stripe_product_id = $stripeProduct->id;
            $plan->stripe_price_id = $stripePrice->id;
            $plan->details = json_encode($request->details);
    
            $plan->save();
            $plan_request->delete();
    
            return redirect()->back()->with('success', __('Plan Request Accepted Successfully.'));

        } else {
            return redirect()->back()->with('error', __('Please set stripe or paypal api key & secret key for add new plan.'));
        }



    } catch (QueryException $e) {
        // Check if the error is due to a duplicate entry
        if ($e->getCode() == 23000) {
            return redirect()->back()->with('error', __('Plan with the same name already exists.'));
        } else {
            // Handle other database errors as needed
            return redirect()->back()->with('error', __('An error occurred while processing your request.'));
        }
    }
}

    public function cancelRequest($id)
    {
        $user = User::find($id);
        $user['requested_plan'] = '0';
        $user->update();
        PlanRequest::where('user_id', $id)->delete();

        return redirect()->back()->with('success', __('Request Canceled Successfully.'));
    }
}
