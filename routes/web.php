<?php

use App\Http\Controllers\AamarpayController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdvocateController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\BenchController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CauseController;
use App\Http\Controllers\CountryStateCityController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\HighCourtController;
use App\Http\Controllers\ToDoController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DoctypeController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\MercadoPaymentController;
use App\Http\Controllers\PaytmPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\CoingatePaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanRequestController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\BenefitPaymentController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\HearingTypeController;
use App\Http\Controllers\IyziPayController;
use App\Http\Controllers\PayfastController;
use App\Http\Controllers\PaymentWallController;
use App\Http\Controllers\PaytabController;
use App\Http\Controllers\SspayController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ToyyibpayController;
use App\Http\Controllers\UserlogController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TemporaryController;
use App\Http\Controllers\FormEncryptionController;
use App\Http\Controllers\ExpensesTypeController;
use App\Mail\UserLoginNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


use App\Models\User;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('/dashboard', function () {
//     // $companyRole = Role::find(4);
//     // $company = User::find(1);

//     // $company->assignRole($companyRole);

//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');

    return redirect()->back()->with('success', __('Clear Cache successfully.'));
});


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/get-country', [CountryStateCityController::class, 'getCountry'])->name('get.country');
Route::post('/get-state', [CountryStateCityController::class, 'getState'])->name('get.state');
Route::post('/get-city', [CountryStateCityController::class, 'getCity'])->name('get.city');
Route::get('/get-all-city', [CountryStateCityController::class, 'getAllState'])->name('get.all.state');
Route::post('/get-timezone', [CountryStateCityController::class, 'getTimezone'])->name('get.timezone');
Route::post('/update-widget-position', [DashboardController::class, 'updateWidgetPositions'])->name('update.widget.position');
Route::get('/update-graph/{startDate?}/{endDate?}', [DashboardController::class, 'updateGraph'])->name('update.graph');

Route::get('get-timesheet-data/{startDate?}/{endDate?}', [DashboardController::class, 'getTimesheetData'])->name('getTimesheetData');




Route::group(['middleware' => ['auth', 'XSS', 'verified', 'prevent.multiple.logins']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);

    Route::post('delete/sessions', [UserController::class, 'deleteSession'])->name('delete.sessions');
    // demote-to-co-admin
    Route::post('users/demote-to-co-admin', [UserController::class, 'demoteToCoAdmin'])->name('users.demote-to-co-admin');
    // promote-to-co-admin
    Route::post('users/promote-to-co-admin', [UserController::class, 'promoteToCoAdmin'])->name('users.promote-to-co-admin');
    Route::resource('users', UserController::class);

    Route::get('users-list', [UserController::class, 'userList'])->name('users.list');
    Route::get('companyUsers/{id}', [UserController::class, 'companyUsers'])->name('users.companyUsers');

    
    Route::post('users/{id}/change-password',[UserController::class,'changeMemberPassword'])->name('member.change.password');
    Route::get('user/{id}/plan', [UserController::class, 'upgradePlan'])->name('plan.upgrade')->middleware(['auth', 'XSS']);
    Route::get('user/{id}/plan/{pid}', [UserController::class, 'activePlan'])->name('plan.active')->middleware(['auth', 'XSS']);
    Route::any('company-reset-password/{id}', [UserController::class, 'companyPassword'])->name('company.reset');


    Route::resource('teams', TeamController::class);

    Route::resource('groups', GroupController::class);

    Route::resource('advocate', AdvocateController::class);
    Route::get('/advocate/contacts/{id}', [AdvocateController::class, 'contacts'])->name('advocate.contacts');
    Route::get('/advocate/bills/{id}', [AdvocateController::class, 'bills'])->name('advocate.bill');


    Route::resource('courts', CourtController::class);

    Route::resource('highcourts', HighCourtController::class);

    Route::resource('bench', BenchController::class);

    Route::resource('cause', CauseController::class);
    Route::post('/cause/get-highcourts', [CauseController::class, 'getHighCourt'])->name('get.highcourt');
    Route::post('/cause/get-bench', [CauseController::class, 'getBench'])->name('get.bench');

    Route::post('cases/case_docs',[CaseController::class, 'caseDoc'])->name('case_docs');
    
    Route::post('cases/old_docs',[CaseController::class, 'caseDocOld'])->name('case_docs');
    
    Route::post('cases/delete-doc',[CaseController::class, 'deleteDoc'])->name('cases.delete.doc');

    Route::post('cases/caseDraft',[CaseController::class, 'caseDraft'])->name('case.draft');

    Route::get('cases/draft', [CaseController::class, 'draftView'])->name('cases.draft.view');

    Route::get('/get-users-for-case/{caseId}', [CaseController::class, 'getUsersForCase'])->name('cases.get.users');


    Route::post('cases/sendRequest', [GoogleCalendarController::class, 'sendRequest'])->name('google.calendar.send-request');
    Route::get('cases/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');
    Route::post('cases/createEvent', [GoogleCalendarController::class, 'createEvent'])->name('google.calendar.create-event');
    Route::get('cases/getEvents/{id}', [GoogleCalendarController::class, 'getEvents'])->name('google.calendar.get-event');
    Route::post('cases/updateEvent', [GoogleCalendarController::class, 'updateEvent'])->name('google.calendar.update-event');
    Route::post('cases/deleteEvent', [GoogleCalendarController::class, 'deleteEvent'])->name('google.calendar.delete-event');


    
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    Route::post('cases/tasks', [TaskController::class, 'taskStore'])->name('tasks.create');
    Route::get('cases/tasks/edit/{taskId}', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::delete('cases/tasks/destroy/{taskId}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('cases/tasks/show/{taskId}', [TaskController::class, 'show'])->name('tasks.show');

    Route::post('/update-task-status', [TaskController::class, 'updateTaskStatus'])->name('tasks.updateTaskStatus');
    Route::post('/tasks/update-priority/', [TaskController::class, 'updatePriority'])->name('tasks.updatePriority');


    Route::put('cases/tasks/update/{taskId}', [TaskController::class, 'update'])->name('tasks.update');

    Route::post('/tasks/remove-assignee/', [TaskController::class, 'removeAssignee'])->name('task.remove.assignee');

    // case task
    Route::get('cases/tasks/create/{taskId}', [TaskController::class, 'TaskCreateCase'])->name('taskcase.create');

    
    
    
    
    // Route::get('cases/getEvents', 'CalendarController@getEvents');
    Route::resource('cases', CaseController::class);


    // Activities
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    

    // Route::post('cases/{id}/case_docs_update',[CaseController::class, 'case_docs_update'])->name('case_docs_update');

    // Route::post('cases/delete_file/{filename}',[CaseController::class, 'delete_file'])->name('delete_file');

    // Route::post('/cases/emptyFolderAfterDelay',[CaseController::class, 'emptyFolderAfterDelay'])->name('emptyFolderAfterDelay');

    // Route::delete('delete', [TemporaryController::class, '__invoke'])->name('case_docs_delete');
    // Route::post('case_docs_update',[CaseController::class, 'case_docs_update'])->name('case_docs_update');

    


    Route::resource('to-do', ToDoController::class);
    Route::get('to-do/status/{id}', [ToDoController::class, 'status'])->name('to-do.status');
    Route::PUT('to-do/status-update/{id}', [ToDoController::class, 'statusUpdate'])->name('to-do.status.update');

    Route::resource('bills', BillController::class);
    Route::get('bills/addpayment/{bill_id}', [BillController::class,'paymentcreate'])->name('create.payment');
    Route::POST('bills/storepayment/{bill_id}', [BillController::class,'paymentstore'])->name('payment.store');


    Route::resource('taxs', TaxController::class);
    Route::post('taxs/get-tax/', [TaxController::class, 'getTax'])->name('get.tax');

    Route::resource('casediary', DiaryController::class);

    
    Route::POST('timesheet/timeSheetTimerLog', [TimeSheetController::class,'timeSheetTimerLog'])->name('timeSheetTimerLog');


    Route::POST('timesheet/timeSheetTimer', [TimeSheetController::class,'timeSheetTimer'])->name('timeSheetTimer');
    // timeSheetTimerDraft
    Route::POST('timesheet/timeSheetTimerDraft', [TimeSheetController::class,'timeSheetTimerDraft'])->name('timeSheetTimerDraft');
    // deleteDraft
    Route::POST('timesheet/deleteDraft', [TimeSheetController::class,'deleteDraft'])->name('deleteDraft');
    // getCaseTeam
    Route::POST('timesheet/getCaseTeam', [TimeSheetController::class,'getCaseTeam'])->name('timesheet.getCaseTeam');

    Route::get('timesheet/draft', [TimeSheetController::class, 'draftView'])->name('timesheet.draft.view');
    Route::resource('timesheet', TimeSheetController::class);
    Route::post('expenses/file-upload',[ExpenseController::class, 'fileUpload'])->name('expenses.fileUpload');
// deleteExpenseDoc
    Route::post('expenses/deleteExpenseDoc',[ExpenseController::class, 'deleteExpenseDoc'])->name('expenses.deleteExpenseDoc');
    Route::resource('expenses', ExpenseController::class);

    Route::resource('fee-receive', FeeController::class);

    Route::resource('calendar', CalendarController::class);
    // expensesType
    Route::resource('expenses-types', ExpensesTypeController::class);

    
   
    Route::get('documents/get-case-docs/{caseId}',[DocumentController::class, 'getDocs'])->name('ocuments.get-case-docs');

  

    Route::resource('documents', DocumentController::class);

    Route::resource('doctype', DoctypeController::class);

    Route::resource('settings', SettingController::class);
    Route::post('storage-settings',[SettingController::class,'storageSettingStore'])->name('storage.setting.store');
    Route::post('user-access-store', [SettingController::class,'userAccessStore'])->name('user.access.store');


    Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');
    Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language');
    Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data');
    Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language');
    Route::post('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language');
    Route::delete('destroy-language/{lang}', [LanguageController::class, 'destroyLang'])->name('destroy.language');
    Route::post('disable-language',[LanguageController::class,'disableLang'])->name('disablelanguage')->middleware(['auth','XSS']);

    Route::post('cookie-setting', [SettingController::class, 'saveCookieSettings'])->name('cookie.setting');
    Route::post('email-settings', [SettingController::class,'saveEmailSettings'])->name('email.settings');
    Route::any('test', [SettingController::class,'testMail'])->name('test.mail');
    Route::post('test-mail', [SettingController::class,'testSendMail'])->name('test.send.mail');
    Route::post('setting/seo', [SettingController::class, 'SeoSettings'])->name('seo.settings');




    Route::post('recaptcha-settings', [SettingController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store');

    Route::get('plans/create-company', [PlanController::class, 'companyPlan'])->name('plans.create-company');
    

    Route::resource('plans', PlanController::class);

    Route::get('plans/paymentPlan/{code}', [PlanController::class, 'paymentAjax'])->name('paymentAjax');


    Route::get('plans/payment/{code}', [PlanController::class, 'payment'])->name('payment');
    Route::get('plans/upgradePlan/{code}', [PlanController::class, 'upgradePlan'])->name('upgradePlan');
    

    Route::get('system-settings', [SettingController::class, 'adminSettings'])->name('admin.settings');
    Route::post('business-setting', [SettingController::class,'saveBusinessSettings'])->name('business.setting');

    Route::get('plan_request',[PlanRequestController::class,'index'])->name('plan_request.index');

    


    

    Route::get('request_send/{id}', [PlanRequestController::class,'userRequest'])->name('send.request');
    Route::get('request_cancel/{id}', [PlanRequestController::class,'cancelRequest'])->name('request.cancel');

    Route::get('request_response/{id}/{response}', [PlanRequestController::class,'acceptRequest'])->name('response.request');
    Route::PUT('acceptCompany/{id}', [PlanRequestController::class,'acceptCompany'])->name('acceptCompany');

    Route::resource('coupons', CouponController::class);

    Route::get('/orders', [StripePaymentController::class, 'index'])->name('order.index');
    Route::get('/apply-coupon', [CouponController::class,'applyCoupon'])->name('apply.coupon');

    Route::post('plans/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    Route::post('/create-payment-intent', [StripePaymentController::class, 'stripeIntent'])->name('create.payment.intent.post');
    Route::post('/order/success', [StripePaymentController::class, 'orderSuccess'])->name('order.success');

    

    Route::post('plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->name('plan.pay.with.paypal');
    Route::get('{id}/{amount}/plan-get-payment-status', [PaypalController::class, 'planGetPaymentStatus'])->name('plan.get.payment.status');

    Route::post('/plan-pay-with-paystack', [PaystackPaymentController::class, 'planPayWithPaystack'])->name('plan.pay.with.paystack');
    Route::get('/plan/paystack/{pay_id}/{plan_id}/', [PaystackPaymentController::class, 'getPaymentStatus'])->name('plan.paystack');

    Route::post('/plan-pay-with-flaterwave', [FlutterwavePaymentController::class, 'planPayWithFlutterwave'])->name('plan.pay.with.flaterwave');
    Route::get('/plan/flaterwave/{txref}/{plan_id}', [FlutterwavePaymentController::class, 'getPaymentStatus'])->name('plan.flaterwave');

    Route::post('/plan-pay-with-razorpay', [RazorpayPaymentController::class, 'planPayWithRazorpay'])->name('plan.pay.with.razorpay');
    Route::get('/plan/razorpay/{txref}/{plan_id}', [RazorpayPaymentController::class, 'getPaymentStatus'])->name('plan.razorpay');

    Route::post('/plan-pay-with-paytm', 'App\Http\Controllers\PaytmPaymentController@planPayWithPaytm')->name('plan.pay.with.paytm');
    Route::post('/plan/paytm/{plan_id}', [PaytmPaymentController::class, 'getPaymentStatus'])->name('plan.paytm');

    Route::post('/plan-pay-with-mercado', [MercadoPaymentController::class, 'planPayWithMercado'])->name('plan.pay.with.mercado');
    Route::get('/plan/mercado/{plan}/{amount}', [MercadoPaymentController::class, 'getPaymentStatus'])->name('plan.mercado');

    Route::post('/plan-pay-with-mollie', [MolliePaymentController::class, 'planPayWithMollie'])->name('plan.pay.with.mollie');
    Route::get('/plan/mollie/{plan}', [MolliePaymentController::class, 'getPaymentStatus'])->name('plan.mollie');

    Route::post('/plan-pay-with-skrill', [SkrillPaymentController::class, 'planPayWithSkrill'])->name('plan.pay.with.skrill');
    Route::get('/plan/skrill/{plan_id}', [SkrillPaymentController::class, 'getPaymentStatus'])->name('plan.skrill');

    Route::post('/plan-pay-with-coingate', [CoingatePaymentController::class, 'planPayWithCoingate'])->name('plan.pay.with.coingate');
    Route::get('/plan/coingate/{plan}', [CoingatePaymentController::class, 'getPaymentStatus'])->name('plan.coingate');

    Route::post('/planpayment', [PaymentWallController::class, 'planpay'])->name('paymentwall');
    Route::post('/paymentwall-payment/{plan}', [PaymentWallController::class, 'planPayWithPaymentWall'])->name('paymentwall.payment');
    Route::get('/plan/error/{flag}', [PaymentWallController::class, 'planerror'])->name('error.plan.show');

    Route::post('/plan-pay-with-toyyibpay', [ToyyibpayController::class, 'planPayWithToyyibpay'])->name('plan.pay.with.toyyibpay');
    Route::get('/plan-pay-with-toyyibpay/{id}/{amount}/{couponCode?}', [ToyyibpayController::class, 'planGetPaymentStatus'])->name('plan.toyyibpay');

    Route::post('payfast-plan', [PayfastController::class, 'index'])->name('payfast.payment')->middleware(['auth']);
    Route::get('payfast-plan/{success}', [PayfastController::class, 'success'])->name('payfast.payment.success')->middleware(['auth']);

    Route::post('plan-pay-with-bank', [BankTransferController::class, 'planPayWithbank'])->name('plan.pay.with.bank');
    Route::get('orders/show/{id}', [BankTransferController::class, 'show'])->name('order.show');
    Route::delete('/bank_transfer/{order}/', [BankTransferController::class, 'destroy'])->name('bank_transfer.destroy');
    Route::any('order_approve/{id}', [BankTransferController::class, 'orderapprove'])->name('order.approve');
    Route::any('order_reject/{id}', [BankTransferController::class, 'orderreject'])->name('order.reject');

    Route::post('pusher-setting', [SettingController::class, 'savePusherSettings'])->name('pusher.setting');
    Route::get('/advocate/view/{id}', [AdvocateController::class, 'view'])->name('advocate.view');

    Route::post('setting/google-calender', [SettingController::class, 'saveGoogleCalenderSettings'])->name('google.calender.settings');
    Route::post('data/get_all_data', [CalendarController::class, 'get_call_data'])->name('call.get_call_data');

    Route::resource('userlog',UserlogController::class);
    Route::delete('/userlog/{id}/', [UserlogController::class, 'destroy'])->name('userlog.destroy')->middleware(['auth','XSS']);
    Route::get('userlog-view/{id}/', [UserlogController::class, 'view'])->name('userlog.view')->middleware(['auth','XSS']);


    //iyzipay
    Route::post('iyzipay/prepare', [IyziPayController::class, 'initiatePayment'])->name('iyzipay.payment.init');
    Route::post('iyzipay/callback/plan/{id}/{amount}/{coupan_code?}', [IyzipayController::class, 'iyzipayCallback'])->name('iyzipay.payment.callback');

    Route::post('/sspay', [SspayController::class,'SspayPaymentPrepare'])->name('plan.sspaypayment');
    Route::get('sspay-payment-plan/{plan_id}/{amount}/{couponCode}', [SspayController::class, 'SspayPlanGetPayment'])->name('plan.sspay.callback');

    Route::post('plan-pay-with-paytab', [PaytabController::class, 'planPayWithpaytab'])->name('plan.pay.with.paytab');
    Route::any('paytab-success/plan', [PaytabController::class, 'PaytabGetPayment'])->name('plan.paytab.success');

    // Benefit
    Route::any('/payment/initiate', [BenefitPaymentController::class, 'initiatePayment'])->name('benefit.initiate');
    Route::any('call_back', [BenefitPaymentController::class, 'call_back'])->name('benefit.call_back');

    // cashfree
    Route::post('cashfree/payments/', [CashfreeController::class, 'planPayWithcashfree'])->name('plan.pay.with.cashfree');
    Route::any('cashfree/payments/success', [CashfreeController::class, 'getPaymentStatus'])->name('plan.cashfree');

    // Aamarpay
    Route::post('/aamarpay/payment', [AamarpayController::class, 'planPayWithpay'])->name('plan.pay.with.aamarpay');
    Route::any('/aamarpay/success/{data}', [AamarpayController::class, 'getPaymentStatus'])->name('plan.aamarpay');

    Route::resource('country',CountryController::class);
    Route::resource('state',StateController::class);
    Route::resource('city',CityController::class);

    Route::resource('hearingType',HearingTypeController::class);
    Route::get('/hearing/{case_id}', [HearingController::class, 'create'])->name('hearings.create');
    Route::resource('hearing',HearingController::class);

    Route::resource('appointments',AppointmentsController::class);


});

Route::any('/cookie-consent', [SettingController::class, 'CookieConsent'])->name('cookie-consent');

Route::post('payment-setting', [SettingController::class, 'savePaymentSettings'])->name('payment.settings')->middleware(['auth','verified']);
Route::post('admin-payment-setting', [SettingController::class, 'saveAdminPaymentSettings'])->name('admin.payment.settings')->middleware(['auth','verified']);


Route::get('/bills/pay/{bill_id}', [BillController::class, 'payinvoice'])->name('pay.invoice')->middleware(['XSS']);

Route::post('bills/{id}/payment', [StripePaymentController::class, 'addpayment'])->name('invoice.payment')->middleware(['XSS']);

Route::post('bills/{id}/bill-with-paypal', [PaypalController::class,'PayWithPaypal'])->name('bill.with.paypal')->middleware(['XSS']);
Route::get('{id}/get-payment-status/{amount}', [PaypalController::class,'GetPaymentStatus'])->name('get.payment.status')->middleware(['XSS']);

Route::post('/invoice-pay-with-paystack', [PaystackPaymentController::class, 'invoicePayWithPaystack'])->name('invoice.pay.with.paystack')->middleware(['XSS']);
Route::get('/invoice/paystack/{invoice_id}/{amount}/{pay_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paystack')->middleware(['XSS']);

Route::post('/invoice-pay-with-flaterwave', [FlutterwavePaymentController::class, 'invoicePayWithFlutterwave'])->name('invoice.pay.with.flaterwave')->middleware(['XSS']);
Route::get('/invoice/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.flaterwave')->middleware(['XSS']);

Route::post('/invoice-pay-with-razorpay', [RazorpayPaymentController::class, 'invoicePayWithRazorpay'])->name('invoice.pay.with.razorpay')->middleware(['XSS']);
Route::get('/invoice/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.razorpay');

Route::post('/invoice-pay-with-mercado', [MercadoPaymentController::class, 'invoicePayWithMercado'])->middleware(['XSS'])->name('invoice.pay.with.mercado');
Route::any('/invoice/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mercado')->middleware(['XSS']);

Route::post('/invoice-pay-with-paytm', [PaytmPaymentController::class, 'invoicePayWithPaytm'])->middleware(['XSS'])->name('invoice.pay.with.paytm');
Route::post('/invoice/paytm/{invoice}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm')->middleware(['XSS']);

Route::post('/invoice-pay-with-mollie', [MolliePaymentController::class, 'invoicePayWithMollie'])->middleware(['XSS'])->name('invoice.pay.with.mollie');
Route::get('/invoice/mollie/{invoice}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mollie')->middleware(['XSS']);

Route::post('/invoice-pay-with-skrill', [SkrillPaymentController::class, 'invoicePayWithSkrill'])->middleware(['XSS'])->name('invoice.pay.with.skrill');
Route::get('/invoice/skrill/{invoice}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.skrill')->middleware(['XSS']);

Route::post('/invoice-pay-with-coingate', [CoingatePaymentController::class, 'invoicePayWithCoingate'])->middleware(['XSS'])->name('invoice.pay.with.coingate');
Route::get('/invoice/coingate/{invoice}', [CoingatePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.coingate')->middleware(['XSS']);

Route::post('/invoicepayment', [PaymentWallController::class, 'invoicePayWithPaymentwall'])->name('paymentwall.invoice');
Route::post('/invoice-pay-with-paymentwall/{invoice}', [PaymentWallController::class, 'getInvoicePaymentStatus'])->name('invoice-pay-with-paymentwall');
Route::any('/invoice/error/{flag}/{invoice_id}', [PaymentWallController::class, 'invoiceerror'])->name('error.invoice.show');

Route::post('/invoice-with-toyyibpay', [ToyyibpayController::class, 'invoicepaywithtoyyibpay'])->name('invoice.with.toyyibpay');
Route::get('/invoice-toyyibpay-status/{amount}/{invoice_id}', [ToyyibpayController::class, 'invoicetoyyibpaystatus'])->name('invoice.toyyibpay.status');

Route::post('/invoice-with-payfast', [PayfastController::class, 'invoicepaywithpayfast'])->name('invoice.with.payfast');
Route::get('/invoice-payfast-status/{invoice_id}', [PayfastController::class, 'invoicepayfaststatus'])->name('invoice.payfast.status');

Route::any('/pay-with-bank', [BankTransferController::class, 'invoicePayWithbank'])->name('invoice.pay.with.bank');
Route::get('bankpayment/show/{id}', [BankTransferController::class, 'bankpaymentshow'])->name('bankpayment.show');
Route::delete('invoice/bankpayment/{id}/delete', [BankTransferController::class, 'invoicebankPaymentDestroy'])->name('invoice.bankpayment.delete');
Route::post('/invoice/status/{id}', [BankTransferController::class, 'invoicebankstatus'])->name('invoice.status');

Route::post('/invoice-with-iyzipay', [IyziPayController::class, 'invoicepaywithiyzipay'])->name('invoice.with.iyzipay');
Route::post('/invoice-iyzipay-status/{invoice_id}/{amount}', [IyziPayController::class, 'invoiceiyzipaystatus'])->name('invoice.iyzipay.status');

Route::post('/customer-pay-with-sspay', [SspayController::class,'invoicepaywithsspaypay'])->name('customer.pay.with.sspay');
Route::get('/customer/sspay/{invoice}/{amount}', [SspayController::class,'getInvoicePaymentStatus'])->name('customer.sspay');

Route::post('invoice-with-paytab/', [PaytabController::class, 'invoicePayWithpaytab'])->name('pay.with.paytab');
Route::any('invoice-paytab-status/{invoice}/{amount}', [PaytabController::class, 'PaytabGetPaymentCallback'])->name('invoice.paytab.status');

Route::post('invoice-with-benefit/', [BenefitPaymentController::class, 'invoicePayWithbenefit'])->name('pay.with.paytab');
Route::any('invoice-benefit-status/{invoice_id}/{amount}', [BenefitPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.benefit.status');


// cashfree
Route::post('invoice-with-cashfree/', [CashfreeController::class, 'invoicePayWithcashfree'])->name('pay.with.cashfree');
Route::any('invoice-cashfree-status/', [CashfreeController::class, 'getInvociePaymentStatus'])->name('invoice.cashfree.status');


Route::post('invoice-with-aamarpay/', [AamarpayController::class, 'invoicePayWithaamarpay'])->name('pay.with.aamarpay');
Route::any('invoice-aamarpay-status/{data}', [AamarpayController::class, 'getInvociePaymentStatus'])->name('invoice.aamarpay.status');


// encryption
Route::get('/form-encryption', [FormEncryptionController::class, 'index'])->name('form.encryption.index');
Route::post('/form-encryption', [FormEncryptionController::class, 'store'])->name('form.encryption.store');
