<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Expense extends Model
{
    use HasFactory;

    protected $table = 'tbl_expenses';
    protected $fillable = ['Date', 'Category', 'Description', 'Amount', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
