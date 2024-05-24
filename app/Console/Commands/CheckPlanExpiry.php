<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Utility;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;


class CheckPlanExpiry extends Command
{
  
    protected $signature = 'check:plan_expiry';
    protected $description = 'Check plan expiry dates and deduct payments';

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

    public function handle()
    {
        $users = User::where('plan_expire_date', now()->toDateString())->get();

        $setting = Utility::settings();
        $currency  = $setting['site_currency'] ?? 'USD';

        $this->planpaymentSetting();

        foreach ($users as $user) {

            $plan = $user->getPlan();

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            Stripe::setApiKey($this->stripe_secret);
            $stripeCustomerID = $user->stripe_id;

            if ($stripeCustomerID) {
                try {

                    $data = Charge::create([
                        "amount" => 100 * $plan->price, 
                        "currency" => $currency, 
                        "customer" => $stripeCustomerID,
                        "description" => "Plan - " . $plan->name,
                         "metadata" => ["order_id" => $orderID],
                    ]);
                 

                } catch (CardException $e) {
                    // Handle card errors
                    $this->error($e->getMessage());
                } catch (RateLimitException $e) {
                    // Handle rate limit errors
                    $this->error($e->getMessage());
                } catch (InvalidRequestException $e) {
                    // Handle invalid request errors
                    $this->error($e->getMessage());
                } catch (AuthenticationException $e) {
                    // Handle authentication errors
                    $this->error($e->getMessage());
                } catch (ApiConnectionException $e) {
                    // Handle API connection errors
                    $this->error($e->getMessage());
                } catch (ApiErrorException $e) {
                    // Handle other Stripe API errors
                    $this->error($e->getMessage());
                }


                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {

                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $user->name,
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $plan->price,
                            'price_currency' => isset($data['currency']) ? $data['currency'] : $currency,
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'payment_type' => __('STRIPE'),
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'user_id' => $user->id,
                        ]
                    );
                }
                if ($data['status'] == 'succeeded') {
                    $assignPlan = $user->assignPlanCommand($plan->id);
                }
            }
        }

        return Command::SUCCESS;
    }

   
}
