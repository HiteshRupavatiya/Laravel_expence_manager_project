<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_number',
        'is_default',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account_users()
    {
        return $this->hasMany(AccountUsers::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
