<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Direct extends Model
{
    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function tags()
    {
        return $this->belongsToMany(DirectTag::class);
    }

    public static function getDaysSinceLastDirect($directs)
    {
        $directs = $directs->sort(function($a, $b) {
            if($a->start_time === $b->start_time) {
                return 0;
            } elseif($a->start_time === null) {
                return -1;
            } elseif($b->start_time === null) {
                return 1;
            } else {
                return $a->start_time > $b->start_time;
            }
        });

        if(sizeof($directs) > 0) {
            return $directs[0]->start_time === null ? null : $directs[0]->start_time->diffInDays(Carbon::now(), absolute: true);
        } else {
            return null;
        }
    }

    public static function getAvgDaysBetweenDirects($directs)
    {
        $total_days_between = 0;

        foreach($directs as $i => $direct) {
            if($direct->start_time !== null && isset($directs[$i + 1])) {
                $days_between = $direct->start_time->diffInDays($directs[$i + 1]->start_time, absolute: true);
                $total_days_between += $days_between;
            }
        }

        if($directs->count() > 1) {
            return $total_days_between / ($directs->count() - 1);
        } elseif($directs->count() === 1) {
            return self::getDaysSinceLastDirect($directs);
        } else {
            return null;
        }
    }

    public static function getMaxDaysBetweenDirects($directs)
    {
        $max_days_between = self::getDaysSinceLastDirect($directs);

        foreach($directs as $i => $direct) {
            if($direct->start_time !== null && isset($directs[$i + 1])) {
                $days_between = $direct->start_time->diffInDays($directs[$i + 1]->start_time, absolute: true);
                if($days_between > $max_days_between) {
                    $max_days_between = $days_between;
                }
            }
        }

        return $max_days_between;
    }
}
