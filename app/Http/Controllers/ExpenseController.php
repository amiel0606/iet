<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::getUserExpenses();
        return Inertia::render('Tracker', [
            'expenses' => $expenses,
        ]);
    }

    public function store(Request $request)
    {
        Expense::createExpense($request->all());
        return back()->with('success', 'Expense added successfully');
    }

    public function update(Request $request, $id)
    {
        Expense::updateExpense($id, $request->all());
        return back()->with('success', 'Expense updated successfully');
    }

    public function destroy($id)
    {
        Expense::deleteExpense($id);
        return back()->with('success', 'Expense deleted successfully');
    }
}
