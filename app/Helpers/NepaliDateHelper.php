<?php

namespace App\Helpers;

use Carbon\Carbon;

class NepaliDateHelper
{
    /**
     * Map of BS month starts in AD for the year 2082 BS (2025-2026 AD)
     * Format: AD Month => Split Day (where the NEXT BS month starts)
     */
    private static $splits = [
        1 => 15, // Jan 15 starts Magh (10)
        2 => 13, // Feb 13 starts Falgun (11)
        3 => 15, // Mar 15 starts Chaitra (12)
        4 => 14, // Apr 14 starts Baishakh (1)
        5 => 15, // May 15 starts Jestha (2)
        6 => 15, // Jun 15 starts Asar (3)
        7 => 17, // Jul 17 starts Shrawan (4)
        8 => 17, // Aug 17 starts Bhadra (5)
        9 => 17, // Sep 17 starts Ashwin (6)
        10 => 18, // Oct 18 starts Kartik (7)
        11 => 17, // Nov 17 starts Mangsir (8)
        12 => 16, // Dec 16 starts Poush (9)
    ];

    /**
     * Approximate conversion from AD to BS Month/Year
     */
    public static function adToBs($adDate)
    {
        $carbon = is_string($adDate) ? Carbon::parse($adDate) : $adDate;
        $m = $carbon->month;
        $y = $carbon->year;
        $d = $carbon->day;

        $split = self::$splits[$m] ?? 15;

        // Content date conversion (using splits)
        if ($m >= 4) {
            if ($d >= $split) {
                $bsMonth = $m - 3;
                $bsYear = $y + 57;
                $bsDay = $d - $split + 1;
            } else {
                $bsMonth = $m - 4;
                if ($bsMonth < 1) {
                    $bsMonth = 12;
                    $bsYear = $y + 56;
                } else {
                    $bsYear = $y + 57;
                }
                // Previous AD month's split and actual days
                $prevMonth = $carbon->copy()->subMonth();
                $prevSplit = self::$splits[$prevMonth->month] ?? 15;
                $bsDay = $d + ($prevMonth->daysInMonth - $prevSplit) + 1;
            }
        } else {
            if ($d >= $split) {
                $bsMonth = $m + 9;
                $bsYear = $y + 56;
                $bsDay = $d - $split + 1;
            } else {
                $bsMonth = $m + 8;
                $bsYear = $y + 56;
                $prevMonth = $carbon->copy()->subMonth();
                $prevSplit = self::$splits[$prevMonth->month] ?? 15;
                $bsDay = $d + ($prevMonth->daysInMonth - $prevSplit) + 1;
            }
        }

        return ['month' => $bsMonth, 'year' => $bsYear, 'day' => $bsDay];
    }

    /**
     * Conversion for Target dates (stored as Y-m-01)
     * Maps AD Month back to BS Month using the simplified 1-to-1 mapping
     */
    public static function representativeAdToBs($adDate)
    {
        $carbon = is_string($adDate) ? Carbon::parse($adDate) : $adDate;
        $m = $carbon->month;
        $y = $carbon->year;

        if ($m >= 4) {
            $bsMonth = $m - 3; // Apr -> 1, Dec -> 9
            $bsYear = $y + 57;
        } else {
            $bsMonth = $m + 9; // Jan -> 10, Mar -> 12
            $bsYear = $y + 56;
        }

        return ['month' => $bsMonth, 'year' => $bsYear];
    }

    /**
     * Approximate conversion from BS Month/Year to AD Month/Year
     * This returns the AD month that CONTAINS THE START of the BS month.
     */
    public static function bsToAd($bsMonth, $bsYear)
    {
        if ($bsMonth <= 9) {
            $adMonth = $bsMonth + 3;
            $adYear = $bsYear - 57;
        } else {
            $adMonth = $bsMonth - 9;
            $adYear = $bsYear - 56;
        }

        return ['month' => $adMonth, 'year' => $adYear];
    }

    /**
     * Returns the AD date range for a given BS month and year
     */
    public static function getBsMonthRange($bsMonth, $bsYear)
    {
        // 1. Find the AD date for BS 1st of the given month
        // For Poush (9) 2082, BS 1st is Dec 16, 2025.
        // For Magh (10) 2082, BS 1st is Jan 15, 2026.
        
        $startYear = ($bsMonth <= 9) ? ($bsYear - 57) : ($bsYear - 56);
        
        // Find split for the corresponding AD month
        $approxAdMonth = ($bsMonth <= 9) ? ($bsMonth + 3) : ($bsMonth - 9);
        $split = self::$splits[$approxAdMonth] ?? 15;
        
        $startDate = Carbon::create($startYear, $approxAdMonth, $split)->startOfDay();
        
        // 2. Find the AD date for the start of the NEXT BS month
        $nextMonth = $bsMonth + 1;
        $nextYear = $bsYear;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }
        
        $nextStartYear = ($nextMonth <= 9) ? ($nextYear - 57) : ($nextYear - 56);
        $nextApproxAdMonth = ($nextMonth <= 9) ? ($nextMonth + 3) : ($nextMonth - 9);
        $nextSplit = self::$splits[$nextApproxAdMonth] ?? 15;
        
        $endDate = Carbon::create($nextStartYear, $nextApproxAdMonth, $nextSplit)->subDay()->endOfDay();
        
        return [$startDate, $endDate];
    }
}
