<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'business_name',
        'initials',
        'status',
        'avatar',
    ];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function monthlyTargets()
    {
        return $this->hasMany(MonthlyTarget::class);
    }
}
