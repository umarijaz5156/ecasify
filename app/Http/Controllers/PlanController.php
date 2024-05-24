<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanRequest;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Customer;

class PlanController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('manage plan') || Auth::user()->can('buy plan')) {
            if(Auth::user()->type == 'super admin'){
                $plansMonthly = Plan::where('duration','month')->get();
                $plansYearly = Plan::where('duration','year')->get();

            }else{
                if (Auth::user()->type == 'co admin'  ) {
                    $user = Auth::user();
                    $userIds = $user->coAdminIds();
                    $userIds[] = intval($user->creatorId());
                }else{
                    $userIds = [Auth::user()->id];
                }
                $plansMonthly = Plan::where(function ($query) use ($userIds) {
                    $query->where('user_id', 1)->where('duration', 'month');
                })->orWhere(function ($query) use ($userIds) {
                    $query->where('duration', 'month')->whereIn('user_id', $userIds);
                })->get();
                
                $plansYearly = Plan::where(function ($query) use ($userIds) {
                    $query->where('user_id', 1)->where('duration', 'year');
                })->orWhere(function ($query) use ($userIds) {
                    $query->where('duration', 'year')->whereIn('user_id', $userIds);
                })->get();
               }
          
            $payment_setting = Utility::set_payment_settings();
            return view('plan.index', compact('plansMonthly','plansYearly', 'payment_setting'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('create plan')) {
            $arrDuration = Plan::$arrDuration;
          
            return view('plan.create', compact('arrDuration'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function companyPlan()
    {
        
            $arrDuration = Plan::$arrDuration;
            
            return view('plan.createCompany', compact('arrDuration'));
       

    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      

        if ($request->request_by == 'company') {
          
                $validator = Validator::make(
                    $request->all(), [
                        'duration' => 'required',
                        'max_users' => 'required|numeric',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                
                $plan_request = new PlanRequest();
                $plan_request['name'] = $request->name;
                $plan_request['duration'] = $request->duration;
                $plan_request['max_users'] = $request->max_users;
                $plan_request['description'] = $request->description ?? '';
                $plan_request['user_id'] = Auth::user()->id;
                $plan_request['price_range'] = $request->price_range;

                 $plan_request->save();
              
                if ($plan_request) {
                    return redirect()->back()->with('success', __('Plan Request created Successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Something is wrong.'));
                }

          
        }


        if (Auth::user()->can('create plan')) {


            $admin_payment_setting = Utility::set_payment_settings();

           

            if (!empty($admin_payment_setting) && ($admin_payment_setting['is_stripe_enabled'] == 'on' || $admin_payment_setting['is_paypal_enabled'] == 'on' || $admin_payment_setting['is_paystack_enabled'] == 'on' || $admin_payment_setting['is_flutterwave_enabled'] == 'on' || $admin_payment_setting['is_razorpay_enabled'] == 'on' || $admin_payment_setting['is_mercado_enabled'] == 'on' || $admin_payment_setting['is_paytm_enabled'] == 'on' || $admin_payment_setting['is_mollie_enabled'] == 'on' || $admin_payment_setting['is_skrill_enabled'] == 'on' || $admin_payment_setting['is_coingate_enabled'] == 'on' || $admin_payment_setting['is_paymentwall_enabled'] == 'on' || $admin_payment_setting['is_manually_enabled'] == 'on' || $admin_payment_setting['is_bank_enabled'] == 'on' || $admin_payment_setting['is_paytab_enabled'] == 'on' ))
            {
                
                $validator = Validator::make(
                    $request->all(), [
                        'name' => 'required|unique:plans',
                        'price' => 'required|numeric|min:0',
                        'duration' => 'required',
                        'max_users' => 'required|numeric|min:0',
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
             

                $stripePrice = $stripe->plans->create([
                    'amount' =>  100 * $request->price,
                    'currency' => $currency,
                    'interval' => $request->duration,
                    'product' => $stripeProduct->id
                ]);

                // $stripePrice = $stripe->prices->create([
                //     'unit_amount' =>  100 * $request->price,
                //     'currency' => $currency,
                //     'recurring' => ['interval' => $request->duration],
                //     'product' => $stripeProduct->id
                // ]);

            

                $post = $request->all();
                $post['user_id'] = Auth::user()->id;
                $post['stripe_product_id'] = $stripeProduct->id;
                $post['stripe_price_id'] = $stripePrice->id;
               
                $post['details'] = json_encode($request->details);

                if (Plan::create($post)) {
                    return redirect()->back()->with('success', __('Plan Successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Something is wrong.'));
                }

            } else {
                return redirect()->back()->with('error', __('Please set stripe or paypal api key & secret key for add new plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->can('edit plan')) {
            $arrDuration = Plan::$arrDuration;
            $plan = Plan::find($id);

            return view('plan.edit', compact('plan', 'arrDuration'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $plan_id)
    {
        if (Auth::user()->can('edit plan')) {
            $payment = Utility::set_payment_settings();

            if (count($payment) > 0) {
                $plan = Plan::find($plan_id);
            
                if (!empty($plan)) {
                    $validation = [];
                    $validation['name'] = 'required|unique:plans,name,' . $plan_id;
                    if($plan_id != 1){
                        $validation['price'] = 'required|numeric|min:0';
                        $validation['duration'] = 'required';    
                    }
                    $validation['max_users'] = 'required|numeric|min:0';
                    $request->validate($validation);

                    $post = $request->all();
                    if($plan->price == 0){

                    }elseif($plan->price != $request->price || $plan->duration != $request->duration){

                        
                     
                        $admin_payment_setting = Utility::set_payment_settings();

                        $currency  = $admin_payment_setting['site_currency'] ?? 'USD';
    
    
                        $stripe = new \Stripe\StripeClient($admin_payment_setting['stripe_secret']);

                        $product = $stripe->products->retrieve($plan->stripe_product_id); // Replace with the actual Product ID
                      

                        $newPrice = $stripe->plans->create([
                            'amount' =>  100 * $request->price,
                            'currency' => $currency,
                            'interval' => $request->duration,
                            'product' => $product->id
                        ]);
                 
                            
                    }
                  
                    
                    $post = $request->all();
                    $post['user_id'] = Auth::user()->id;
                    if($plan->price != $request->price || $plan->duration != $request->duration){
                        $post['stripe_price_id'] = $newPrice->id; 
                    }
                    
                    $post['details'] = json_encode($request->details);
                    if ($plan->update($post)) {
                        return redirect()->back()->with('success', __('Plan Successfully updated.'));
                    } else {
                        return redirect()->back()->with('error', __('Something is wrong.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('Plan not found.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please set payment api key & secret key for update plan'));
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function payment($code)
    {
     
        $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
       
        $plan    = Plan::find($plan_id);
       
        if($plan)
        {
            if(Auth::user()->stripe_id){

                $oldPlan    = Plan::find(Auth::user()->plan);
               
                if($oldPlan->max_users >= $plan->max_users){
                   
                    
                    $msg = 'Are you sure you want to downgrade this package?';
                    $btnTxt = 'Yes downgrade';
                    return response()->json(['action' => 'openModal', 'view' => view('plan.confirmation-modal', compact('code', 'msg', 'btnTxt'))->render()]);

                }else{
                   
                    $msg = 'Are you sure you want to upgrade this package?';
                    $btnTxt = 'Yes upgrade';
                    return response()->json(['action' => 'openModal','view' => view('plan.confirmation-modal', compact('code', 'msg', 'btnTxt'))->render()]);

                }


               
            }else{

                $url = route('paymentAjax', $code);
                $responseData['action'] = 'openNewTab';
                $responseData['url'] = $url;

                return response()->json($responseData);
                


              $admin_payment_setting = Utility::payment_settings();
            $url = route('paymentAjax', $code);
                return view('payment', compact('url', 'plan', 'admin_payment_setting',));

                return view('payment', ['plan' => $plan, 'admin_payment_setting' => $admin_payment_setting]);

                return redirect()->route('paymentAjax',$code);
  
            }
            

        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function upgradePlan($planId){
        
        $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($planId);
        $user = Auth::user();
        if($user->type == 'co admin'){
            $user = User::where('id',$user->created_by)->first();
        }
        $plan = Plan::where('id',$plan_id)->first();

        
        $old_plan = Plan::where('id',$user->plan)->first();

        $all_users = User::where('created_by',$user->id)->count();
       
        
        if($old_plan->max_users <= $plan->max_users || $all_users <= $plan->max_users){


            if($plan->price > 0){
                $setting = Utility::settings();
                $currency  = $setting['site_currency'] ?? 'USD';
    
                $this->planpaymentSetting();
          
             
    
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
    

               
                Stripe::setApiKey($this->stripe_secret);
                
                $stripeCustomerID =  Auth::user()->stripe_id;

              
 
                if ($stripeCustomerID) {
                   

                    try {

                        $stripe = new \Stripe\StripeClient($this->stripe_secret);
                        $stripe->subscriptions->cancel(
                            $user->subscription_id,
                        []
                        );

                        $stripeClient = new \Stripe\StripeClient($this->stripe_secret);
                        $subData =   $stripeClient->subscriptions->create([
                          'customer' => $stripeCustomerID,
                          'items' => [
                              ['price' => $plan->stripe_price_id],
                          ],
                          ]);
  
                       
                        $user->update(['subscription_id' => $subData->id]);
                    
                       
                        // Handle success and update your order and user data accordingly
                        // ...
                    } catch (\Stripe\Exception\CardException $e) {
                        // Handle card errors
                        return redirect()->back()->with('error', __( $e->getMessage()));
                    } catch (\Stripe\Exception\RateLimitException $e) {
                        // Handle rate limit errors
                        return redirect()->back()->with('error', __( $e->getMessage()));
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        // Handle invalid request errors
                        return redirect()->back()->with('error', __( $e->getMessage()));
                    } catch (\Stripe\Exception\AuthenticationException $e) {
                        // Handle authentication errors
                        return redirect()->back()->with('error', __( $e->getMessage()));
                    } catch (\Stripe\Exception\ApiConnectionException $e) {
                        // Handle API connection errors
                        return redirect()->back()->with('error', __( $e->getMessage()));
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        // Handle other Stripe API errors
                        return redirect()->back()->with('error', __( $e->getMessage()));
                    }
    
    
                    if (!empty($subData->id)) {
    
                        Order::create(
                            [
                                'order_id' => $orderID,
                                'name' => $user->name,
                                'card_number' =>  '',
                                'card_exp_month' =>  '',
                                'card_exp_year' =>  '',
                                'plan_name' => $plan->name,
                                'plan_id' => $plan->id,
                                'price' => $plan->price,
                                'price_currency' =>  $currency,
                                'txn_id' =>  '',
                                'payment_status' =>  'succeeded',
                                'payment_type' => __('STRIPE'),
                                'receipt' => 'free coupon',
                                'user_id' => $user->id,
                            ]
                        );
                    }

                    if (!empty($subData->id)) {
                        $assignPlan = $user->assignPlanCommand($plan->id);
                    }
                }
    
                return redirect()->back()->with('success', __('Plan Activate Successfully.'));

            }else{

                $assignPlan = $user->assignPlanCommand($plan->id);
                return redirect()->back()->with('success', __('Plan Activate Successfully.'));
            }
       


        }else{

            return redirect()->back()->with('error', __('You cannot downgrade your plan as your number of users are more than the downgradation, this would result in data loss that is guarded!.'));

        }
           

    }

    public function paymentAjax($code){

        $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
        $plan    = Plan::find($plan_id);
        $admin_payment_setting = Utility::payment_settings();
        $countries = Countries::all();

        return view('payment', ['plan' => $plan, 'admin_payment_setting' => $admin_payment_setting, 'countries' => $countries]);

    }
}
