<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
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
        if (Auth::user()->can('manage order')) {
            $objUser = Auth::user();
            if ($objUser->type == 'super admin') {
                $orders = Order::select(
                    [
                        'orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->get();
            } else {
                $orders = Order::select(
                    [
                        'orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->get();
            }

            return view('order.index', compact('orders'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function addPayment(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $invoice = Bill::find($id);

            if (Auth::check()) {
                $user_id = Auth::user()->creatorId();
                $company_payment_setting = Utility::getCompanyPaymentSetting($user_id);
            } else {

                $user = User::where('id', $invoice->created_by)->first();
                $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
            }

            if ($invoice) {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $price = $request->amount;
                Stripe::setApiKey($company_payment_setting['stripe_secret']);

                $data = Charge::create(
                    [
                        "amount" => 100 * $price,
                        "currency" => !empty($company_payment_setting['site_currency']) ? $company_payment_setting['site_currency'] : 'INR',
                        "source" => $request->stripeToken,
                        "description" => 'Invoice - ' . isset($invoice->description) ? $invoice->description : '',
                        "metadata" => ["order_id" => $orderID],
                    ]

                );

                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {

                        $payments = new BillPayment();
                        $payments['bill_id'] = $id;
                        $payments['date'] = date('Y-m-d');
                        $payments['amount'] = $price;
                        $payments['method'] = __('STRIPE');
                        $payments['order_id'] = $orderID;
                        $payments['currency'] = $data['currency'];
                        $payments['txn_id'] = $data['balance_transaction'];
                        $payments['note'] = $invoice->description;
                        $payments->save();

                        $payment = BillPayment::where('bill_id',$id)->sum('amount');

                        if ($payment >= $invoice->total_amount) {
                            $invoice->status = 'PAID';
                            $invoice->due_amount = 0.00;
                        } else {
                            $invoice->status = 'Partialy Paid';
                            $invoice->due_amount = $invoice->due_amount - $price;
                        }

                        $invoice->save();

                    if (Auth::check()) {
                        return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                    } else {
                        return redirect()->back()->with('success', __(' Payment successfully added.'));
                    }

                } else {
                    if (Auth::check()) {
                        return redirect()->route('bills.show', $invoice->id)->with('error', __('Transaction has been failed.'));
                    } else {
                        return redirect()->back()->with('success', __('Transaction succesfull'));
                    }
                }

            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // plan
    public function stripePost(Request $request)
    {
       
    

        $this->planpaymentSetting();

        $objUser = Auth::user();
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
    
        $setting = Utility::settings();
        $currency  = $setting['site_currency'] ?? 'USD';


        if ($plan) {
            try
            {
                $price = $plan->price;

                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price = $plan->price - $discount_value;

                        if ($usedCoupun >= $coupons->limit) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if ($price > 0.0) {

                    Stripe::setApiKey($this->stripe_secret);

                    $stripeCustomerID = $objUser->stripe_id;
                    if ($stripeCustomerID) {

                   
                        try {



                            // $data = Charge::create([
                            //     "amount" => 100 * $price,
                            //     "currency" => $currency,
                            //     "customer" => $stripeCustomerID,
                            //     "description" => "Plan - " . $plan->name,
                            //     "metadata" => ["order_id" => $orderID],
                            // ]);


                            $stripe = new \Stripe\StripeClient($this->stripe_secret);
                            $stripe->subscriptions->cancel(
                                $objUser->subscription_id,
                            []
                            );

                            $stripeClient = new \Stripe\StripeClient($this->stripe_secret);
                            $subData =   $stripeClient->subscriptions->create([
                              'customer' => $stripeCustomerID,
                              'items' => [
                                  ['price' => $plan->stripe_price_id],
                              ],
                              ]);
      
                           
                            $objUser->update(['subscription_id' => $subData->id]);
                        
                           
                            // Handle success and update your order and user data accordingly
                            // ...
                        } catch (\Stripe\Exception\CardException $e) {
                            // Handle card errors
                            dd($e->getMessage()); // Output the error message for debugging
                        } catch (\Stripe\Exception\RateLimitException $e) {
                            // Handle rate limit errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\InvalidRequestException $e) {
                            // Handle invalid request errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\AuthenticationException $e) {
                            // Handle authentication errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\ApiConnectionException $e) {
                            // Handle API connection errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            // Handle other Stripe API errors
                            dd($e->getMessage());
                        }

                       
                    } 
                    else {
                      

                        try {
                            $stripeCustomer = Customer::create([
                                'email' => $objUser->email, 
                                'source' => $request->stripeToken, 
                            ]);

                            $stripeClient = new \Stripe\StripeClient($this->stripe_secret);
                          $subData =   $stripeClient->subscriptions->create([
                            'customer' => $stripeCustomer->id,
                            'items' => [
                                ['price' => $plan->stripe_price_id],
                            ],
                            ]);
    

                        // $data = Charge::create(
                        //     [
                        //         "amount" => 100 * $price,
                        //         "currency" => $currency,
                        //         "customer" => $stripeCustomer->id,
                        //         "description" => " Plan - " . $plan->name,
                        //         "metadata" => ["order_id" => $orderID],
                        //     ]
                        // );

                       

                            $objUser->update(['stripe_id' => $stripeCustomer->id]);
                            $objUser->update(['subscription_id' => $subData->id]);

                        } catch (\Stripe\Exception\CardException $e) {
                            // Handle card errors
                            dd($e->getMessage()); // Output the error message for debugging
                        } catch (\Stripe\Exception\RateLimitException $e) {
                            // Handle rate limit errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\InvalidRequestException $e) {
                            // Handle invalid request errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\AuthenticationException $e) {
                            // Handle authentication errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\ApiConnectionException $e) {
                            // Handle API connection errors
                            dd($e->getMessage());
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            // Handle other Stripe API errors
                            dd($e->getMessage());
                        }

                    }
                  
                } else {
                    $data['amount_refunded'] = 0;
                    $data['failure_code'] = '';
                    $data['paid'] = 1;
                    $data['captured'] = 1;
                    $data['status'] = 'succeeded';
                    $data['payment_method_details']['card']['last4'] = '';
                    $data['payment_method_details']['card']['exp_month'] = '';
                    $data['payment_method_details']['card']['exp_year'] = '';
                    $data['currency'] = ($this->currancy) ? strtolower($this->currancy) : $currency;
                    $data['receipt_url'] = 'free coupon';
                    $data['balance_transaction'] = 0;
                }
                
                if (!empty($subData->id)) {

                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'card_number' =>  '',
                            'card_exp_month' => '',
                            'card_exp_year' => '',
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price,
                            'price_currency' => $this->currancy,
                            'txn_id' =>  '',
                            'payment_status' => 'succeeded',
                            'payment_type' => __('STRIPE'),
                            'receipt' => 'free coupon',
                            'user_id' => $objUser->id,
                        ]
                    );

                    if (!empty($request->coupon)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $objUser->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $orderID;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }

                    if (!empty($subData->id)) {
                        $assignPlan = $objUser->assignPlan($plan->id, $request->frequency);
                        if ($assignPlan['is_success']) {
                            return redirect()->back()->with('success', __('Plan activated Successfully!'));
                        } else {
                            return redirect()->back()->with('error', __($assignPlan['error']));
                        }
                    } else {
                        return redirect()->back()->with('error', __('Your Payment has failed!'));
                    }
                } else {
                    return redirect()->back()->with('error', __('Transaction has been failed!'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function stripeIntent(Request $request)
    {
        
        $objUser = Auth::user();

        // Set your Stripe secret key
        $admin_payment_setting = Utility::payment_settings();
        Stripe::setApiKey($admin_payment_setting['stripe_secret']);
        

        // Create a PaymentIntent
        try {

            $paymentIntent = PaymentIntent::create([
                'amount' => 100 * $request['planPrice'], 
                'currency' => $admin_payment_setting['currency'] ?? 'usd', 
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],

            ]);


            // dd($paymentIntent);

            // $paymentIntentId = $paymentIntent->id;  
            // $customer = \Stripe\Customer::create([
            //     'email' => $objUser->email,  
            // ]);

            // \Stripe\PaymentIntent::update($paymentIntentId, [
            //     'customer' => $customer->id,
            // ]);

            // $PaymentIntent = PaymentIntent::retrieve($paymentIntentId);


            return response()->json(['clientSecret' => $paymentIntent->client_secret]);
        } catch (\Exception $e) {
          
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function orderSuccess(Request $request){
        
        

        $paymentIntentId = $request['paymentIntentId'];
        $planId = $request['plan'];

        $setting = Utility::settings();
        $currency  = $setting['site_currency'] ?? 'USD';
        $objUser = Auth::user();
        $plan = Plan::find($planId);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));


        Order::create(
            [
                'order_id' => $orderID,
                'name' => $objUser->name,
                'card_number' =>  '',
                'card_exp_month' => '',
                'card_exp_year' => '',
                'plan_name' => $plan->name,
                'plan_id' => $plan->id,
                'price' => $plan->price,
                'price_currency' => $currency ?? '',
                'txn_id' => '',
                'payment_status' => 'succeeded',
                'payment_type' => __('STRIPE'),
                'receipt' => 'free coupon',
                'user_id' => $objUser->id,
            ]
        );


        $objUser->update(['stripe_id' => $paymentIntentId]);
        
        $assignPlan = $objUser->assignPlan($planId);

        if ($assignPlan['is_success']) {
            return true;
        } else {
            return false;
        }
        
       

    }
}
