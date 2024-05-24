<?php

namespace App\Http\Controllers;

use App\Events\CaseNotification;
use App\Events\CaseNotificationEvent;
use App\Models\Advocate;
use App\Models\Cases;
use App\Models\Court;
use App\Models\Hearing;
use App\Models\HearingType;
use App\Models\FolderActivity;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Utility;
use App\Models\CalendarEvent;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Google\Http\REST;
use Illuminate\Support\Facades\Storage;
use Google\Client as Google_Client;

use DateTimeZone;
use Google\Service\Calendar;
use Google_Service_Calendar;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
use Google_Service_Calendar_Event;
//use Google_Client;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
// mail
use Illuminate\Support\Facades\Mail;
use App\Mail\UserNotification;
use App\Models\CaseDocument;
use App\Models\CasesSlipFall;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\group;
use App\Models\TaskData;
use App\Models\Task;
use App\Models\FormEncryption;
use App\Models\States;
use App\Models\subTask;
use App\Models\SubTaskLog;
use App\Models\TaskLog;
use App\Models\Activity;
use Illuminate\Support\Facades\Crypt;
// log
use Illuminate\Support\Facades\Log;
//  Defuse\Crypto\Crypto,Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Illuminate\Support\Facades\Http;
use League\ISO3166\ISO3166;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\fileExists;
use App\Traits\TaskTrait;
use App\Traits\CaseTrait;



class CaseController extends Controller
{
    use TaskTrait;
    use CaseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public $formFieldsPremissions = array();
    public function __construct()
    {
        
        // Retrieve and decode Motor Vehicle Accident form data
        $motorVehicleFormData = FormEncryption::where('form_name', 'Motor Vehicle Accident')->value('form_array');
        if ($motorVehicleFormData) {
            $motorVehicleFormData = json_decode($motorVehicleFormData, true);
            $this->formFieldsPremissions = array_merge($this->formFieldsPremissions, $motorVehicleFormData);
        }

        // Retrieve and decode Slip & Fall form data
        $slipAndFallFormData = FormEncryption::where('form_name', 'Slip & Fall')->value('form_array');
        if ($slipAndFallFormData) {
            $slipAndFallFormData = json_decode($slipAndFallFormData, true);
            $this->formFieldsPremissions = array_merge($this->formFieldsPremissions, $slipAndFallFormData);
        }
    }

    public function index()
    {
       if (Auth::user()->can('manage case')) {

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
                $user = Auth::user();
                // $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $cases = Cases::whereIn('created_by', $userIds)
                    ->where('draft', 0)
                    ->orderByDesc('id')
                    ->get();
            } else {
                if (Auth::user()->type !== 'client' && Utility::getValByName('viewCases') === 'all') {
                    $cases = Cases::where('draft', 0)
                        ->when(Auth::user()->creatorId(), function ($query, $creatorId) {
                            return $query->whereIn('created_by', [intval($creatorId)]);
                        })
                        ->orderByDesc('id')
                        ->get();
                } else {
                    $user = Auth::user()->id;
                    $cases = Cases::where('draft', 0)
                        ->where(function ($query) use ($user) {
                            $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                                ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                        })
                        ->orderByDesc('id')
                        ->get();
                }                
                
            }
            $fieldsToUpdate = config('form-fields.fieldsToUpdate');
            // cases[$fieldsToUpdate each] decrpty
            foreach ($cases as $case) {
                foreach ($fieldsToUpdate as $field) {
                    if ($field == 'court') {
                        $case->$field = $case->$field != '' ? $case->$field : 1;
                    } else {
                        $case->$field = $this->decryptAES($case->$field);
                    }
                }
            }

            return view('cases.index', compact('cases'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function draftView()
    {
        if (Auth::user()->can('manage case')) {

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $cases = Cases::whereIn('created_by', $userIds)
                    ->where('draft', 1)
                    ->orderByDesc('id')
                    ->get();
            } else {

                $user = Auth::user()->id;

                $cases = DB::table("cases")
                    ->select("cases.*")
                    ->where(function ($query) use ($user) {
                        $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                            ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                    })
                    ->where('draft', 1)
                    ->orderBy('id', 'DESC')
                    ->get();
            }

            $fieldsToUpdate = config('form-fields.fieldsToUpdate');
            // cases[$fieldsToUpdate each] decrpty
            $secretKey = env('AES_Secret_Key_DB');
            $IV = env('AES_IV_Key_DB');
            foreach ($cases as $case) {
                foreach ($fieldsToUpdate as $field) {
                    if ($field == 'court') {
                        $case->$field = $case->$field != '' ? $case->$field : 1;
                    } else {
                        $case->$field = $this->decryptAES($case->$field, $secretKey, $IV);
                    }
                }
            }
            return view('cases.draft', compact('cases'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function emptyFolderAfterDelay(Request $request)
    {
        $folder = Auth::user()->creatorId() . '-' . 'case-doc';
        $destinationPath = public_path('storage/uploads/case_docs/tmp/' . $folder);
        if (File::exists($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }
    }

    public function caseDoc(Request $request)
    {
        $folder_name = Auth::user()->creatorId() . '-' . 'case-doc';

        foreach ($request->input('folders', []) as $folderIndex => $folder) {

            foreach ($folder['folder_doc'] ?? [] as $docIndex => $document) {

                foreach ($document['files'] ?? [] as $fileIndex => $file) {

                    $uploadedFile = $request->file("folders.$folderIndex.folder_doc.$docIndex.files.$fileIndex");

                    if ($uploadedFile) {

                        $destinationPath = public_path('storage/uploads/case_docs/tmp/' . $folder_name);
                        $fileName = time() . '.' . $uploadedFile->getClientOriginalName();
                        $uploadedFile->move($destinationPath, $fileName);
                    
                        $encrypt = $this->encryptFile($destinationPath.'/'.$fileName);
                        // dd($encrypt,$destinationPath.'/'.$fileName);
                    }
                }
            }
        }


        return response()->json([
            'success' => true,
            'fileUrl' =>  $fileName,
        ]);
    }

    public function caseDocOld(Request $request)
    {

        $folder_name = Auth::user()->creatorId() . '-' . 'case-doc';

        foreach ($request->input('folders', []) as $folderIndex => $folder) {

            foreach ($folder['folder_doc'] ?? [] as $docIndex => $document) {

                foreach ($document['files'] ?? [] as $fileIndex => $file) {

                    $uploadedFile = $request->file("folders.$folderIndex.folder_doc.$docIndex.files.$fileIndex");
                    if ($uploadedFile) {

                        $sourceFolderPath = public_path('storage/uploads/case_docs/' . $folder_name);
                        $destinationFolderPath = public_path('storage/uploads/case_docs/tmp/' . $folder_name);


                        if (File::exists($sourceFolderPath)) {

                            File::makeDirectory($destinationFolderPath, 0755, true, true);

                            $originalFileName = $uploadedFile->getClientOriginalName();
                            $sourceFilePath = $sourceFolderPath . '/' . $originalFileName;
                            $destinationFilePath = $destinationFolderPath . '/' . $originalFileName;

                            // Copy the file
                            File::copy($sourceFilePath, $destinationFilePath);
                        } else {
                        }
                    }
                }
            }
        }


        return response()->json([
            'success' => true,
            'fileUrl' =>  $originalFileName,
        ]);
    }

    public function delete_file($fileName)
    {

        $filePath = public_path("storage/uploads/case_docs/{$fileName}");

        if (file_exists($filePath)) {
            unlink($filePath);
            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['message' => 'File not found'], 404);
    }


    public function case_docs_update(Request $request)
    {

        $folder = Auth::user()->creatorId() . '-' . 'case-doc';
        if ($request->hasFile('filepond')) {

            $file = $request->file('filepond');
            $fileNameToStore = $file->getClientOriginalName();
            $destinationPath = public_path('storage/uploads/case_docs/tmp/' . $folder);

            $file->move($destinationPath, $fileNameToStore);
            return $fileNameToStore;
        }

        return '';
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {


        if (Auth::user()->can('create case')) {

            $timeLine = FolderActivity::create([
                'user_id' => Auth::user()->id,
                'start_time' => now(),
            ]);

            $courts = Court::where('created_by', Auth::user()->creatorId())->pluck('name', 'id')->prepend('Please Select', '');
            $advocates = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'client')->pluck('name', 'id');
            $groups = group::where('created_by', Auth::user()->creatorId())->pluck('name', 'members', 'id');
            $allOptions = $advocates->union($groups);
          
            $ip = $request->ip();
            $apiResponse = Http::get("http://ipinfo.io/{$ip}/json");
            // dd($apiResponse);
            $locationData = $apiResponse->json();
            if(isset($locationData['country'])){
                $countryCode = $locationData['country'];
    
            }else{
                $countryCode = 'US';
            }
            $iso3166 = new ISO3166();
           
            $countryInfo = $iso3166->alpha2($countryCode);
            $selectedCountry = $countryInfo['name'] ?? '';
            $selectedState = $locationData['region'] ?? 'Alabama';
            $selectedCity = $locationData['city'] ?? '';
          
            $countryTimezones = $this->getTimezonesForCountry($countryCode);
            
            $timezones = Timezone::whereIn('timezone', $countryTimezones)->get();

            if ($selectedCountry == "United States of America") {
                $selectC = Countries::where('name', 'United States')->select('id', 'name')->first();
            } else {
                $selectC = Countries::where('name', $selectedCountry)->select('id', 'name')->first();
            }
            $selectedCountry = $selectC->name;
            $selectS = States::where('country_id',$selectC->id)->where('name',$selectedState)->first('id');
    
            $cities = Cities::where('country_id',$selectC->id)->where('state_id',$selectS->id)->get();
          
            $countries = Countries::where('name',$selectC->name)->get();
            
            // $countries = Countries::all();
            $states = States::where('country_id',$selectC->id)->get();
            

            $roles = Role::where('created_by', Auth::user()->creatorId())->where('id', '!=', Auth::user()->id)->where('name', '!=', 'Advocate')->get()->pluck('name', 'id');
            

            $caseCount = Cases::where('created_by', Auth::user()->creatorId())->count();


            $team = User::where('created_by', Auth::user()->creatorId())->where('type', '=', 'client')->pluck('name', 'id');
           
            $HearingType = HearingType::where('created_by', Auth::user()->creatorId())->pluck('type', 'id');
            // session()->forget('case_form_activity_created');
            return view('cases.create', compact('courts', 'allOptions', 'advocates', 'team', 'HearingType', 'timeLine','caseCount','roles', 'timezones', 'selectedCountry', 'selectedState', 'selectedCity', 'countries', 'states','cities'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    function getTimezonesForCountry($countryCode)
    {
        $countryCode = strtoupper($countryCode);

        $allTimezones = DateTimeZone::listIdentifiers();
        $countryTimezones = [];

        foreach ($allTimezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezoneCountryCode = $tz->getLocation()['country_code'];

            if ($timezoneCountryCode === $countryCode) {
                $countryTimezones[] = $timezone;
            }
        }

        return $countryTimezones;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $your_advocates = $request->your_advocates;

        $uniqueValues = [];
        if (isset($your_advocates)) {
            foreach ($your_advocates as $element) {
                $values = explode(",", $element);

                $uniqueValues = array_merge($uniqueValues, $values);
            }

            $uniqueValues = array_unique($uniqueValues);

            $uniqueValues = array_values($uniqueValues);
        }

        if (Auth::user()->can('create case') ) {


            $opponentData = [];

            $numOpponents = count($request->opponents['opponents_name']);

            for ($i = 0; $i < $numOpponents; $i++) {
                // Create a new opponent entry with the desired structure.
                $opponent = [
                    'opponents_name' => $request->opponents['opponents_name'][$i],
                    'opponents_email' => $request->opponents['opponents_email'][$i],
                    'opponents_phone' => $request->opponents['opponents_phone'][$i],
                ];

                // Add the opponent to the transformed data array.
                $opponentData[] = $opponent;
            }

            $opponentAdvocateData = [];

            $numOpponentAdvocate = count($request->opponent_advocates['opp_advocates_name']);

            for ($i = 0; $i < $numOpponentAdvocate; $i++) {
                // Create a new opponent entry with the desired structure.
                $opponent = [
                    'opp_advocates_name' => $request->opponent_advocates['opp_advocates_name'][$i],
                    'opp_advocates_email' => $request->opponent_advocates['opp_advocates_email'][$i],
                    'opp_advocates_phone' => $request->opponent_advocates['opp_advocates_phone'][$i],
                ];

                // Add the opponent to the transformed data array.
                $opponentAdvocateData[] = $opponent;
            }



            $inputDateString = $request->open_date;
            $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

            if (preg_match($dateFormatRegex, $inputDateString)) {
                $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
                $formattedDate = $dateObject->format('Y-m-d');
                $convertedDate_openDate = $formattedDate;
            } else {
                $convertedDate_openDate = $inputDateString;
            }


            $convertedDate_closeDate = Null;

            if ($request->close_date) {

                if (preg_match($dateFormatRegex, $request->close_date)) {
                    $dateObject = Carbon::createFromFormat('D M j Y', $request->close_date);
                    $formattedDate = $dateObject->format('Y-m-d');
                    $convertedDate_closeDate = $formattedDate;
                } else {
                    $convertedDate_closeDate = $request->close_date;
                }
            } else {
                $convertedDate_closeDate = Null;
            }

            $convertedDate_incidentDate = Null;
            if ($request->incident_date) {

                if (preg_match($dateFormatRegex, $request->incident_date)) {
                    $dateObject = Carbon::createFromFormat('D M j Y', $request->incident_date);
                    $formattedDate = $dateObject->format('Y-m-d');
                    $convertedDate_incidentDate = $formattedDate;
                } else {

                    $convertedDate_incidentDate = $request->incident_date;
                }
            } else {
                $convertedDate_incidentDate = NUll;
            }


            if ($request->caseId) {
                $case = Cases::findOrFail($request->caseId);
            } else {
                $case = new Cases();
            }

            $fieldsToUpdate = config('form-fields.fieldsToUpdate');
            // dd($request->all());
            foreach ($fieldsToUpdate as $field) {
                if (isset($this->formFieldsPremissions[$field]) && !empty($request->$field) && $this->formFieldsPremissions[$field] == 'Yes') {
                    $case[$field] = ($field == 'court') ? 1 : $this->encryptAES($request->$field);
                } else {
                    $case[$field] = ($field == 'court') ? 1 : $request->$field;
                }
            }
            // practice_area
            $case['practice_area']  = $request->practice_area;
            // open_date,close_date,filing_date,incident_date,first_party_customer_type,first_party_seat_belts_worn,third_party_customer_type,third_party_airbags_developed,third_party_seat_belts_worn,first_party_airbags_developed
            $case['open_date'] = $request->open_date;
            $case['close_date'] = $request->close_date;
            $case['filing_date'] = $request->filing_date;
            $case['incident_date'] = $request->incident_date;
            $case['first_party_customer_type'] = $request->first_party_customer_type;
            $case['first_party_airbags_developed'] = $request->first_party_airbags_developed;
            $case['first_party_seat_belts_worn'] = $request->first_party_seat_belts_worn;
            $case['third_party_customer_type'] = $request->third_party_customer_type;
            $case['third_party_airbags_developed'] = $request->third_party_airbags_developed;
            $case['third_party_seat_belts_worn'] = $request->third_party_seat_belts_worn;

            // opponent_advocates,opponents
            $case['opponents'] = json_encode($opponentData);
            $case['opponent_advocates'] = json_encode($opponentAdvocateData);
            $case['your_advocates'] = $uniqueValues ? implode(',', $uniqueValues) ?? '' : null;

            $case['your_team'] = $request->your_team ? implode(',', $request->your_team) ?? '' : null;


            $case['created_by'] = Auth::user()->creatorId();
            $case['draft'] = 0;


            $folder_name_old = Auth::user()->creatorId() . '-' . 'case-doc';
            $destinationPath = public_path('storage/uploads/case_docs/' . $folder_name_old);

            $caseDocsData = $request->folders;
            $savedData = [];

            if ($caseDocsData) {
                foreach ($caseDocsData as $index => $caseDoc) {



                    $folder_name = $caseDoc['folder_name'] ?? '';
                    $folder_description = $caseDoc['folder_description'] ?? '';


                    $docData = [];
                    if (isset($caseDoc['folder_doc'])) {

                        foreach ($caseDoc['folder_doc'] as $ind => $docFile) {


                            $doc_name = $docFile['doc_name'] ?? '';
                            $doc_des = $docFile['doc_description'] ?? '';


                            $files = [];
                            if (isset($docFile['files'])) {

                                $doc_files = $docFile['files'] ?? '';
                                foreach ($doc_files as $file) {

                                    $sourcePath = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old . '/' . $file);

                                    $destinationFile = $destinationPath . '/' . $file;
                                    if (!File::exists($destinationPath)) {
                                        File::makeDirectory($destinationPath, 0755, true);
                                    }

                                    if (File::exists($sourcePath)) {
                                        File::move($sourcePath, $destinationFile);
                                    }

                                    $files[] = $folder_name_old . '/' . $file;
                                }
                            }

                            $docData[] = [
                                'doc_name' => $doc_name,
                                'doc_des' => $doc_des,
                                'files' => $files,
                                'uploaded_by' => Auth::user()->id,
                                'uploaded_at' => now(),
                            ];
                        }
                    }

                    $savedData[] = [
                        'folder_name' => $folder_name,
                        'folder_description' => $folder_description,
                        'docData' => $docData,
                    ];
                }
            }
          

            // $case['case_docs'] = !empty($file_name) ? implode(',',$file_name) : '';
            $case['case_docs'] =  json_encode($savedData)  ?? '';
            $case->save();


            $sourcePathDelete = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old);
            if (File::exists($sourcePathDelete)) {
                File::deleteDirectory($sourcePathDelete);
            }

            // Activity log create
            if(isset($files)){

                $fileNames = implode(', ', $files);

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Created',
                    'file' => $fileNames,
                ]);

            }else{

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Created',
                ]);

            }




            if ($request->practice_area == 'Slip & Fall') {
                $this->FallSlipCreate($case->id, $request, $case);
            } else {

                $caseData = $case->toArray();

                $ignoredFields = [
                    'created_by', 'updated_at', 'created_at', 'id',
                    'court', 'highcourt', 'bench', 'section', 'priority',
                    'under_acts', 'under_sections', 'FIR_police_station'
                ];

                $nonNullFields = [];

          

                foreach ($caseData as $field => $value) {
                    if (!in_array($field, $ignoredFields) && !empty($value)) {

                        if ($field === 'opponents' || $field === 'opponent_advocates') {
                            $decodedValue = json_decode($value, true);
                            if (!empty($decodedValue) && array_filter($decodedValue[0], 'is_null') !== $decodedValue[0]) {
                                $nonNullFields[] = $field;
                            }
                        } elseif ($field === 'case_docs') {
                            $decodedValue = json_decode($value, true);
                            foreach ($decodedValue as $folder) {
                                $nonNullFields[] = 'Folder Name: ' . $folder['folder_name'];
                                foreach ($folder['docData'] as $doc) {
                                    foreach ($doc['files'] as $file) {
                                        $cleanedFile = basename($file);
                                        $nonNullFields[] = 'File: ' . $cleanedFile;
                                    }
                                }
                            }
                        } else {
                            $nonNullFields[] = $field;
                        }
                    }
                }

                $updatedFieldsString = implode(', ', $nonNullFields);


                $activity =  FolderActivity::findOrFail($request->timeLineId);
                if ($activity) {
                    $activity->case_id = $case->id;
                    $activity->log = $case->name . ' Case was created:';
                    $activity->end_time = now();
                    $activity->edit_case = $updatedFieldsString;

                    $activity->save();
                }
            }

            // send mail
            $authUser = User::find(Auth::user()->id);
            $data = [
                'name' => $authUser->name,
                'caseName' => $case->name,
                'addedBy' => $authUser->name,
            ];
            $ccEmails = [];
            $ccUser = User::find($authUser->created_by);
            while ($ccUser && $ccUser->created_by) {
                array_unshift($ccEmails, $ccUser->email);
                $ccUser = User::find($ccUser->created_by);
            }
            $ccEmailsList = array_unique($ccEmails);

            try {
                Mail::to($authUser->email)->send((new UserNotification)->cc($ccEmailsList)->caseAdded($data));
            } catch (\Exception $e) {
            }

            
            // send notification

            $Notify = Auth::user();
           
            $yourAdvocates = explode(',', $case->your_advocates);
            $yourTeam = explode(',', $case->your_team);

            // Merge the arrays
            $userIds = array_merge($yourAdvocates, $yourTeam);

            $key = array_search($Notify->id, $userIds);

            if ($key !== false) {
                unset($userIds[$key]);
            }


            if($Notify->id != $case->created_by){
                $userIds[] = $case->created_by;
            }
           

            $message = "A new case has been created: '{$case->name}', which is related to you by '{$Notify->name}'";


            $notificationData = [
                'message' => $message,
                'type' => 'case',
                'target_id' => $case->id,
            ];
            
            foreach ($userIds as $userId) {
                if ($userId !== null && is_numeric($userId)) {
                    $notificationData['user_id'] = $userId;
                    event(new CaseNotification($notificationData));
                }
            }


            return redirect()->route('cases.index')->with('success', __('Case successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function FallSlipCreate($caseId, $request, $case)
    {



        $allWitness = [];

        $numOpponents = count($request->witness['witness_name']);

        for ($i = 0; $i < $numOpponents; $i++) {

            $witness = [
                'witness_name' => $request->witness['witness_name'][$i],
                'witness_email' => $request->witness['witness_email'][$i],
                'witness_phone' => $request->witness['witness_phone'][$i],
                'witness_address' => $request->witness['witness_address'][$i],
            ];

            $allWitness[] = $witness;
        }


        $inputDateString = $request->fall_c_dob;
        $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

        if (preg_match($dateFormatRegex, $inputDateString)) {
            $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
            $formattedDate = $dateObject->format('Y-m-d');
            $fallClientDob = $formattedDate;
        } else {
            $fallClientDob = $inputDateString;
        }

        $incidentDate = $request->fall_ii_incident_date;
        if (preg_match($dateFormatRegex, $incidentDate)) {
            $dateObject = Carbon::createFromFormat('D M j Y', $incidentDate);
            $formattedDate = $dateObject->format('Y-m-d');
            $fall_ii_incident_date = $formattedDate;
        } else {
            $fall_ii_incident_date = $incidentDate;
        }





        $fallSlip = new CasesSlipFall();

        $fallSlip['case_id'] = $caseId;
        $fieldsToUpdate = config('form-fields.fallSlip');
        // dd($request->all());
        foreach ($fieldsToUpdate as $field) {
            if (isset($this->formFieldsPremissions[$field]) && $this->formFieldsPremissions[$field] == 'Yes') {
                $fallSlip[$field] = $this->encryptAES($request->$field);
            } else {
                $fallSlip[$field] = $request->$field;
            }
        }


        // fall_ii_time,fall_c_health_insurance,fc_police_notified,fc_incident_report_filed,fall_o_recorded_statements,fall_ii_incident_date
        $fallSlip['fall_ii_time'] = $request->fall_ii_time;
        $fallSlip['fall_c_health_insurance'] = $request->fall_c_health_insurance;
        $fallSlip['fc_police_notified'] = $request->fc_police_notified;
        $fallSlip['fc_incident_report_filed'] = $request->fc_incident_report_filed;
        $fallSlip['fall_o_recorded_statements'] = $request->fall_o_recorded_statements;
        $fallSlip['fall_ii_incident_date'] = $fall_ii_incident_date;



        $fallSlip['witness'] = json_encode($allWitness);
        // dd($fallSlip);
        $fallSlip->save();

        $caseData = $fallSlip->toArray();


        $caseValues = $case->only([
            'name', 'case_number', 'open_date', 'close_date', 'case_stage', 'practice_area', 'statute_of_limitations', 'case_docs', 'your_advocates', 'your_team', 'description'
        ]);

        $caseData = array_merge($caseData, $caseValues);


        $ignoredFields = [
            'created_by', 'updated_at', 'created_at', 'id',
            'court', 'highcourt', 'bench', 'section', 'priority',
            'under_acts', 'under_sections', 'FIR_police_station'
        ];

        $nonNullFields = [];

        foreach ($caseData as $field => $value) {
            if (!in_array($field, $ignoredFields) && !is_null($value)) {

                if ($field === 'witness') {
                    $decodedValue = json_decode($value, true);
                    if (!empty($decodedValue) && array_filter($decodedValue[0], 'is_null') !== $decodedValue[0]) {
                        $nonNullFields[] = $field;
                    }
                } elseif ($field === 'case_docs') {
                    $decodedValue = json_decode($value, true);
                    foreach ($decodedValue as $folder) {
                        $nonNullFields[] = 'Folder Name: ' . $folder['folder_name'];
                        foreach ($folder['docData'] as $doc) {
                            foreach ($doc['files'] as $file) {
                                $cleanedFile = basename($file);
                                $nonNullFields[] = 'File: ' . $cleanedFile;
                            }
                        }
                    }
                } else {
                    $nonNullFields[] = $field;
                }
            }
        }

        $updatedFieldsString = implode(', ', $nonNullFields);


        $activity =  FolderActivity::findOrFail($request->timeLineId);
        if ($activity) {
            $activity->case_id = $case->id;
            $activity->log = $case->name . ' Case was created:';
            $activity->end_time = now();
            $activity->edit_case = $updatedFieldsString;

            $activity->save();
        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {


        $tab = $request->query('tab') ?? 'data';
        if (Auth::user()->can('view case') && $this->isAssignedCase($id)) {

            $case_type = Cases::withTrashed()->where('id', $id)->first('practice_area');

            if ($case_type->practice_area == 'Slip & Fall') {

                $case = Cases::with(['taskData.tasks', 'fallSlip'])->find($id);

                $statusFilters = ['Not Started Yet', 'Incomplete', 'In Progress', 'Completed'];

                $taskDataByStatus = [];
                $userId = Auth::user()->id;
                foreach ($statusFilters as $status) {

                    $taskData = TaskData::where('status', $status)
                    ->where('cases_id', $id)
                    ->where('deleted_at',null)
                    ->where(function ($query) use ($userId) {
                        $query->whereHas('associatedCase', function ($caseQuery) use ($userId) {
                            $caseQuery->whereRaw("(find_in_set('" . $userId . "', your_team) OR find_in_set('" . $userId . "', your_advocates))");
                        })
                        ->orWhere('created_by', $userId);
                    })
                    ->with('tasks')
                    
                    ->get();
                   
                    $taskDataByStatus[$status] = $taskData;
                }

              


                $documents = [];
                if (!empty($case->case_docs)) {
                    $documents = explode(',', $case->case_docs);
                }
                $hearings = Hearing::where('case_id', $id)->get();


                $user_id =   Auth::user()->creatorId();

                $userDetails = auth()->user()->userDetails()->with('timezoneTable')->first();
                $timezoneName = $userDetails->timezoneTable->timezone ?? 'UTC';
                $utc_offset = $userDetails->timezoneTable->utc_offset ?? '';

                if ($timezoneName == null) {
                    $timezoneName = 'UTC';
                }

                $dataByDate = FolderActivity::with('user')
                    ->where('case_id', $id)
                    ->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->orderBy('start_time', 'desc')
                    ->get();

                $timelines = [];


                foreach ($dataByDate as $timeline) {
                    // dd($timeline->start_time );
                    $timeline->start_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeline->start_time, 'UTC')
                        ->setTimezone($timezoneName);
                    $timeline->end_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeline->end_time, 'UTC')
                        ->setTimezone($timezoneName);

                    $startDate = $timeline->start_time->format('Y-m-d'); // Convert and format the date part
                    if (!isset($timelines[$startDate])) {
                        $timelines[$startDate] = [];
                    }
                    $timelines[$startDate][] = $timeline;
                }
                $fallSlip = config('form-fields.fallSlip');
                foreach ($fallSlip as $field) {
                    if(isset($case->fallSlip[$field])){
                        $case->fallSlip[$field] = $this->decryptAES($case->fallSlip[$field]);
                    }
                }

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Viewed',
                ]);


                return view('cases.Fallslipview', compact('case', 'taskDataByStatus', 'documents', 'hearings', 'timelines', 'tab', 'timezoneName', 'utc_offset'));
            } else {

                $case = Cases::withTrashed()->with('taskData.tasks')->find($id);
                $statusFilters = ['Not Started Yet', 'Incomplete', 'In Progress', 'Completed'];

                $taskDataByStatus = [];
                $userId = Auth::user()->id;
                foreach ($statusFilters as $status) {
                    $query = TaskData::where('status', $status)->where('cases_id', $id)->where('deleted_at',null);

                    if (Utility::getValByName('viewTasks') !== 'all') {
                        $query->where(function ($subquery) use ($userId) {
                            $subquery->whereHas('associatedCase', function ($caseQuery) use ($userId) {
                                $caseQuery->whereRaw("(find_in_set('" . $userId . "', your_team) OR find_in_set('" . $userId . "', your_advocates))");
                            })
                            ->orWhere('created_by', $userId);
                        });
                    }

                    $taskData = $query->with('tasks')->get();

                    $taskDataByStatus[$status] = $taskData;
                }



                $documents = [];
                if (!empty($case->case_docs)) {
                    $documents = explode(',', $case->case_docs);
                }
                $hearings = Hearing::where('case_id', $id)->get();


                $user_id =   Auth::user()->creatorId();

                $userDetails = auth()->user()->userDetails()->with('timezoneTable')->first();
                $timezoneName = $userDetails->timezoneTable->timezone ?? 'UTC';
                $utc_offset = $userDetails->timezoneTable->utc_offset ?? '';

                if ($timezoneName == null) {
                    $timezoneName = 'UTC';
                }

                $dataByDate = FolderActivity::with('user')
                    ->where('case_id', $id)
                    ->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->orderBy('start_time', 'desc')
                    ->get();

                $timelines = [];


                foreach ($dataByDate as $timeline) {
                    // dd($timeline->start_time );
                    $timeline->start_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeline->start_time, 'UTC')
                        ->setTimezone($timezoneName);
                    $timeline->end_time = Carbon::createFromFormat('Y-m-d H:i:s', $timeline->end_time, 'UTC')
                        ->setTimezone($timezoneName);

                    $startDate = $timeline->start_time->format('Y-m-d'); // Convert and format the date part
                    if (!isset($timelines[$startDate])) {
                        $timelines[$startDate] = [];
                    }
                    $timelines[$startDate][] = $timeline;
                }
                $fieldsToUpdate = config('form-fields.fieldsToUpdate');
                // cases[$fieldsToUpdate each] decrpty
                // dd($case);
                foreach ($fieldsToUpdate as $field) {
                    if ($field == 'court') {
                        $case[$field] = $case->$field != '' ? $case->$field : 1;
                    } else {
                        $case[$field] = $this->decryptAES($case->$field);
                    }
                }

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Viewed',
                ]);

                return view('cases.view', compact('case', 'taskDataByStatus', 'documents', 'hearings', 'timelines', 'tab', 'timezoneName', 'utc_offset'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if (Auth::user()->can('edit case') && $this->isAssignedCase($id)) {


            // CREATE CLIENT DATA
            $ip = $request->ip() ?? '127.0.0.1';
            $apiResponse = Http::get("http://ipinfo.io/{$ip}/json");
            // dd($apiResponse);
            $locationData = $apiResponse->json();
            if(isset($locationData['country'])){
                $countryCode = $locationData['country'];
    
            }else{
                $countryCode = 'US';
            }
            $iso3166 = new ISO3166();
           
            $countryInfo = $iso3166->alpha2($countryCode);
            $selectedCountry = $countryInfo['name'] ?? '';
            $selectedState = $locationData['region'] ?? 'Alabama';
            $selectedCity = $locationData['city'] ?? '';
          
            $countryTimezones = $this->getTimezonesForCountry($countryCode);
            
            $timezones = Timezone::whereIn('timezone', $countryTimezones)->get();

            if ($selectedCountry == "United States of America") {
                $selectC = Countries::where('name', 'United States')->select('id', 'name')->first();
            } else {
                $selectC = Countries::where('name', $selectedCountry)->select('id', 'name')->first();
            }
            $selectedCountry = $selectC->name;
            $selectS = States::where('country_id',$selectC->id)->where('name',$selectedState)->first('id');
    
            $cities = Cities::where('country_id',$selectC->id)->where('state_id',$selectS->id)->get();
          
            $countries = Countries::where('name',$selectC->name)->get();
            
            // $countries = Countries::all();
            $states = States::where('country_id',$selectC->id)->get();
            

            $roles = Role::where('created_by', Auth::user()->creatorId())->where('id', '!=', Auth::user()->id)->where('name', '!=', 'Advocate')->get()->pluck('name', 'id');
            

            $caseCount = Cases::where('created_by', Auth::user()->creatorId())->count();


            $team = User::where('created_by', Auth::user()->creatorId())->where('type', '=', 'client')->pluck('name', 'id');
           






            $courts = Court::where('created_by', Auth::user()->creatorId())->pluck('name', 'id')->prepend('Please Select', '');
            // $advocates = User::where('created_by', Auth::user()->creatorId())->where('type', 'advocate')->pluck('name', 'id');
            // $team = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'advocate')->pluck('name', 'id');

            $advocates = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'client')->pluck('name', 'id');
            $groups = group::where('created_by', Auth::user()->creatorId())->pluck('name', 'members', 'id');
            $allOptions = $advocates->union($groups);
            $allOptions = $allOptions->sortKeys();



            $case_type = Cases::where('id', $id)->first('practice_area');

            if ($case_type->practice_area == 'Slip & Fall') {

                $case = Cases::with(['fallSlip'])->find($id);
            } else {
                $case = Cases::find($id);
            }
            $your_advocates = User::where('created_by', Auth::user()->creatorId())->whereIn('id', explode(',', $case->your_advocates))->get();
            $your_team = User::where('created_by', Auth::user()->creatorId())->whereIn('id', explode(',', $case->your_team))->get();




            $priorities = ['Super Critical' => 'Super Critical', 'Critical' => 'Critical', 'Important' => 'Important', 'Routine' => 'Routine', 'Normal' => 'Normal'];

            $documents = [];
            if (!empty($case->case_docs)) {
                $documents = explode(',', $case->case_docs);
            }
            $case_typ = Cases::caseType();


            $timeLine = FolderActivity::create([
                'user_id' => Auth::user()->id,
                'case_id' => $id,
                'log' => $case->name . ' Case was edited:',
                'start_time' => now(),
            ]);
            $fieldsToUpdate = config('form-fields.fieldsToUpdate');
            // cases[$fieldsToUpdate each] decrpty
            foreach ($fieldsToUpdate as $field) {
                if ($field == 'court') {
                    $case[$field] = $case->$field != '' ? $case->$field : 1;
                } else {
                    $case[$field] = $this->decryptAES($case->$field);
                }
            }
            $fallSlip = config('form-fields.fallSlip');
                foreach ($fallSlip as $field) {
                    if(isset($case->fallSlip[$field])){
                        $case->fallSlip[$field] = $this->decryptAES($case->fallSlip[$field]);
                    }
                }
            $originalPath = storage_path('app/public/uploads/case_docs/');
            // $case->case_docs
            if($case->case_docs){
            $updatedDocs = [];
            foreach (json_decode($case->case_docs) as $index1 => $folder) {
                $updatedDocs[$index1] = $folder;
                foreach ($folder->docData as $index2 => $document) {
                    $updatedDocs[$index1]->docData[$index2] = $document;
                    foreach ($document->files as $index => $file) {
                        // dd();
                            $file = decryptFile($originalPath.$file,pathinfo($file)['extension']);
                    $updatedDocs[$index1]->docData[$index2]->files[$index] = $file;
                            
                    }
                }
            }
        }
            // dd(json_decode($case->case_docs),$updatedDocs);
            $case->case_docs = json_encode($updatedDocs);
            return view('cases.edit', compact('courts', 'allOptions', 'documents', 'advocates', 'team', 'case', 'your_advocates', 'your_team', 'priorities', 'case_typ', 'timeLine','caseCount','roles', 'timezones', 'selectedCountry', 'selectedState', 'selectedCity', 'countries', 'states','cities'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {



        $your_advocates = $request->your_advocates;

        $uniqueValues = [];
        if (isset($your_advocates)) {
            foreach ($your_advocates as $element) {
                $values = explode(",", $element);

                $uniqueValues = array_merge($uniqueValues, $values);
            }

            $uniqueValues = array_unique($uniqueValues);

            $uniqueValues = array_values($uniqueValues);
        }


        if (Auth::user()->can('edit case') && $this->isAssignedCase($id)) {

            $opponentData = [];

            $numOpponents = count($request->opponents['opponents_name']);

            for ($i = 0; $i < $numOpponents; $i++) {
                // Create a new opponent entry with the desired structure.
                $opponent = [
                    'opponents_name' => $request->opponents['opponents_name'][$i],
                    'opponents_email' => $request->opponents['opponents_email'][$i],
                    'opponents_phone' => $request->opponents['opponents_phone'][$i],
                ];

                // Add the opponent to the transformed data array.
                $opponentData[] = $opponent;
            }

            $allWitness = [];

            $numWitness = count($request->witness['witness_name']);

            for ($i = 0; $i < $numWitness; $i++) {

                $witness = [
                    'witness_name' => $request->witness['witness_name'][$i],
                    'witness_email' => $request->witness['witness_email'][$i],
                    'witness_phone' => $request->witness['witness_phone'][$i],
                    'witness_address' => $request->witness['witness_address'][$i],
                ];

                $allWitness[] = $witness;
            }

            $opponentAdvocateData = [];

            $numOpponentAdvocate = count($request->opponent_advocates['opp_advocates_name']);

            for ($i = 0; $i < $numOpponentAdvocate; $i++) {
                // Create a new opponent entry with the desired structure.
                $opponent = [
                    'opp_advocates_name' => $request->opponent_advocates['opp_advocates_name'][$i],
                    'opp_advocates_email' => $request->opponent_advocates['opp_advocates_email'][$i],
                    'opp_advocates_phone' => $request->opponent_advocates['opp_advocates_phone'][$i],
                ];

                // Add the opponent to the transformed data array.
                $opponentAdvocateData[] = $opponent;
            }


            $inputDateString = $request->open_date;
            $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

            if (preg_match($dateFormatRegex, $inputDateString)) {
                $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
                $formattedDate = $dateObject->format('Y-m-d');
                $convertedDate_openDate = $formattedDate;
            } else {
                $convertedDate_openDate = $inputDateString;
            }


            $convertedDate_closeDate = Null;

            if ($request->close_date) {

                if (preg_match($dateFormatRegex, $request->close_date)) {
                    $dateObject = Carbon::createFromFormat('D M j Y', $request->close_date);
                    $formattedDate = $dateObject->format('Y-m-d');
                    $convertedDate_closeDate = $formattedDate;
                } else {
                    $convertedDate_closeDate = $request->close_date;
                }
            } else {
                $convertedDate_closeDate = Null;
            }

            $convertedDate_incidentDate = Null;
            if ($request->incident_date) {

                if (preg_match($dateFormatRegex, $request->incident_date)) {
                    $dateObject = Carbon::createFromFormat('D M j Y', $request->incident_date);
                    $formattedDate = $dateObject->format('Y-m-d');
                    $convertedDate_incidentDate = $formattedDate;
                } else {

                    $convertedDate_incidentDate = $request->incident_date;
                }
            } else {
                $convertedDate_incidentDate = NUll;
            }

            $case = Cases::with('fallSlip')->find($id);
          

            $updatedFields = [];

            $fieldsToUpdate = config('form-fields.fieldsToUpdate');
            // dd($request->all());
            foreach ($fieldsToUpdate as $field) {
                if ($field == 'court') {
                    $case[$field] = $request->$field != '' ? $request->$field : 1;
                } else {
                    $updateOldData[$field] =  $this->decryptAES($case[$field]);
                    $case[$field] = $this->decryptAES($request->$field);
                }
            }

            if ($request->practice_area == 'Slip & Fall') {

                $fieldsToUpdate = [
                    'fall_c_name', 'fall_c_gender', 'fall_c_marital_status',
                    'fall_c_spouse_name', 'fall_c_emergency_contact_name', 'fall_c_emergency_contact_number',
                    'fall_c_dob', 'fall_c_social_security', 'fall_c_address', 'fall_c_phone', 'fall_c_email',
                    'fall_c_driver_license', 'fall_c_health_insurance', 'fall_tpi_name', 'fall_tpi_phone',
                    'fall_tpi_address', 'fall_tpi_email', 'fall_mi_name', 'fall_mi_phone', 'fall_mi_address',
                    'fall_mi_email', 'fall_ii_incident_date', 'fall_ii_time', 'fall_ii_location_of_incident',
                    'fall_ii_address', 'fall_ii_maps_link', 'fall_ii_cause_incident', 'fc_police_notified',
                    'fc_incident_report_filed', 'fc_incident_report', 'fall_ifp_company_name',
                    'fall_ifp_insured_name', 'fall_ifp_poilicy', 'fall_ifp_member', 'fall_ifp_claim',
                    'fall_ifp_insurance_phone', 'fall_ifp_adjuster_name', 'fall_ifp_adjuster_email',
                    'fall_ifp_adjuster_phone', 'fall_ifp_adjuster_fax', 'fall_ifp_adjuster_policy_limits',
                    'fall_itp_company_name', 'fall_itp_insured_name', 'fall_itp_poilicy', 'fall_itp_claim',
                    'fall_itp_insurance_phone', 'fall_itp_adjuster_name', 'fall_itp_adjuster_email',
                    'fall_itp_adjuster_phone', 'fall_itp_adjuster_fax', 'fall_itp_adjuster_policy_limits',
                    'witness', 'fall_o_incident_report', 'fall_o_recorded_statements', 'fall_o_opponent_counsel',
                    'fall_o_name', 'fall_o_phone', 'fall_o_email', 'fall_o_fax'
                ];



                if ($case['open_date'] !== $convertedDate_openDate) {
                    $case['open_date'] = $convertedDate_openDate;
                    $updatedFields[] = 'open_date';
                }

                if ($case['close_date'] !== $convertedDate_closeDate) {
                    $case['close_date'] = $convertedDate_closeDate;
                    $updatedFields[] = 'close_date';
                }

                if ($case['incident_date'] !== $convertedDate_incidentDate) {
                    $case['incident_date'] = $convertedDate_incidentDate;
                    $updatedFields[] = 'incident_date';
                }

                if ($case['case_stage'] !== $request->case_stage) {
                    $updatedFields[] = 'case_stage';
                }
                if ($case['practice_area'] !== $request->practice_area) {
                    $updatedFields[] = 'practice_area';
                }
                if ($case['statute_of_limitations'] !== $request->statute_of_limitations) {
                    $updatedFields[] = 'statute_of_limitations';
                }
                if ($case['description'] !== $request->description) {
                    $updatedFields[] = 'description';
                }

                if ($request->folders) {
                    foreach ($request->folders as $index => $caseDoc) {

                       
                        if (isset($caseDoc['folder_doc'])) {
                            foreach ($caseDoc['folder_doc'] as $ind => $docFile) {
                                if (isset($docFile['files'])) {

                                    $folderName = isset($caseDoc['folder_name']) ? $caseDoc['folder_name'] : '';
                                    $fileName = isset($docFile['files'][$ind]) ? $docFile['files'][$ind] : '';

                                    $updatedFields[] = 'New File Added in ' . $folderName . ' (' . $fileName . ')';
                                }
                            }
                        }
                    }
                }
                


                $witness = json_decode($case->fallSlip?->witness ?? '', true);

                foreach ($fieldsToUpdate as $field) {

                    if ($field === 'witness') {

                        foreach ($allWitness as $index => $opponent) {
                            if (!isset($witness[$index])) {

                                $witness[$index] = $opponent;
                                $updatedFields[] = 'witness';
                            } else {
                                $caseOpponent = $witness[$index];
                                foreach ($opponent as $field => $value) {
                                    if ($value !== $caseOpponent[$field]) {
                                        $witness[$index][$field] = $value;
                                        $updatedFields[] = 'witness';
                                    }
                                }
                            }
                        }
                    } elseif ($field === 'your_advocates' || $field === 'your_team') {
                        $case->$field = json_encode($case->$field);
                        $oldValue = json_decode($case->$field, true);
                        $oldValue = explode(',', $oldValue);

                        if (!is_array($oldValue)) {
                            $oldValue = [];
                        }

                        $newValue = $request->$field;
                        if (!is_array($newValue)) {
                            $newValue = [];
                        }

                        $addedUsers = array_diff($newValue, $oldValue);

                        $removedUsers = array_diff($oldValue, $newValue);

                        $message = '';

                        $fieldLabel = ($field === 'your_advocates') ? 'Attorney' : 'User';

                        if (!empty($addedUsers)) {
                            $addedUserNames = [];
                            foreach ($addedUsers as $userId) {
                                $user = User::find($userId);
                                if ($user) {
                                    $addedUserNames[] = $user->name;
                                }
                            }

                            if (!empty($addedUserNames)) {
                                $message .= 'Added ' . $fieldLabel . '(s): ' . implode(', ', $addedUserNames) . '. ';
                            }
                        }

                        if (!empty($removedUsers)) {
                            $removedUserNames = [];
                            foreach ($removedUsers as $userId) {
                                $user = User::find($userId);
                                if ($user) {
                                    $removedUserNames[] = $user->name;
                                }
                            }

                            if (!empty($removedUserNames)) {
                                $message .= 'Removed ' . $fieldLabel . '(s): ' . implode(', ', $removedUserNames) . '. ';
                            }
                        }

                        if (!empty($message)) {
                            $updatedFields[] = $message;
                        }
                    } else {

                        if (isset($case->fallSlip[$field]) && $request->$field && ($case->fallSlip[$field] !== $request->$field)) {

                            $case->fallSlip[$field] = $request->$field;
                            $updatedFields[] = $field;
                        }
                    }
                }
            } else {

                $fieldsToUpdate = [
                    'name', 'case_number', 'case_stage', 'practice_area',
                    'description', 'location_of_accident', 'statute_of_limitations',  'intersection', 'coordinates', 'injury_type',
                    'case_manager', 'file_location', 'first_party_company_name',
                    'first_party_policy_name', 'statute_of_limitations', 'first_party_insurance_phone_number', 'first_party_name',
                    'first_party_phone_number', 'first_party_policy_limits', 'first_insured_name',
                    'first_party_claim_number', 'first_party_adjuster', 'first_party_email',
                    'first_party_fax', 'third_party_company_name', 'third_party_policy_name',
                    'third_party_insurance_phone_number', 'third_party_name', 'third_party_phone_number',
                    'third_party_policy_limits', 'third_insured_name', 'third_party_claim_number',
                    'third_party_adjuster', 'third_party_email', 'third_party_fax',
                    'first_party_driver_name', 'first_party_vehicle_year', 'first_party_vehicle_model',
                    'first_party_customer_type', 'first_party_passenger_name', 'first_party_vehicle_make',
                    'first_party_vehicle_license', 'first_party_airbags_developed', 'first_party_seat_belts_worn',
                    'emergency_name', 'emergency_phone', 'third_party_driver_name', 'third_party_vehicle_year',
                    'third_party_vehicle_model', 'third_party_customer_type', 'third_party_passenger_name',
                    'third_party_vehicle_make', 'third_party_vehicle_license', 'third_party_airbags_developed',
                    'third_party_seat_belts_worn', 'police_report', 'recorded_statement',
                    'recorded_statement_description', 'other_name', 'other_phone_number',
                    'other_email_address', 'other_fax', 'opponents', 'opponent_advocates', 'your_advocates', 'your_team'

                ];
               

                if ($case['open_date'] !== $convertedDate_openDate) {
                    $case['open_date'] = $convertedDate_openDate;
                    $updatedFields[] = 'open_date';
                }

                if ($case['close_date'] !== $convertedDate_closeDate) {
                    $case['close_date'] = $convertedDate_closeDate;
                    $updatedFields[] = 'close_date';
                }
                if ($case['incident_date'] !== $convertedDate_incidentDate) {
                    $case['incident_date'] = $convertedDate_incidentDate;
                    $updatedFields[] = 'incident_date';
                }


                if ($request->folders) {
                    foreach ($request->folders as $index => $caseDoc) {
                      
                        if (isset($caseDoc['folder_doc'])) {
                            foreach ($caseDoc['folder_doc'] as $ind => $docFile) {
                                if (isset($docFile['files'])) {

                                    foreach ($docFile['files'] as $index => $file) {
                                        $folderName = isset($caseDoc['folder_name']) ? $caseDoc['folder_name'] : '';
                                        $fileName = $file ?? '';
    
                                        $updatedFields[] = 'New File Added in ' . $folderName . ' (' . $fileName . ')';

                                    }
                                }
                            }
                        }
                    }
                }

               
                $caseOpponents = json_decode($case['opponents'], true);
                $caseOpponentsAdv = json_decode($case['opponent_advocates'], true);

                

                foreach ($fieldsToUpdate as $field) {
 

                    if ($field === 'opponents') {

                        foreach ($opponentData as $index => $opponent) {
                            if (!isset($caseOpponents[$index])) {
                                // If the opponent doesn't exist in the database, add it as a new opponent
                                $caseOpponents[$index] = $opponent;
                                $updatedFields[] = 'opponents'; // Mark all fields as updated for the new opponent
                            } else {
                                // If the opponent exists, compare fields and update if needed
                                $caseOpponent = $caseOpponents[$index];
                                foreach ($opponent as $field => $value) {
                                    if ($value !== $caseOpponent[$field]) {
                                        $caseOpponents[$index][$field] = $value;
                                        $updatedFields[] = 'opponents';
                                    }
                                }
                            }
                        }

                        $case['opponents'] = json_encode($caseOpponents);
                    } 

                  

                    if ($field === 'opponent_advocates') {


                        foreach ($opponentAdvocateData as $index => $opponentAdv) {
                            if (!isset($caseOpponentsAdv[$index])) {
                                // If the opponent doesn't exist in the database, add it as a new opponent
                                $caseOpponentsAdv[$index] = $opponentAdv;
                                $updatedFields[] = 'opponents_advocates'; // Mark all fields as updated for the new opponent
                            } else {
                                // If the opponent exists, compare fields and update if needed
                                $caseOpponent = $caseOpponentsAdv[$index];
                                foreach ($opponentAdv as $field => $value) {
                                    if ($value !== $caseOpponent[$field]) {
                                        $caseOpponentsAdv[$index][$field] = $value;
                                        $updatedFields[] = 'opponents_advocates';
                                    }
                                }
                            }
                        }
                        $case['opponent_advocates'] = json_encode($caseOpponentsAdv);
                    } 
                    
                    if ($field === 'your_advocates' || $field === 'your_team') {
                        $case->$field = json_encode($case->$field);
                        $oldValue = json_decode($case->$field, true);
                        $oldValue = explode(',', $oldValue);

                        if (!is_array($oldValue)) {
                            $oldValue = [];
                        }

                        $newValue = $request->$field;
                        if (!is_array($newValue)) {
                            $newValue = [];
                        }

                        $addedUsers = array_diff($newValue, $oldValue);

                        $removedUsers = array_diff($oldValue, $newValue);

                        $message = '';

                        $fieldLabel = ($field === 'your_advocates') ? 'Attorney' : 'User';

                        if (!empty($addedUsers)) {
                            $addedUserNames = [];
                            foreach ($addedUsers as $userId) {
                                $user = User::find($userId);
                                if ($user) {
                                    $addedUserNames[] = $user->name;
                                }
                            }

                            if (!empty($addedUserNames)) {
                                $message .= 'Added ' . $fieldLabel . '(s): ' . implode(', ', $addedUserNames) . '. ';
                            }
                        }

                        if (!empty($removedUsers)) {
                            $removedUserNames = [];
                            foreach ($removedUsers as $userId) {
                                $user = User::find($userId);
                                if ($user) {
                                    $removedUserNames[] = $user->name;
                                }
                            }

                            if (!empty($removedUserNames)) {
                                $message .= 'Removed ' . $fieldLabel . '(s): ' . implode(', ', $removedUserNames) . '. ';
                            }
                        }

                        if (!empty($message)) {
                            $updatedFields[] = $message;
                        }
                    } 

                   
                    
                    if ($field === 'first_party_vehicle_year') {

                        if ($case[$field] == $request->$field) {
                        } else {
                            $updatedFields[] = $field;
                        }
                    } 
                    if ($field === 'third_party_vehicle_year') {
                        if ($case[$field] == $request->$field) {
                        } else {
                            $updatedFields[] = $field;
                        }
                    } 
                   
                    if(isset($updateOldData[$field])){
                        if ( (!empty($updateOldData[$field] && !empty($request->$field))) && $updateOldData[$field] !== $request->$field ) {
                            $updateOldData[$field] = $request->$field;
                            $updatedFields[] = $field;
                        }
                     }
                }
              
            }
           
 
         



            $fieldsToUpdate = config('form-fields.fieldsToUpdate');

            foreach ($fieldsToUpdate as $field) {
                if (isset($this->formFieldsPremissions[$field]) && $this->formFieldsPremissions[$field] == 'Yes') {
                    $case[$field] = ($field == 'court') ? ($request->$field != '' ? $request->$field : 1) : $this->encryptAES($request->$field);
                } else {
                    $case[$field] = ($field == 'court') ? ($request->$field != '' ? $request->$field : 1) : $request->$field;
                }
            }
            // open_date,close_date,filing_date,incident_date,first_party_customer_type,first_party_seat_belts_worn,third_party_customer_type,third_party_airbags_developed,third_party_seat_belts_worn,first_party_airbags_developed
            $case['open_date'] = $this->updateFormat($request->open_date);
            $case['close_date'] = $this->updateFormat($request->close_date);
            $case['filing_date'] = $this->updateFormat($request->filing_date);
            $case['incident_date'] = $this->updateFormat($request->incident_date);
            $case['first_party_customer_type'] = $request->first_party_customer_type;
            $case['first_party_airbags_developed'] = $request->first_party_airbags_developed;
            $case['first_party_seat_belts_worn'] = $request->first_party_seat_belts_worn;
            $case['third_party_customer_type'] = $request->third_party_customer_type;
            $case['third_party_airbags_developed'] = $request->third_party_airbags_developed;
            $case['third_party_seat_belts_worn'] = $request->third_party_seat_belts_worn;

            // opponent_advocates,opponents
            $case['opponents'] = json_encode($opponentData);
            $case['opponent_advocates'] = json_encode($opponentAdvocateData);

            $case['your_advocates'] =  $uniqueValues ? implode(',', $uniqueValues) : null;
            $case['your_team'] = $request->your_team ? implode(',', $request->your_team) : '';

            $case['created_by'] = Auth::user()->creatorId();
            $case['draft'] = 0;

            $folder_name_old = Auth::user()->creatorId() . '-' . 'case-doc';
            $destinationPath = public_path('storage/uploads/case_docs/' . $folder_name_old);

            $caseDocsData = $request->folders;
            $savedData = [];

            if ($caseDocsData) {
                foreach ($caseDocsData as $index => $caseDoc) {



                    $folder_name = $caseDoc['folder_name'] ?? '';
                    $folder_description = $caseDoc['folder_description'] ?? '';


                    $docData = [];
                    if (isset($caseDoc['folder_doc'])) {

                        foreach ($caseDoc['folder_doc'] as $ind => $docFile) {


                            $doc_name = $docFile['doc_name'] ?? '';
                            $doc_des = $docFile['doc_description'] ?? '';


                            $files = [];
                            if (isset($docFile['files'])) {

                                $doc_files = $docFile['files'] ?? '';
                                foreach ($doc_files as $file) {

                                    $sourcePath = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old . '/' . $file);

                                    $destinationFile = $destinationPath . '/' . $file;
                                    if (!File::exists($destinationPath)) {
                                        File::makeDirectory($destinationPath, 0755, true);
                                    }

                                    if (File::exists($sourcePath)) {
                                        File::move($sourcePath, $destinationFile);
                                    }

                                    $files[] = $folder_name_old . '/' . $file;
                                }
                            }

                            $docData[] = [
                                'doc_name' => $doc_name,
                                'doc_des' => $doc_des,
                                'files' => $files,
                                'uploaded_by' => Auth::user()->id,
                                'uploaded_at' => now(),


                            ];
                        }
                    }

                    $savedData[] = [
                        'folder_name' => $folder_name,
                        'folder_description' => $folder_description,
                        'docData' => $docData,
                    ];
                }
            }
            $dbDocData = json_decode($case->case_docs);
            foreach ($dbDocData as $dbDocDataindex => $docData) {
                if (isset($savedData[$dbDocDataindex])) {
                    $savedData[$dbDocDataindex]['docData'] = array_merge($docData->docData, $savedData[$dbDocDataindex]['docData']);
                }
            }


            $case['case_docs'] =  json_encode($savedData)  ?? '';
            $case->save();

            if ($request->practice_area == 'Slip & Fall') {
                $this->FallSlipUpdate($case->id, $request, $case);
            }

            // Activity log create
            if(isset($files)){

                $fileNames = implode(', ', $files);

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Updated',
                    'file' => $fileNames,
                ]);

            }else{

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Updated',
                ]);

            }

           

            $sourcePathDelete = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old);
            if (File::exists($sourcePathDelete)) {
                File::deleteDirectory($sourcePathDelete);
            }

            $updatedFieldsString = implode(', ', $updatedFields);

            $activity =  FolderActivity::findOrFail($request->timeLineId);
            if ($activity) {
                $activity->end_time = now();
                $activity->edit_case = $updatedFieldsString;
                $activity->save();
            } 

          

            $Notify = Auth::user();
           
            $yourAdvocates = explode(',', $case->your_advocates);
            $yourTeam = explode(',', $case->your_team);

            // Merge the arrays
            $userIds = array_merge($yourAdvocates, $yourTeam);
          
          
            $key = array_search($Notify->id, $userIds);

            if ($key !== false) {
                unset($userIds[$key]);
            }


            if($Notify->id != $case->created_by){
                $userIds[] = $case->created_by;
            }
           
         
            $message = "The case '{$case->name}' has been updated, and it is related to you by '{$Notify->name}'";


            $notificationData = [
                'message' => $message,
                'type' => 'case',
                'target_id' => $case->id,
            ];
            
            foreach ($userIds as $userId) {
                if ($userId !== null && is_numeric($userId)) {
                    $notificationData['user_id'] = $userId;
                    event(new CaseNotification($notificationData));
                }
            }


            return redirect()->route('cases.index')->with('success', __('Case successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function FallSlipUpdate($caseId, $request, $case)
    {



        $allWitness = [];

        $numOpponents = count($request->witness['witness_name']);

        for ($i = 0; $i < $numOpponents; $i++) {

            $witness = [
                'witness_name' => $request->witness['witness_name'][$i],
                'witness_email' => $request->witness['witness_email'][$i],
                'witness_phone' => $request->witness['witness_phone'][$i],
                'witness_address' => $request->witness['witness_address'][$i],
            ];

            $allWitness[] = $witness;
        }


        $inputDateString = $request->fall_c_dob;
        $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

        if (preg_match($dateFormatRegex, $inputDateString)) {
            $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
            $formattedDate = $dateObject->format('Y-m-d');
            $fallClientDob = $formattedDate;
        } else {
            $fallClientDob = $inputDateString;
        }

        $incidentDate = $request->fall_ii_incident_date;
        if (preg_match($dateFormatRegex, $incidentDate)) {
            $dateObject = Carbon::createFromFormat('D M j Y', $incidentDate);
            $formattedDate = $dateObject->format('Y-m-d');
            $fall_ii_incident_date = $formattedDate;
        } else {
            $fall_ii_incident_date = $incidentDate;
        }





        // if $case->fallSlip->id not define than creat else update
        $fallSlip = CasesSlipFall::where('case_id', $caseId)->first();
        if (!$fallSlip) {
            $fallSlip = new CasesSlipFall();
        }

        $fallSlip['case_id'] = $caseId;


        $fieldsToUpdate = config('form-fields.fallSlip');
        // dd($request->all());
        foreach ($fieldsToUpdate as $field) {
            if (isset($this->formFieldsPremissions[$field]) && $this->formFieldsPremissions[$field] == 'Yes' && isset($request[$field])) {
                $fallSlip[$field] = $this->encryptAES($request->$field);
            } else {
                $fallSlip[$field] = $request->$field;
            }
        }
        // fall_ii_time,fall_c_health_insurance,fc_police_notified,fc_incident_report_filed,fall_o_recorded_statements,fall_ii_incident_date
        $fallSlip['fall_ii_time'] = $request->fall_ii_time;
        $fallSlip['fall_c_health_insurance'] = $request->fall_c_health_insurance;
        $fallSlip['fc_police_notified'] = $request->fc_police_notified;
        $fallSlip['fc_incident_report_filed'] = $request->fc_incident_report_filed;
        $fallSlip['fall_o_recorded_statements'] = $request->fall_o_recorded_statements;
        $fallSlip['fall_ii_incident_date'] = $fall_ii_incident_date;



        $fallSlip['witness'] = json_encode($allWitness);


        $fallSlip->save();

        return true;
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->can('delete case') && $this->isAssignedCase($id)) {

            $case = Cases::find($id);

            if ($case) {

                $taskData = TaskData::where('cases_id', $case->id)->get();
               
                // make foreach for uper taskData and create activity log
                foreach ($taskData as $data) {
                    Activity::create([
                        'user_id' => Auth::user()->id,
                        'company_id' => Auth::user()->creatorId(),
                        'target_id' => $data->id,
                        'target_type' => 'Task',
                        'action' => 'Deleted',
                    ]);
                }


                TaskData::where('cases_id', $case->id)->update(['deleted_at' => now()]);

                $case->delete();

                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $case->id,
                    'target_type' => 'Case',
                    'action' => 'Deleted',
                ]);

                
            }
            

            return redirect()->route('cases.index')->with('success', __('Case successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function permanentlyDelete($id){

        if (Auth::user()->can('delete case') && $this->isAssignedCase($id)) {

            $case = Cases::find($id);

            if ($case) {

                $caseDocArray = json_decode($case->case_docs, true);

                if (!empty($caseDocArray)) {
                    foreach ($caseDocArray as $docData) {
                        if (isset($docData['docData']) && is_array($docData['docData'])) {
                            foreach ($docData['docData'] as $doc) {
                                if (isset($doc['files']) && is_array($doc['files'])) {
                                    foreach ($doc['files'] as $file) {
                                        $filePath = public_path('storage/uploads/case_docs/' . $file);

                                        if (File::exists($filePath)) {
                                            File::delete($filePath);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $taskData = TaskData::where('cases_id', $id)->get();
               
                foreach ($taskData as $data) {
                    $tasks = Task::where('task_data_id', $data->id)->get();
                   
                    if (!empty($tasks)) {
                        foreach ($tasks as $task) {
                           
                            $taskLogs = TaskLog::where('task_id', $task->id)->get();
                            foreach ($taskLogs as $taskLog) {
                                $taskLog->delete();
                            }

                            $subTasks = subTask::where('task_id', $task->id)->get();

                            foreach ($subTasks as $subTask) {
                               
                                $subtaskLogs = SubTaskLog::where('task_id', $task->id)->where('subtask_id', $subTask->id)->get();
                                foreach ($subtaskLogs as $subtaskLog) {
                                    $subtaskLog->delete();
                                }
                                
                                $subTask->delete();
                             }


                            $task->delete();
                         }
                    }
                    $data->delete();
                }

                if ($case->practice_area == 'Slip & Fall') {
                    $fallSlipCase = CasesSlipFall::where('case_id', $case->id)->first();
                    if ($fallSlipCase) {
                        $fallSlipCase->delete();
                    }
                }

                $activities = FolderActivity::where('case_id', $case->id)->get();
                foreach ($activities as $activity) {
                    $activity->delete();
                }

                $case->delete();
            }

            return redirect()->route('cases.index')->with('success', __('Case successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // deleteDoc
    public function deleteDoc(Request $request)
    {

        if ($request->case_id) {
            $case = Cases::find($request->case_id);

            if ($case) {
                $caseDocs = json_decode($case->case_docs);

                if (isset($caseDocs[$request->folderIndex]->docData[$request->docIndex]->files)) {
                    $files = $caseDocs[$request->folderIndex]->docData[$request->docIndex]->files;
                    
                    if (isset($files[$request->fileIndex])) {

                        $deletedFIle = $files[$request->fileIndex];

                        $filePath = public_path("storage/uploads/case_docs/{$files[$request->fileIndex]}");

                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        unset($files[$request->fileIndex]);

                        $files = array_values($files);

                        $caseDocs[$request->folderIndex]->docData[$request->docIndex]->files = $files;

                        Activity::create([
                            'user_id' => Auth::user()->id,
                            'company_id' => Auth::user()->creatorId(),
                            'target_id' => $case->id,
                            'target_type' => 'Case',
                            'action' => 'Deleted',
                            'file' => $deletedFIle,
                        ]);

                    }
                }
                $case->case_docs = json_encode($caseDocs);
                $case->save();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        } else {
            return response()->json(['success' => false]);
        }
    }
    private function encryptAES($data)
{
    try {
        // Generate a random IV for encryption
        $iv = random_bytes(16);

        // Handle empty or array data
        if (is_array($data) || $data === '') {
            return $data;
        }

        // Perform AES encryption using openssl_encrypt
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-cbc',
            env('AES_Secret_Key_DB'),
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            return "Encryption failed.";
        }

        // Combine IV and encrypted data
        $combinedData = $iv . $encrypted;

        // Encode the combined data as base64
        $encryptedBase64 = base64_encode($combinedData);
        // dd($iv,$encryptedBase64);

        return $encryptedBase64;
    } catch (\Exception $e) {
        // Handle encryption errors if necessary
        dd($e->getMessage());
    }
}

    private function decryptAES($encryptedText)
{
    try {
        // Decode the base64 encoded data
        $combinedData = base64_decode($encryptedText);

        // Extract the IV (first 16 bytes) and the encrypted data
        $iv = substr($combinedData, 0, 16);
        $encrypted = substr($combinedData, 16);
        if(strlen($iv) < 16){
            return $encryptedText;
        }
        // Perform AES decryption using openssl_decrypt
        $decrypted = openssl_decrypt(
            $encrypted,
            'aes-256-cbc',
            env('AES_Secret_Key_DB'),
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            return $encryptedText;
        }
        return $decrypted;
    } catch (\Exception $e) {
        // Handle decryption errors if necessary
        dd($e->getMessage());
    }
}


    private function encryptFile($filePath)
    {
        try {
            // Check if the file exists
            if (!file_exists($filePath)) {
                return "File does not exist.";
            }
    
            // Read the file contents
            $fileContents = file_get_contents($filePath);
    
            // Generate a random IV for encryption
            $iv = random_bytes(16);
    
            // Encrypt the file contents using AES-256-CBC encryption
            $encryptedContents = openssl_encrypt(
                $fileContents,
                'aes-256-cbc',
                env('AES_Secret_Key_DB'),
                0,
                $iv
            );
    
            // Write the encrypted contents back to the file
            file_put_contents($filePath, $iv . $encryptedContents);
    
            return "File encrypted successfully.";
        } catch (\Exception $e) {
            // Handle encryption errors if necessary
            dd($e->getMessage());
        }
    }

    public function caseDraft(Request $request)
    {


        if (empty($request->caseId)) {

            if (Auth::user()->can('create case')) {

                $your_advocates = $request->your_advocates;

                $uniqueValues = [];
                if (isset($your_advocates)) {
                    foreach ($your_advocates as $element) {
                        $values = explode(",", $element);

                        $uniqueValues = array_merge($uniqueValues, $values);
                    }

                    $uniqueValues = array_unique($uniqueValues);

                    $uniqueValues = array_values($uniqueValues);
                }


                $opponentData = [];

                $numOpponents = count($request->opponents['opponents_name']);

                for ($i = 0; $i < $numOpponents; $i++) {
                    // Create a new opponent entry with the desired structure.
                    $opponent = [
                        'opponents_name' => $request->opponents['opponents_name'][$i],
                        'opponents_email' => $request->opponents['opponents_email'][$i],
                        'opponents_phone' => $request->opponents['opponents_phone'][$i],
                    ];

                    $opponentData[] = $opponent;
                }

                $opponentAdvocateData = [];

                $numOpponentAdvocate = count($request->opponent_advocates['opp_advocates_name']);

                for ($i = 0; $i < $numOpponentAdvocate; $i++) {
                    // Create a new opponent entry with the desired structure.
                    $opponent = [
                        'opp_advocates_name' => $request->opponent_advocates['opp_advocates_name'][$i],
                        'opp_advocates_email' => $request->opponent_advocates['opp_advocates_email'][$i],
                        'opp_advocates_phone' => $request->opponent_advocates['opp_advocates_phone'][$i],
                    ];

                    $opponentAdvocateData[] = $opponent;
                }


                $inputDateString = $request->open_date;
                $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

                if (preg_match($dateFormatRegex, $inputDateString)) {
                    $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
                    $formattedDate = $dateObject->format('Y-m-d');
                    $convertedDate_openDate = $formattedDate;
                } else {
                    $convertedDate_openDate = $inputDateString;
                }


                $convertedDate_closeDate = Null;

                if ($request->close_date) {

                    if (preg_match($dateFormatRegex, $request->close_date)) {
                        $dateObject = Carbon::createFromFormat('D M j Y', $request->close_date);
                        $formattedDate = $dateObject->format('Y-m-d');
                        $convertedDate_closeDate = $formattedDate;
                    } else {
                        $convertedDate_closeDate = $request->close_date;
                    }
                } else {
                    $convertedDate_closeDate = Null;
                }

                $convertedDate_incidentDate = Null;
                if ($request->incident_date) {

                    if (preg_match($dateFormatRegex, $request->incident_date)) {
                        $dateObject = Carbon::createFromFormat('D M j Y', $request->incident_date);
                        $formattedDate = $dateObject->format('Y-m-d');
                        $convertedDate_incidentDate = $formattedDate;
                    } else {

                        $convertedDate_incidentDate = $request->incident_date;
                    }
                } else {
                    $convertedDate_incidentDate = NUll;
                }

                $case = new Cases();

            

                $fieldsToUpdate = config('form-fields.fieldsToUpdate');
                // dd($request->all());
                foreach ($fieldsToUpdate as $field) {
                    if (isset($this->formFieldsPremissions[$field]) && $this->formFieldsPremissions[$field] == 'Yes') {
                        if ($field != 'court') {
                            $case[$field] = $this->encryptAES($request->$field);
                        }
                    } else {
                        $case[$field] = $request->$field != '' ? $request->$field : ($field == 'court' ? 1 : $request->$field);
                    }
                }
                // open_date,close_date,filing_date,incident_date,first_party_customer_type,first_party_seat_belts_worn,third_party_customer_type,third_party_airbags_developed,third_party_seat_belts_worn,first_party_airbags_developed
                $case['open_date'] = $request->open_date;
                $case['close_date'] = $request->close_date;
                $case['filing_date'] = $request->filing_date;
                $case['incident_date'] = $request->incident_date;
                $case['first_party_customer_type'] = $request->first_party_customer_type;
                $case['first_party_airbags_developed'] = $request->first_party_airbags_developed;
                $case['first_party_seat_belts_worn'] = $request->first_party_seat_belts_worn;
                $case['third_party_customer_type'] = $request->third_party_customer_type;
                $case['third_party_airbags_developed'] = $request->third_party_airbags_developed;
                $case['third_party_seat_belts_worn'] = $request->third_party_seat_belts_worn;

                // opponent_advocates,opponents
                $case['opponents'] = json_encode($opponentData);
                $case['opponent_advocates'] = json_encode($opponentAdvocateData);

                // dd($case);
                $case['your_advocates'] = $uniqueValues ? implode(',', $uniqueValues) ?? '' : null;

                $case['your_team'] = $request->your_team ? implode(',', $request->your_team) ?? '' : null;


                $case['created_by'] = Auth::user()->creatorId();



                $folder_name_old = Auth::user()->creatorId() . '-' . 'case-doc';
                $destinationPath = public_path('storage/uploads/case_docs/' . $folder_name_old);

                $caseDocsData = $request->folders;
                $savedData = [];

                if ($caseDocsData) {
                    foreach ($caseDocsData as $index => $caseDoc) {



                        $folder_name = $caseDoc['folder_name'] ?? '';
                        $folder_description = $caseDoc['folder_description'] ?? '';


                        $docData = [];
                        if (isset($caseDoc['folder_doc'])) {

                            foreach ($caseDoc['folder_doc'] as $ind => $docFile) {


                                $doc_name = $docFile['doc_name'] ?? '';
                                $doc_des = $docFile['doc_description'] ?? '';


                                $files = [];
                                if (isset($docFile['files'])) {

                                    $doc_files = $docFile['files'] ?? '';
                                    foreach ($doc_files as $file) {
                                        
                                        $sourcePath = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old . '/' . $file);
                                        $destinationFile = $destinationPath . '/' . $file;
                                        if (!File::exists($destinationPath)) {
                                            File::makeDirectory($destinationPath, 0755, true);
                                        }
                                        
                                        if (File::exists($sourcePath)) {
                                            File::move($sourcePath, $destinationFile);
                                        }

                                        $files[] = $folder_name_old . '/' . $file;
                                    }
                                }

                                $docData[] = [
                                    'doc_name' => $doc_name,
                                    'doc_des' => $doc_des,
                                    'files' => $files,
                                    'uploaded_by' => Auth::user()->id,
                                    'uploaded_at' => now(),
                                ];
                            }
                        }

                        $savedData[] = [
                            'folder_name' => $folder_name,
                            'folder_description' => $folder_description,
                            'docData' => $docData,
                        ];
                    }
                }

                $case['case_docs'] =  json_encode($savedData)  ?? '';
                $case->save();



                $sourcePathDelete = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old);
                if (File::exists($sourcePathDelete)) {
                    File::deleteDirectory($sourcePathDelete);
                }

                return response()->json(['success' => true, 'caseId' => $case->id]);
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {

            if (Auth::user()->can('edit case')) {

                $your_advocates = $request->your_advocates;

                $uniqueValues = [];
                if (isset($your_advocates)) {
                    foreach ($your_advocates as $element) {
                        $values = explode(",", $element);

                        $uniqueValues = array_merge($uniqueValues, $values);
                    }

                    $uniqueValues = array_unique($uniqueValues);

                    $uniqueValues = array_values($uniqueValues);
                }

                $opponentData = [];

                $numOpponents = count($request->opponents['opponents_name']);

                for ($i = 0; $i < $numOpponents; $i++) {
                    // Create a new opponent entry with the desired structure.
                    $opponent = [
                        'opponents_name' => $request->opponents['opponents_name'][$i],
                        'opponents_email' => $request->opponents['opponents_email'][$i],
                        'opponents_phone' => $request->opponents['opponents_phone'][$i],
                    ];

                    // Add the opponent to the transformed data array.
                    $opponentData[] = $opponent;
                }

                $opponentAdvocateData = [];

                $numOpponentAdvocate = count($request->opponent_advocates['opp_advocates_name']);

                for ($i = 0; $i < $numOpponentAdvocate; $i++) {
                    // Create a new opponent entry with the desired structure.
                    $opponent = [
                        'opp_advocates_name' => $request->opponent_advocates['opp_advocates_name'][$i],
                        'opp_advocates_email' => $request->opponent_advocates['opp_advocates_email'][$i],
                        'opp_advocates_phone' => $request->opponent_advocates['opp_advocates_phone'][$i],
                    ];

                    // Add the opponent to the transformed data array.
                    $opponentAdvocateData[] = $opponent;
                }


                $inputDateString = $request->open_date;
                $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

                if (preg_match($dateFormatRegex, $inputDateString)) {
                    $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
                    $formattedDate = $dateObject->format('Y-m-d');
                    $convertedDate_openDate = $formattedDate;
                } else {
                    $convertedDate_openDate = $inputDateString;
                }


                $convertedDate_closeDate = Null;

                if ($request->close_date) {

                    if (preg_match($dateFormatRegex, $request->close_date)) {
                        $dateObject = Carbon::createFromFormat('D M j Y', $request->close_date);
                        $formattedDate = $dateObject->format('Y-m-d');
                        $convertedDate_closeDate = $formattedDate;
                    } else {
                        $convertedDate_closeDate = $request->close_date;
                    }
                } else {
                    $convertedDate_closeDate = Null;
                }

                $convertedDate_incidentDate = Null;
                if ($request->incident_date) {

                    if (preg_match($dateFormatRegex, $request->incident_date)) {
                        $dateObject = Carbon::createFromFormat('D M j Y', $request->incident_date);
                        $formattedDate = $dateObject->format('Y-m-d');
                        $convertedDate_incidentDate = $formattedDate;
                    } else {

                        $convertedDate_incidentDate = $request->incident_date;
                    }
                } else {
                    $convertedDate_incidentDate = NUll;
                }

                $case = Cases::find($request->caseId);



                $fieldsToUpdate = config('form-fields.fieldsToUpdate');
                // dd($request->all());
                foreach ($fieldsToUpdate as $field) {
                    if (isset($this->formFieldsPremissions[$field]) && $this->formFieldsPremissions[$field] == 'Yes') {
                        $request[$field] = $field == 'court' ? ($request->$field != '' ? $request->$field : 1) : $this->encryptAES($request->$field);
                    } else {
                        $request[$field] = $request->$field != '' ? $request->$field : ($field == 'court' ? 1 : $request->$field);
                    }
                }

                $case['name'] = $request->name;
                $case['case_number'] = $request->case_number;
                $case['open_date'] = $convertedDate_openDate ?? today();
                $case['close_date'] = $convertedDate_closeDate;
                $case['case_stage'] = $request->case_stage;
                $case['practice_area'] = $request->practice_area;
                $case['description'] = $request->description;

                $case['location_of_accident'] = $request->location_of_accident;
                $case['intersection'] = $request->intersection;
                $case['coordinates'] = $request->coordinates;
                $case['injury_type'] = $request->injury_type;
                $case['incident_date'] = $convertedDate_incidentDate;
                $case['statute_of_limitations'] = $request->statute_of_limitations;

                $case['case_manager'] = $request->case_manager;
                $case['file_location'] = $request->file_location;

                $case['first_party_company_name'] = $request->first_party_company_name;
                $case['first_party_policy_name'] = $request->first_party_policy_name;
                $case['first_party_insurance_phone_number'] = $request->first_party_insurance_phone_number;
                $case['first_party_name'] = $request->first_party_name;
                $case['first_party_phone_number'] = $request->first_party_phone_number;
                $case['first_party_policy_limits'] = $request->first_party_policy_limits;
                $case['first_insured_name'] = $request->first_insured_name;
                $case['first_party_claim_number'] = $request->first_party_claim_number;
                $case['first_party_adjuster'] = $request->first_party_adjuster;
                $case['first_party_email'] = $request->first_party_email;
                $case['first_party_fax'] = $request->first_party_fax;


                $case['third_party_company_name'] = $request->third_party_company_name;
                $case['third_party_policy_name'] = $request->third_party_policy_name;
                $case['third_party_insurance_phone_number'] = $request->third_party_insurance_phone_number;
                $case['third_party_name'] = $request->third_party_name;
                $case['third_party_phone_number'] = $request->third_party_phone_number;
                $case['third_party_policy_limits'] = $request->third_party_policy_limits;
                $case['third_insured_name'] = $request->third_insured_name;
                $case['third_party_claim_number'] = $request->third_party_claim_number;
                $case['third_party_adjuster'] = $request->third_party_adjuster;
                $case['third_party_email'] = $request->third_party_email;
                $case['third_party_fax'] = $request->third_party_fax;


                $case['first_party_driver_name'] = $request->first_party_driver_name;
                $case['first_party_vehicle_year'] = $request->first_party_vehicle_year;
                $case['first_party_vehicle_model'] = $request->first_party_vehicle_model;
                $case['first_party_customer_type'] = $request->first_party_customer_type;
                $case['first_party_passenger_name'] = $request->first_party_passenger_name;
                $case['first_party_vehicle_make'] = $request->first_party_vehicle_make;
                $case['first_party_vehicle_license'] = $request->first_party_vehicle_license;
                $case['first_party_airbags_developed'] = $request->first_party_airbags_developed;
                $case['first_party_seat_belts_worn'] = $request->first_party_seat_belts_worn;
                $case['emergency_name'] = $request->emergency_name;
                $case['emergency_phone'] = $request->emergency_phone;


                $case['third_party_driver_name'] = $request->third_party_driver_name;
                $case['third_party_vehicle_year'] = $request->third_party_vehicle_year;
                $case['third_party_vehicle_model'] = $request->third_party_vehicle_model;
                $case['third_party_customer_type'] = $request->third_party_customer_type;
                $case['third_party_passenger_name'] = $request->third_party_passenger_name;
                $case['third_party_vehicle_make'] = $request->third_party_vehicle_make;
                $case['third_party_vehicle_license'] = $request->third_party_vehicle_license;
                $case['third_party_airbags_developed'] = $request->third_party_airbags_developed;
                $case['third_party_seat_belts_worn'] = $request->third_party_seat_belts_worn;


                $case['police_report'] = $request->police_report;
                $case['recorded_statement'] = $request->recorded_statement;
                $case['recorded_statement_description'] = $request->recorded_statement_description;
                $case['other_name'] = $request->other_name;
                $case['other_phone_number'] = $request->other_phone_number;
                $case['other_email_address'] = $request->other_email_address;
                $case['other_fax'] = $request->other_fax;


                $case['opponents'] = json_encode($opponentData);
                $case['opponent_advocates'] = json_encode($opponentAdvocateData);


                $case['court'] = $request->court ?? 1;
                $case['highcourt'] = $request->highcourt ?? 1;
                $case['bench'] = $request->bench ?? 1;
                $case['casetype'] = $request->casetype;
                $case['casenumber'] = $request->casenumber;
                $case['diarybumber'] = !empty($request->diarybumber) ? $request->diarybumber : null;
                $case['year'] = $request->year;

                $case['filing_date'] = $request->filing_date;
                $case['court_hall'] = $request->court_hall;
                $case['floor'] = $request->floor;
                $case['title'] = $request->title;

                $case['before_judges'] = $request->before_judges;
                $case['referred_by'] = $request->referred_by;
                $case['section'] = $request->section ?? '';
                $case['priority'] = $request->priority ?? '';
                $case['under_acts'] = $request->under_acts ?? '';
                $case['under_sections'] = $request->under_sections ?? '';
                $case['FIR_police_station'] = $request->FIR_police_station ?? '';
                $case['FIR_number'] = $request->FIR_number;
                $case['FIR_year'] = $request->FIR_year;
                $case['your_advocates'] =  $uniqueValues ? implode(',', $uniqueValues) : null;
                $case['your_team'] = $request->your_team ? implode(',', $request->your_team) : '';


                $case['created_by'] = Auth::user()->creatorId();

                $folder_name_old = Auth::user()->creatorId() . '-' . 'case-doc';
                $destinationPath = public_path('storage/uploads/case_docs/' . $folder_name_old);

                $caseDocsData = $request->folders;
                $savedData = [];

                if ($caseDocsData) {
                    foreach ($caseDocsData as $index => $caseDoc) {



                        $folder_name = $caseDoc['folder_name'] ?? '';
                        $folder_description = $caseDoc['folder_description'] ?? '';


                        $docData = [];
                        if (isset($caseDoc['folder_doc'])) {

                            foreach ($caseDoc['folder_doc'] as $ind => $docFile) {


                                $doc_name = $docFile['doc_name'] ?? '';
                                $doc_des = $docFile['doc_description'] ?? '';


                                $files = [];
                                if (isset($docFile['files'])) {

                                    $doc_files = $docFile['files'] ?? '';
                                    foreach ($doc_files as $file) {

                                        $sourcePath = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old . '/' . $file);

                                        $destinationFile = $destinationPath . '/' . $file;
                                        if (!File::exists($destinationPath)) {
                                            File::makeDirectory($destinationPath, 0755, true);
                                        }

                                        if (File::exists($sourcePath)) {
                                            File::move($sourcePath, $destinationFile);
                                        }

                                        $files[] = $folder_name_old . '/' . $file;
                                    }
                                }

                                $docData[] = [
                                    'doc_name' => $doc_name,
                                    'doc_des' => $doc_des,
                                    'files' => $files,
                                    'uploaded_by' => Auth::user()->id,
                                    'uploaded_at' => now(),
                                ];
                            }
                        }

                        $savedData[] = [
                            'folder_name' => $folder_name,
                            'folder_description' => $folder_description,
                            'docData' => $docData,
                        ];
                    }
                }

                $dbDocData = json_decode($case->case_docs);
                foreach ($dbDocData as $dbDocDataindex => $docData) {
                    if (isset($savedData[$dbDocDataindex])) {
                        $savedData[$dbDocDataindex]['docData'] = array_merge($docData->docData, $savedData[$dbDocDataindex]['docData']);
                    }
                }


                // dd(json_decode($case->case_docs),$savedData);
                $case['case_docs'] =  json_encode($savedData)  ?? '';
                $case->save();

                $this->FallSlipDraft($case, $request);

                $sourcePathDelete = public_path('storage/uploads/case_docs/tmp/' . $folder_name_old);
                if (File::exists($sourcePathDelete)) {
                    File::deleteDirectory($sourcePathDelete);
                }




                return response()->json(['success' => true, 'caseId' => $case->id]);
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
    }

    public function FallSlipDraft($case, $request)
    {

        if ($case->practice_area == 'Slip & Fall') {

            $caseFAll = CasesSlipFall::where('case_id', $case->id)->first();

            if ($caseFAll) {
                $fallSlip = CasesSlipFall::findOrFail($caseFAll->id);
            } else {
                $fallSlip = new CasesSlipFall();
            }

            $allWitness = [];

            $numOpponents = count($request->witness['witness_name']);

            for ($i = 0; $i < $numOpponents; $i++) {

                $witness = [
                    'witness_name' => $request->witness['witness_name'][$i],
                    'witness_email' => $request->witness['witness_email'][$i],
                    'witness_phone' => $request->witness['witness_phone'][$i],
                    'witness_address' => $request->witness['witness_address'][$i],
                ];

                $allWitness[] = $witness;
            }


            $inputDateString = $request->fall_c_dob;
            $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

            if (preg_match($dateFormatRegex, $inputDateString)) {
                $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
                $formattedDate = $dateObject->format('Y-m-d');
                $fallClientDob = $formattedDate;
            } else {
                $fallClientDob = $inputDateString;
            }

            $incidentDate = $request->fall_ii_incident_date;
            if (preg_match($dateFormatRegex, $incidentDate)) {
                $dateObject = Carbon::createFromFormat('D M j Y', $incidentDate);
                $formattedDate = $dateObject->format('Y-m-d');
                $fall_ii_incident_date = $formattedDate;
            } else {
                $fall_ii_incident_date = $incidentDate;
            }




            $fallSlip['case_id'] = $case->id;

            $fieldsToUpdate = config('form-fields.fallSlip');
            // dd($request->all());
            foreach ($fieldsToUpdate as $field) {
                if (isset($this->formFieldsPremissions[$field]) && $this->formFieldsPremissions[$field] == 'Yes' && isset($request[$field])) {
                    $request[$field] = $this->encryptAES($request->$field);
                }
            }

            $fallSlip['fall_c_name'] = $request->fall_c_name;
            $fallSlip['fall_c_gender'] = $request->fall_c_gender;
            $fallSlip['fall_c_marital_status'] = $request->fall_c_marital_status;
            $fallSlip['fall_c_spouse_name'] = $request->fall_c_spouse_name;
            $fallSlip['fall_c_emergency_contact_name'] = $request->fall_c_emergency_contact_name;
            $fallSlip['fall_c_emergency_contact_number'] = $request->fall_c_emergency_contact_number;
            $fallSlip['fall_c_dob'] = $fallClientDob;
            $fallSlip['fall_c_social_security'] = $request->fall_c_social_security;
            $fallSlip['fall_c_address'] = $request->fall_c_address;
            $fallSlip['fall_c_phone'] = $request->fall_c_phone;
            $fallSlip['fall_c_email'] = $request->fall_c_email;
            $fallSlip['fall_c_driver_license'] = $request->fall_c_driver_license;
            $fallSlip['fall_c_health_insurance'] = $request->fall_c_health_insurance;
            $fallSlip['fall_c_id'] = $request->fall_c_id;


            $fallSlip['fall_tpi_name'] = $request->fall_tpi_name;
            $fallSlip['fall_tpi_phone'] = $request->fall_tpi_phone;
            $fallSlip['fall_tpi_address'] = $request->fall_tpi_address;
            $fallSlip['fall_tpi_email'] = $request->fall_tpi_email;
            $fallSlip['fall_mi_name'] = $request->fall_mi_name;
            $fallSlip['fall_mi_phone'] = $request->fall_mi_phone;
            $fallSlip['fall_mi_address'] = $request->fall_mi_address;
            $fallSlip['fall_mi_email'] = $request->fall_mi_email;


            $fallSlip['fall_ii_incident_date'] = $fall_ii_incident_date;
            $fallSlip['fall_ii_time'] = $request->fall_ii_time;
            $fallSlip['fall_ii_location_of_incident'] = $request->fall_ii_location_of_incident;
            $fallSlip['fall_ii_address'] = $request->fall_ii_address;

            $fallSlip['fall_ii_maps_link'] = $request->fall_ii_maps_link;
            $fallSlip['fall_ii_cause_incident'] = $request->fall_ii_cause_incident;
            $fallSlip['fc_police_notified'] = $request->fc_police_notified;
            $fallSlip['fc_incident_report_filed'] = $request->fc_incident_report_filed;
            $fallSlip['fc_police_department'] = $request->fc_police_department;
            $fallSlip['fc_incident_report'] = $request->fc_incident_report;


            $fallSlip['fall_ifp_company_name'] = $request->fall_ifp_company_name;
            $fallSlip['fall_ifp_insured_name'] = $request->fall_ifp_insured_name;
            $fallSlip['fall_ifp_poilicy'] = $request->fall_ifp_poilicy;
            $fallSlip['fall_ifp_member'] = $request->fall_ifp_member;
            $fallSlip['fall_ifp_claim'] = $request->fall_ifp_claim;
            $fallSlip['fall_ifp_insurance_phone'] = $request->fall_ifp_insurance_phone;
            $fallSlip['fall_ifp_adjuster_name'] = $request->fall_ifp_adjuster_name;
            $fallSlip['fall_ifp_adjuster_email'] = $request->fall_ifp_adjuster_email;
            $fallSlip['fall_ifp_adjuster_phone'] = $request->fall_ifp_adjuster_phone;
            $fallSlip['fall_ifp_adjuster_fax'] = $request->fall_ifp_adjuster_fax;
            $fallSlip['fall_ifp_adjuster_policy_limits'] = $request->fall_ifp_adjuster_policy_limits;

            $fallSlip['fall_itp_company_name'] = $request->fall_itp_company_name;
            $fallSlip['fall_itp_insured_name'] = $request->fall_itp_insured_name;
            $fallSlip['fall_itp_poilicy'] = $request->fall_itp_poilicy;
            $fallSlip['fall_itp_claim'] = $request->fall_itp_claim;
            $fallSlip['fall_itp_insurance_phone'] = $request->fall_itp_insurance_phone;
            $fallSlip['fall_itp_adjuster_name'] = $request->fall_itp_adjuster_name;
            $fallSlip['fall_itp_adjuster_email'] = $request->fall_itp_adjuster_email;
            $fallSlip['fall_itp_adjuster_phone'] = $request->fall_itp_adjuster_phone;
            $fallSlip['fall_itp_adjuster_fax'] = $request->fall_itp_adjuster_fax;
            $fallSlip['fall_itp_adjuster_policy_limits'] = $request->fall_itp_adjuster_policy_limits;


            $fallSlip['witness'] = json_encode($allWitness);

            $fallSlip['fall_o_incident_report'] = $request->fall_o_incident_report;
            $fallSlip['fall_o_recorded_statements'] = $request->fall_o_recorded_statements;
            $fallSlip['fall_o_opponent_counsel'] = $request->fall_o_opponent_counsel;
            $fallSlip['fall_o_name'] = $request->fall_o_name;
            $fallSlip['fall_o_phone'] = $request->fall_o_phone;
            $fallSlip['fall_o_email'] = $request->fall_o_email;
            $fallSlip['fall_o_fax'] = $request->fall_o_fax;


            $fallSlip->save();
        }
    }
    function updateFormat($dateStr) {
         // Try to convert the input date to a timestamp
    $timestamp = strtotime($dateStr);

    if ($timestamp !== false) {
        // Successfully converted to a timestamp, now format it as 'Y-m-d'
        return date('Y-m-d', $timestamp);
    } else {
        // Unable to convert, return an error or handle it as needed
        return null;
    }
    }

    public function getUsersForCase($caseId){
       
        $usersList = Cases::where('id',$caseId)->first('your_advocates');
        return $usersList;

    }


}
