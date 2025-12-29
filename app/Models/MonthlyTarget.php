<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client; // Added for the relationship

class MonthlyTarget extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'month',
        'target_posts',
        'target_reels',
        'status',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
