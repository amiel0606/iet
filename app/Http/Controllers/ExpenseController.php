<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $expenses = Expense::getUserExpenses($user_id);
        return Inertia::render('Tracker', [
            'expenses' => $expenses,
        ]);
    }

    public function store(Request $request)
    {
        $expense = Expense::createExpense($request->all());
        return response()->json($expense); 
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::updateExpense($id, $request->all());
        return response()->json($expense); 
    }

    public function find($id)
    {
        $expense = Expense::findExpense($id)->first();
        return response()->json($expense);
    }

    public function destroy($id)
    {
        Expense::deleteExpense($id);
        return back()->with('success', 'Expense deleted successfully');
    }
}
