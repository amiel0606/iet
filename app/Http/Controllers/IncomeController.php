<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Income;

class IncomeController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        
        // Fetch incomes for the authenticated user only
        $incomes = Income::where('user_id', $user_id)
            ->select('id', 'date', 'amount', 'category', 'description')
            ->get();

        \Log::info('Incomes:', $incomes->toArray()); // Debugging log

        return Inertia::render('Tracker', [
            'incomes' => $incomes,
        ]);
    }

    public function store(Request $request)
    {
        DB::table('tbl_income')->insert([
            'Date' => now()->toDateString(),
            'Category' => $request->category,
            'Description' => $request->description,
            'Amount' => $request->amount,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Income added successfully');
    }

    public function update(Request $request, $id)
    {
        // Ensure the income belongs to the authenticated user
        DB::table('tbl_income')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'Category' => $request->category,
                'Description' => $request->description,
                'Amount' => $request->amount,
            ]);

        return back()->with('success', 'Income updated successfully');
    }

    public function destroy($id)
    {
        // Ensure the income belongs to the authenticated user before deleting
        DB::table('tbl_income')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Income deleted successfully');
    }
}
