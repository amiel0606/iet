<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Income extends Model
{
    use HasFactory;

    protected $table = 'tbl_income';
    protected $fillable = ['Date', 'Category', 'Description', 'Amount', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getUserIncomes($user_id)
    {
        return self::where('user_id', $user_id)
            ->select('id', 'date', 'amount', 'category', 'description')
            ->get();
    }

    public static function createIncome($data)
    {
        return self::create([
            'Date' => $data['date'] ?? now()->toDateString(),
            'Category' => $data['category'],
            'Description' => $data['description'],
            'Amount' => $data['amount'],
            'user_id' => Auth::id(),
        ]);
    }

    public static function updateIncome($id, $data)
    {
        return self::where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'Category' => $data['category'],
                'Description' => $data['description'],
                'Amount' => $data['amount'],
            ]);
    }

    public static function findIncome($id)
    {
        return self::where('id', $id)
            ->where('user_id', Auth::id())
            ->select('id', 'date', 'amount', 'category', 'description');
    }

    public static function searchIncome($data)
    {
        return self::where('user_id', Auth::id())
            ->where(function ($query) use ($data) {
                $query->where('category', 'like', "%{$data['search']}%")
                    ->orWhere('description', 'like', "%{$data['search']}%")
                    ->orWhere('amount', 'like', "%{$data['search']}%")
                    ->orWhere('date', 'like', "%{$data['search']}%");
            })
            ->select('id', 'date', 'amount', 'category', 'description')
            ->get();
    }

    public static function deleteIncome($id)
    {
        return self::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();
    }
}
