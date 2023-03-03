<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'amount',
        'account_user_id',
        'account_id',
    ];

    public function user()
    {
        return $this->hasOneThrough(User::class, Account::class, 'user_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function accountUser()
    {
        return $this->belongsTo(AccountUsers::class);
    }
}
