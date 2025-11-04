<?php

use Carbon\Carbon;

if (!function_exists('backChangeFormatDate')) {
    function backChangeFormatDate($date)
    {
        if (!$date) return null;
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        return $carbonDate->format('d/m/Y H:i:s');
    }
}