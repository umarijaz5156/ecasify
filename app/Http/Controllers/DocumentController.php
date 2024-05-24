<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Cases;
use App\Models\DocType;
use App\Models\Document;
use App\Models\FolderActivity;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{



    public function getDocs(Request $request, $id)
    {

        $case = Cases::find($id);
        $originalPath = storage_path('app/public/uploads/case_docs/');
        // $case->case_docs
        if ($case->case_docs) {
            $updatedDocs = [];
            foreach (json_decode($case->case_docs) as $index1 => $folder) {
                $updatedDocs[$index1] = $folder;
                foreach ($folder->docData as $index2 => $document) {
                    $updatedDocs[$index1]->docData[$index2] = $document;
                    foreach ($document->files as $index => $file) {
                        // dd();
                        $file = decryptFile($originalPath . $file, pathinfo($file)['extension']);
                        $updatedDocs[$index1]->docData[$index2]->files[$index] = $file;
                    }
                }
            }
        }
        // dd(json_decode($case->case_docs),$updatedDocs);
        $case->case_docs = json_encode($updatedDocs);
        return response()->json(['case_docs' => $case->case_docs]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('manage document')) {

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $cases = Cases::whereIn('created_by', $userIds)->orderBy('id', 'DESC')->get();
            } else {

                $user = Auth::user()->id;

                $cases = DB::table("cases")
                    ->select("cases.*")
                    ->where(function ($query) use ($user) {
                        $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                            ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                    })
                    ->orderBy('id', 'DESC')
                    ->get();
            }

            //  add dot . before $request->type;
            $selectedType = $request->type;
            $allowedExtensions = $request->type && $request->type != 0   ? array($request->type) :array()  ;

            $allDocuments = [];
            foreach ($cases as $case) {
                if (!empty($case->case_docs)) {
                    $caseDocs = json_decode($case->case_docs, true);
                    foreach ($caseDocs as $doc) {
                        $folderName = $doc['folder_name'];

                        foreach ($doc['docData'] as $docData) {
                            foreach ($docData['files'] as $file) {
                                // Extract the file extension
                                $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                if (empty($allowedExtensions) || in_array($fileExtension, $allowedExtensions)) {
                                    if (preg_match('/\/(\d+)\./', $file, $matches)) {
                                        $numericPart = $matches[1];
                                        $timestamp = intval($numericPart);
                                        $humanReadableTime = date('F j, Y, g:i a', $timestamp);
                                    } else {
                                        $humanReadableTime = '----';
                                    }
                                    $documentInfo = [
                                        'case_id' => $case->id,
                                        'case_name' => $case->name,
                                        'case_type' => $case->practice_area,
                                        'folder_name' => $folderName,
                                        'doc_name' => $docData['doc_name'],
                                        'doc_des' => $docData['doc_des'],
                                        'uploaded_by' => $docData['uploaded_by'] ?? '',
                                        'uploaded_at' => $docData['uploaded_at'] ?? '',
                                        'file' => $file,
                                        'human_readable_time' => $humanReadableTime,
                                    ];

                                    $allDocuments[] = $documentInfo;
                                }
                            }
                        }

                        usort($allDocuments, function ($a, $b) {
                            $aTimestamp = strtotime($a['human_readable_time']);
                            $bTimestamp = strtotime($b['human_readable_time']);

                            if ($aTimestamp === $bTimestamp) {
                                return 0;
                            }

                            return ($aTimestamp > $bTimestamp) ? -1 : 1;
                        });
                    }
                }
            }


            // dd($allDocuments);

            $docs = Document::where('created_by', Auth::user()->creatorId())->get();
            return view('documents.index', compact('docs', 'allDocuments','selectedType'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('create document')) {
            // $types = DocType::where('created_by',Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $cases = Cases::where('created_by',Auth::user()->creatorId())->get()->pluck('name','id');

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $cases = Cases::whereIn('created_by', $userIds)->orderBy('id', 'DESC')->get()->pluck('name', 'id');
            } else {
                $user = Auth::user()->id;

                $cases = DB::table("cases")
                    ->select("cases.*")
                    ->where(function ($query) use ($user) {
                        $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                            ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                    })
                    ->orderBy('id', 'DESC')
                    ->get()->pluck('name', 'id');
            }


            return view('documents.create', compact('cases'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // dd($request->all());
        if (Auth::user()->can('create document')) {


            $case_doc = Cases::where('id', $request['case_id'])->first('case_docs');
            $caseDocsArray = json_decode($case_doc->case_docs, true);


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
                            }else{
                                return redirect()->back()->with('error', __('Document Not Uploaded.'));
                            }

                            $docData[] = [
                                'doc_name' => $doc_name,
                                'doc_des' => $doc_des,
                                'files' => $files,
                                'uploaded_by' => Auth::user()->id,
                                'uploaded_at' => now(),

                            ];
                        }
                    }else{
                        return redirect()->back()->with('error', __('Document Not Uploaded.'));

                    }

                    $savedData[] = [
                        'folder_name' => $folder_name,
                        'folder_description' => $folder_description,
                        'docData' => $docData,
                    ];
                }
           

            $case = Cases::find($request->case_id);

            $dbDocData = json_decode($case->case_docs);
            foreach ($dbDocData as $dbDocDataindex => $docData) {
                if (isset($savedData[$dbDocDataindex])) {
                    $savedData[$dbDocDataindex]['docData'] = array_merge($docData->docData, $savedData[$dbDocDataindex]['docData']);
                }
            }

            $updatedFields = [];
            if ($request->folders) {
                foreach ($request->folders as $ind => $caseDoc) {

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

            $updatedFieldsString = implode(', ', $updatedFields);


            $caseName = Cases::where('id', $request['case_id'])->first('name');
            $random_seconds = [10, 15, 20, 25, 30];
            $random_value = $random_seconds[array_rand($random_seconds)];

            $timeLine = FolderActivity::create([
                'user_id' => Auth::user()->id,
                'case_id' => $request['case_id'],
                'log' => 'New Document Added in ' .  $caseName->name  . ' Case',
                'start_time' => now(),
                'end_time' => now()->addSeconds($random_value),
                'edit_case' =>  $updatedFieldsString,

            ]);

            $case->case_docs = $savedData;
            $case->save();

            $fileNames = implode(', ', $files);

            Activity::create([
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->creatorId(),
                'target_id' => $case->id,
                'target_type' => 'Case',
                'action' => 'Updated',
                'file' => $fileNames,
            ]);

            return redirect()->route('documents.index')->with('success', __('Document successfully created.'));

        }else{
            return redirect()->back()->with('error', __('Document Not Uploaded.'));

        }

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if (Auth::user()->can('view document')) {
            $doc = Document::find($id);
            $cases = '-';
            if (!empty($doc->cases)) {
                $cases = Cases::whereIn('id', explode(',', $doc->cases))->get()->pluck('title')->toArray();
                $cases = implode(',', $cases);
            }
            return view('documents.view', compact('doc', 'cases'));
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
    public function edit($id)
    {
        if (Auth::user()->can('edit document')) {
            $doc = Document::find($id);
            $types = DocType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $cases = Cases::where('created_by', Auth::user()->creatorId())->get()->pluck('title', 'id');

            $data = explode(',', $doc->cases);
            $my_cases = Cases::whereIn('id', $data)->get()->pluck('id');

            return view('documents.edit', compact('doc', 'types', 'cases', 'my_cases'));
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
        if (Auth::user()->can('edit document')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'term' => 'required',
                    'type' => 'required',
                    'judgement_date' => 'required',
                    'expiry_date' => 'required',
                    'purpose' => 'required',
                    'first_party' => 'required',
                    'second_party' => 'required',
                    'headed_by' => 'required',
                    'description' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $doc = Document::find($id);
            $doc['term'] = $request->term;
            $doc['type'] = $request->type;
            $doc['judgement_date'] = $request->judgement_date;
            $doc['expiry_date'] = $request->expiry_date;
            $doc['purpose'] = $request->purpose;
            $doc['first_party'] = $request->first_party;
            $doc['second_party'] = $request->second_party;
            $doc['headed_by'] = $request->headed_by;
            $doc['description'] = $request->description;
            $doc['created_by'] = Auth::user()->id;
            $doc['cases'] = !empty($request->cases) ? implode(',', $request->cases) : null;

            if (!empty($request->file('file'))) {
                $dir        = 'uploads/documents/';
                $file_path = $dir . $doc->file;

                $image_size = $request->file('file')->getSize();

                $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);

                if ($result == 1) {

                    Utility::changeStorageLimit(Auth::user()->creatorId(), $file_path);
                    $filenameWithExt = $request->file('file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('file')->getClientOriginalExtension();
                    $fileNameToStores = 'document_' . time() . '.' . $extension;

                    $settings = Utility::getStorageSetting();
                    if ($settings['storage_setting'] == 'local') {
                        $dir = 'uploads/documents/';
                    } else {
                        $dir = 'uploads/documents/';
                    }
                    $path = Utility::upload_file($request, 'file', $fileNameToStores, $dir, []);

                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }

                    $filesize = number_format($request->file('file')->getSize() / 1000000, 4);

                    $doc['file'] = $fileNameToStores;
                    $doc['doc_size'] = $filesize;
                }
            }

            $doc->save();

            return redirect()->route('documents.index')->with('success', __('Document successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
       
        if (Auth::user()->can('delete document')) {
            $doc = Document::find($id);
            if ($doc) {

                $filepath = storage_path('uploads/documents/' . $doc->file);
                Utility::changeStorageLimit(Auth::user()->creatorId(), 'uploads/documents/' . $doc->file);

                if (File::exists($filepath)) {
                    File::delete($filepath);
                }

                $doc->delete();
            }
            return redirect()->route('documents.index')->with('success', __('Document successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
