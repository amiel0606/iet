<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'tbl_expenses';
    protected $fillable = ['Date', 'Category', 'Description', 'Amount', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getUserExpenses($user_id)
    {
        return self::where('user_id', $user_id)
            ->select('id', 'date', 'amount', 'category', 'description')
            ->get();
    }

    public static function createExpense($data)
    {
        return self::create([
            'Date' => $data['date'] ?? now()->toDateString(),
            'Category' => $data['category'],
            'Description' => $data['description'],
            'Amount' => $data['amount'],
            'user_id' => Auth::id(),
        ]);
    }

    public static function findExpense($expense_id)
    {
        return self::where('id', $expense_id)
            ->where('user_id', Auth::id())
            ->select('id', 'date', 'amount', 'category', 'description');
    }

    public static function searchExpense($data)
    {
        return self::where('user_id', Auth::id())
            ->when(!empty($data['search']), function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('category', 'like', "%{$data['search']}%")
                        ->orWhere('description', 'like', "%{$data['search']}%");
                });
            })
            ->when(!empty($data['date']), function ($query) use ($data) {
                $query->whereDate('date', $data['date']);
            })
            ->select('id', 'date', 'amount', 'category', 'description')
            ->get();
    }

    public static function updateExpense($id, $data)
    {
        return self::where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'Category' => $data['category'],
                'Description' => $data['description'],
                'Amount' => $data['amount'],
            ]);
    }

    public static function deleteExpense($id)
    {
        return self::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();
    }
}
