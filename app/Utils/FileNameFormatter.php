<?php

namespace App\Utils;

class FileNameFormatter
{

    public static function generateAttendanceFileNameByNow()
    {
        $dateTimeNow = now()->format('Y-m-d_H-i');
        return 'attendances_'  . $dateTimeNow . '.xlsx';
    }
}
