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

    public function getAll()
    {
        $user_id = Auth::id();
        $incomes = Expense::getUserIncomes($user_id);
        return response()->json($incomes);
    }

    public function store(Request $request)
    {
        $expense = Expense::createExpense($request->all());
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findExpense($id)->first();
        Expense::updateExpense($id, $request->all());
        return response()->json($expense);
    }

    public function find($id)
    {
        $expense = Expense::findExpense($id)->first();
        return response()->json($expense);
    }

    public function search(Request $request)
    {
        $expenses = Expense::searchExpense($request->all());
        return response()->json($expenses);
    }

    public function destroy($id)
    {
        Expense::deleteExpense($id);
        return back()->with('success', 'Expense deleted successfully');
    }

    public function bulkImport(Request $request)
    {
        try {
            foreach ($request->data as $item) {
                $item['date'] = \Carbon\Carbon::parse($item['date'])->format('Y-m-d');
                Expense::createExpense($item);
            }
            return response()->json(['message' => 'Bulk import successful!'], 200);
        } catch (\Exception $e) {
            \Log::error("Bulk Import Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
    
}
