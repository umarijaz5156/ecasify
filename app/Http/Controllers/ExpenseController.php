<?php

namespace App\Http\Controllers;

use App\Models\Cases;
use App\Models\Expense;
use App\Models\User;
use App\Models\Utility;
use App\Models\ExpensesType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('manage expense')) {
            $expenses = Expense::where('created_by', Auth::user()->creatorId())->get();

            return view('expense.index', compact('expenses'));
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
        if (Auth::user()->can('create expense')) {
            $cases = Cases::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $members = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'advocate')->get()->pluck('name', 'id');

            $payments_data = Utility::getCompanyPaymentSetting(Auth::user()->id);
            $payTypes = Utility::getCompanyOnPyaments($payments_data);

            // expensetye
            $expensetype = ExpensesType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');;

            return view('expense.create', compact('cases', 'members', 'payTypes','expensetype'));
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

        if (Auth::user()->can('create expense')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
                'description' => 'nullable|string',
                'money' => 'required|numeric|not_regex:/-/',
                'type' => 'required|numeric',
                'member' => 'nullable|numeric',
                'case' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $docFile = $request->attachment;
            $folderNameOld = Auth::user()->creatorId() . '-' . 'expense-doc';
            $destinationPath = public_path('storage/uploads/expense_docs/' . $folderNameOld);
            $files = [];

            if ($docFile) {
                foreach ($docFile as $file) {
                    $sourcePath = public_path('storage/uploads/expense_docs/tmp/' . $folderNameOld . '/' . $file);
                    $destinationFile = $destinationPath . '/' . $file;

                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }

                    if (File::exists($sourcePath)) {
                        File::move($sourcePath, $destinationFile);
                    }

                    $files[] = $folderNameOld . '/' . $file;
                }
            }

            $docData = [
                'files' => $files,
                'uploaded_by' => Auth::user()->id,
                'uploaded_at' => now(),
            ];

            $expense = new Expense($request->except('attachment'));
            $expense->attachment = json_encode($docData);
            $expense->created_by = Auth::user()->creatorId();
            $expense->save();

            return redirect()->route('expenses.index')->with('success', __('Expense successfully created.'));
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
        if (Auth::user()->can('view expense')) {
            $cases = Cases::get()->pluck('name', 'id');
            $members = User::get()->pluck('name', 'id');
            $expense = Expense::find($id);
            return view('expense.view', compact('cases', 'members', 'expense'));
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
        if (Auth::user()->can('edit expense')) {
            $cases = Cases::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $members = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'advocate')->get()->pluck('name', 'id');
            $expense = Expense::find($id);
            $payments_data = Utility::getCompanyPaymentSetting(Auth::user()->id);
            $payTypes = Utility::getCompanyOnPyaments($payments_data);
            // expenseTypes
            $expenseTypes = ExpensesType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');;

            return view('expense.edit', compact('cases', 'members', 'expense', 'payTypes','expenseTypes'));
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
        if (Auth::user()->can('edit expense')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
                'description' => 'nullable|string',
                'money' => 'required|numeric|not_regex:/-/',
                'type' => 'required|numeric',
                'member' => 'nullable|numeric',
                'case' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
            $expense = Expense::find($id);
            $docFile = $request->attachment;
            $folderNameOld = Auth::user()->creatorId() . '-' . 'expense-doc';
            $destinationPath = public_path('storage/uploads/expense_docs/' . $folderNameOld);
            $files = [];
            if (!empty($docFile)) {
                foreach ($docFile as $file) {
                    $sourcePath = public_path('storage/uploads/expense_docs/tmp/' . $folderNameOld . '/' . $file);
                    $destinationFile = $destinationPath . '/' . $file;
                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }
                    if (File::exists($sourcePath)) {
                        File::move($sourcePath, $destinationFile);
                    }
                    $files[] = $folderNameOld . '/' . $file;
                }
            }
            $docData = [
                'files' => $files,
                'uploaded_by' => Auth::user()->id,
                'uploaded_at' => now(),
            ];
            // first get $expense->attachment add coming file and array unique than store
            $oldFiles = json_decode($expense->attachment, true)['files'] ?? [];
            $newFiles = array_unique(array_merge($oldFiles, $files));
            $docData['files'] = $newFiles;
            $expense->attachment = json_encode($docData);            
            $expense->title = $request->title;
            $expense->date = $request->date;
            $expense->description = $request->description;
            $expense->money = $request->money;
            $expense->type = $request->type;
            $expense->member = $request->member;
            $expense->case = $request->case;
            $expense->notes = $request->notes;
            $expense->save();
            return redirect()->route('expenses.index')->with('success', __('Expense successfully created.'));
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
        if (Auth::user()->can('delete expense')) {
            $expense = Expense::find($id);
            //  also delete files
            $files = json_decode($expense->attachment, true)['files'] ?? [];
            // $folderNameOld = Auth::user()->creatorId() . '-' . 'expense-doc';
            $destinationPath = public_path('storage/uploads/expense_docs/');
            if ($expense) {
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $destinationFile = $destinationPath . '/' . $file;
                        if (File::exists($destinationFile)) {
                            File::delete($destinationFile);
                        }
                    }
                }
                $expense->delete();
            }
            return redirect()->route('expenses.index')->with('success', __('Expense successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    // fileUpload
    public function fileUpload(Request $request)
    {
        if (Auth::user()->can('create expense')) {
            $localStorageValidation = Utility::getValByName('local_storage_validation', 1);
            $localStorageMaxUploadSize = Utility::getValByName('local_storage_max_upload_size', 1);
    
            $validator = Validator::make($request->all(), [
                'file' => $localStorageValidation . '|' . $localStorageMaxUploadSize,
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->getMessageBag()->first()]);
            }
    
            $folderName = Auth::user()->creatorId() . '-' . 'expense-doc';
            $uploadedFiles = $request->file('attachment') ?? [];
    
            $fileUrls = [];
    // dd($uploadedFiles);
            foreach ($uploadedFiles as $fileIndex => $uploadedFile) {
                if ($uploadedFile) {
                    $destinationPath = public_path('storage/uploads/expense_docs/tmp/' . $folderName);
                    $fileName = time() . '.' . $uploadedFile->getClientOriginalName();
                    $uploadedFile->move($destinationPath, $fileName);
    
                    encryptFile($destinationPath . '/' . $fileName);
                }
            }
    
            return response()->json([
                'success' => true,
                'fileUrl' => $fileName,
            ]);
        } else {
            return response()->json(['error' => __('Permission Denied.')]);
        }
    }
    // delete expense doc
    public function deleteExpenseDoc(Request $request)
    {
        if (Auth::user()->can('create expense')) {
        //   request->id get expense->attachment and update and alos delete file 
            $expense = Expense::find($request->id);
            $files = json_decode($expense->attachment, true)['files'] ?? [];
            $folderNameOld = Auth::user()->creatorId() . '-' . 'expense-doc';
            $destinationPath = public_path('storage/uploads/expense_docs/');
            // $request->file => "2-expense-doc/1698343358.image.png" delete and update  expense->attachment
            if ($expense) {
                if (!empty($files)) {
                    foreach ($files as $file) {
                        if ($file == $request->file) {
                            $destinationFile = $destinationPath . '/' . $file;
                            if (File::exists($destinationFile)) {
                                File::delete($destinationFile);
                            }
                        }
                    }
                }
                // update expense->attachment
                $oldFiles = json_decode($expense->attachment, true)['files'] ?? [];
                $newFiles = array_diff($oldFiles, [$request->file]);
                // dd($newFiles);
                $docData = [
                    'files' => $newFiles,
                    'uploaded_by' => Auth::user()->id,
                    'uploaded_at' => now(),
                ];
                $expense->attachment = json_encode($docData);
                $expense->save();
            }
    
            return response()->json( [
                'status' => 'success',
                'message' => __('File successfully deleted.'),
                'fileCount' => count($newFiles)
            ]); 
            
        } else {
            return response()->json(['error' => __('Permission Denied.')]);
        }
    }
    
}
