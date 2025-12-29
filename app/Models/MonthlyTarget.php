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

    public function checkCompletionStatus()
    {
        try {
            // Get actual counts
            $actualPosts = $this->getActualPosts();
            $actualReels = $this->getActualReels();

            // Check if targets are met
            if ($actualPosts >= $this->target_posts && $actualReels >= $this->target_reels) {
                if ($this->status !== 'completed') {
                    $this->update(['status' => 'completed']);
                }
            } else {
                // Determine if we should revert from 'completed' to 'active'
                // Only revert if it WAS completed but now isn't (e.g. content deleted)
                if ($this->status === 'completed') {
                     $this->update(['status' => 'active']);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error checking target completion: ' . $e->getMessage());
        }
    }

    public function getActualPosts()
    {
        return $this->client->contents()
            ->whereYear('date',  \Carbon\Carbon::parse($this->month)->year)
            ->whereMonth('date', \Carbon\Carbon::parse($this->month)->month)
            ->where('type', 'Post')
            ->count();
    }

    public function getActualReels()
    {
        return $this->client->contents()
            ->whereYear('date',  \Carbon\Carbon::parse($this->month)->year)
            ->whereMonth('date', \Carbon\Carbon::parse($this->month)->month)
            ->where('type', 'Reel')
            ->count();
    }
}
