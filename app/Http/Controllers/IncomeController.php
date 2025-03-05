<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Income;
use App\Models\Expense;

class IncomeController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();

        return Inertia::render('Tracker', [
            'incomes' => Income::getUserIncomes($user_id),
            'expenses' => Expense::getUserExpenses($user_id),
        ]);
    }

    public function getAll()
    {
        $user_id = Auth::id();
        $incomes = Income::getUserIncomes($user_id);
        $expenses = Expense::getUserExpenses($user_id);

        return response()->json([
            'incomes' => $incomes,
            'expenses' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $income = Income::createIncome($request->all());
        return response()->json($income);
    }

    public function update(Request $request, $id)
    {
        $income = Income::findIncome($id)->first();
        Income::updateIncome($id, $request->all());
        return response()->json($income);
    }

    public function find($id)
    {
        $income = Income::findIncome($id)->first();
        return response()->json($income);
    }

    public function search(Request $request)
    {
        $incomes = Income::searchIncome($request->all());
        return response()->json($incomes);
    }

    public function destroy($id)
    {
        Income::deleteIncome($id);
        return back()->with('success', 'Income deleted successfully');
    }

    public function bulkImport(Request $request)
    {
        try {
            foreach ($request->data as $item) {
                $item['date'] = \Carbon\Carbon::parse($item['date'])->format('Y-m-d'); // ✅ Format date correctly
                Income::createIncome($item);
            }
            return response()->json(['message' => 'Bulk import successful!'], 200);
        } catch (\Exception $e) {
            \Log::error("Bulk Import Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
    
}