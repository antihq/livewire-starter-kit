<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait Prunable
{
    public static function prune(Carbon $date, int $limit = 500): int
    {
        $instance = new static;
        $total = 0;

        do {
            $affected = DB::table($instance->getTable())
                ->where('created_at', '<=', $date)
                ->orderBy('id')
                ->limit($limit)
                ->delete();

            $total += $affected;
        } while ($affected > 0);

        return $total;
    }
}
