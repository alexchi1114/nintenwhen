<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Game extends Model
{
    protected $casts = [
        'reveal_date' => 'datetime',
        'release_date' => 'datetime',
    ];

    public function tags() 
	{
	   return $this->belongsToMany(Tag::class);
	}

	public function franchise()
	{
		return $this->belongsTo(Franchise::class);
	}

	public function systems()
	{
		return $this->belongsToMany(System::class);
	}

	public static function getDaysSinceLastRelease($games)
	{	
		$games = $games->sort(function($a, $b){
			if($a->release_date === $b->release_date) {
				return 0;
			} elseif($a->release_date === null) {
				return -1;
			} elseif($b->release_date === null) {
				return 1;
			} else {
				return $a->release_date > $b->release_date;
			}
			return $a->release_date > $b->release_date;
		});
		if(sizeof($games) > 0) {
			return $games[0]->release_date === null ? null : $games[0]->release_date->diffInDays(Carbon::now(), absolute: true);
		} else {
			return null;
		}
	}

	public static function getAvgDaysBetweenReleases($games)
	{
		$total_days_between_releases = 0;

        foreach($games as $i => $game) {
            if($game->release_date !== null && isset($games[$i + 1])) {
                $days_between_releases = $game->release_date->diffInDays($games[$i + 1]->release_date, absolute: true);
                $total_days_between_releases += $days_between_releases;
            }
        }

        if($games->count() > 1)
        {
        	return $total_days_between_releases/($games->count() - 1);
        }
        else if($games->count() === 1) {
        	return self::getDaysSinceLastRelease($games);
        } else {
        	return null;
        }
	}

	public static function getMaxDaysBetweenReleases($games)
	{
		$max_days_between_releases = self::getDaysSinceLastRelease($games);
        foreach($games as $i => $game) {
            if($game->release_date !== null && isset($games[$i + 1])) {
                $days_between_releases = $game->release_date->diffInDays($games[$i + 1]->release_date, absolute: true);
                if($days_between_releases > $max_days_between_releases) {
                    $max_days_between_releases = $days_between_releases;
                }
            }
        }

        return $max_days_between_releases;
	}
}
