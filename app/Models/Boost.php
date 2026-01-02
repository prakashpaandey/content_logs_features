<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Client;

class Boost extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'platform',
        'boosted_content_type',
        'date',
        'url',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
