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
        return $this->hasManyThrough(User::class, Account::class);
    }

    public function account()
    {
        return $this->hasOneThrough(Account::class, AccountUsers::class);
    }

    public function account_user(){
        return $this->belongsTo(AccountUsers::class);
    }
}
