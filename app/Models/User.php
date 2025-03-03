<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_users';

    protected $fillable = ['name', 'email', 'password'];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'user_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'user_id');
    }
}
