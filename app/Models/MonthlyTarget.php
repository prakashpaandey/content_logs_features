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
        'bs_month',
        'bs_year',
        'target_posts',
        'target_reels',
        'target_boost_budget',
        'status',
        'notes',
    ];

    protected $appends = [
        'actual_posts',
        'actual_reels',
        'actual_boost_amount',
    ];

    public function getActualPostsAttribute()
    {
        return $this->getActualPosts();
    }

    public function getActualReelsAttribute()
    {
        return $this->getActualReels();
    }

    public function getActualBoostAmountAttribute()
    {
        return $this->getActualBoostAmount();
    }

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
            $actualBoostAmount = $this->getActualBoostAmount();

            // Check if targets are met
            if ($actualPosts >= $this->target_posts && 
                $actualReels >= $this->target_reels && 
                $actualBoostAmount >= $this->target_boost_budget) {
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
        // Use explicit BS month/year if available, fallback to conversion
        $bsMonth = $this->bs_month ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($this->month)['month'];
        $bsYear = $this->bs_year ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($this->month)['year'];
        
        [$startDate, $endDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($bsMonth, $bsYear);

        return $this->client->contents()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Post')
            ->count();
    }

    public function getActualReels()
    {
        $bsMonth = $this->bs_month ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($this->month)['month'];
        $bsYear = $this->bs_year ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($this->month)['year'];
        
        [$startDate, $endDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($bsMonth, $bsYear);

        return $this->client->contents()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('type', 'Reel')
            ->count();
    }

    public function getActualBoostAmount()
    {
        $bsMonth = $this->bs_month ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($this->month)['month'];
        $bsYear = $this->bs_year ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($this->month)['year'];
        
        [$startDate, $endDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($bsMonth, $bsYear);

        return $this->client->boosts()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }
}
