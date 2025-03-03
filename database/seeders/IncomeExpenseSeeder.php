<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Income;
use App\Models\Expense;
use App\Models\User;

class IncomeExpenseSeeder extends Seeder
{
    public function run()
    {
        $user = User::first(); 
        Income::create([
            'user_id' => $user->id,
            'date' => now(),
            'category' => 'Salary',
            'description' => 'Monthly salary',
            'amount' => 50000,
        ]);

        Expense::create([
            'user_id' => $user->id,
            'date' => now(),
            'category' => 'Groceries',
            'description' => 'Bought food for the week',
            'amount' => 2000,
        ]);
    }
}
