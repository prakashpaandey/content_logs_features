<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'platform',
        'type', 
        'date',
        'url',
        'remarks',
    ];

    protected $casts = [
        'platform' => 'array',
        'url' => 'array',
        'date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
