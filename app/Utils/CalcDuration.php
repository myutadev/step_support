<?php

namespace App\Utils;

use Carbon\Carbon;
use Carbon\CarbonInterval;

/**
 *文字列形式の開始時間、終了時間を受け取り、CarbonInterval形式の経過時間のオブジェクトを返す
 */
class CalcDuration
{
    public static function getCarbonIntervalStartFromStr($start, $end): CarbonInterval
    {

        $startCarbon = Carbon::parse($start);
        $endCarbon = Carbon::parse($end);

        return CarbonInterval::instance($endCarbon->diff($startCarbon));
    }
}
