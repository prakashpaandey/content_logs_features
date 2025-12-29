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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
