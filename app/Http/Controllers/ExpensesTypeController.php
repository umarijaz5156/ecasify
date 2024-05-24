<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpensesType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ExpensesTypeController extends Controller
{

    public function create()
    {
        return view('expenses_types.create');
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|unique:expenses_types|max:255',
        ];

        // Custom error messages (optional)
        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name has already been taken.',
            'name.max' => 'The name may not be greater than :max characters.',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules, $customMessages);

        // Check for validation failure
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        // Create a new ExpensesType
        ExpensesType::create([
            'name' => $request->input('name'), // Use input() method
            'created_by' => Auth::user()->creatorId(), // Use creatorId() method
        ]);

        return redirect()->back()->with('success', 'Expense type created successfully');
    }


    public function edit($id)
    {
        $expenseType = ExpensesType::find($id);

        if (!$expenseType) {
            // Handle the case where the ExpensesType is not found, e.g., redirect to an error page
            return redirect()->back()->with('error','Expense type not found');
        }

        return view('expenses_types.edit', compact('expenseType'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        $expenseType = ExpensesType::find($id);
        $expenseType->name = $request->name;
        $expenseType->save();

        return redirect()->back()->with('success', 'Expense type updated successfully');
    }

    public function destroy($id)
    {
        $expenseType = ExpensesType::find($id);
        if(!$expenseType){
            return redirect()->back()->with('error','Expense type not found');
        }
        $expenseType->delete();

        return redirect()->back()
            ->with('success', 'Expense type deleted successfully');
    }
}
