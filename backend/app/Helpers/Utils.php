<?php

namespace App\Helpers;

use Carbon\Carbon;

class Utils
{
    /**
     * Calculate the difference in days between two dates.
     *
     * @param string $startDate The start date in 'Y-m-d' format.
     * @param string $endDate The end date in 'Y-m-d' format.
     * @return int The number of days between the start date and end date.
     */

    public static function diffInDays(string $startDate, string $endDate): int
    {
        return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
    }

    /**
     * Create a date string that is a certain number of days in the future.
     *
     * @param int $daysToAdd The number of days to add to the current date.
     * @return string A date string in 'Y-m-d' format that is $daysToAdd days in the future.
     */
    public static function createDate($daysToAdd): string
    {
        return now()->addDays($daysToAdd)->format('Y-m-d');
    }

}

