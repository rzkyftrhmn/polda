<?php

use App\Support\Alert;
use Carbon\Carbon;

if (!function_exists('backChangeFormatDate')) {
    function backChangeFormatDate($date)
    {
        if (!$date) return null;
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        return $carbonDate->format('d/m/Y H:i:s');
    }
}

if (! function_exists('alert')) {
    function alert(): Alert
    {
        return app(Alert::class);
    }
}