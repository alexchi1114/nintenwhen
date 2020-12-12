<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Game;

class Franchise extends Model
{
    public function games()
    {
    	return $this->hasMany(Game::class);
    }

    public function getAllReleasedGames() {
    	$franchise = $this;
    	return Game::select('games.*')
            ->join('franchises', 'games.franchise_id', '=', 'franchises.id')
            ->where(function($q) use($franchise) {
                $q->where('franchise_id', $franchise->id)
                  ->orWhere('parent_franchise_id', $franchise->id);
            })
            ->where('is_upcoming', '=', '0')
            ->orderByRaw('release_date DESC')->get();
    }

    public function getDaysSinceLastRelease() {
    	return Game::getDaysSinceLastRelease($this->getAllReleasedGames());
    }

    public function getAvgDaysBetweenReleases() {
    	return Game::getAvgDaysBetweenReleases($this->getAllReleasedGames());
    }

    public function getMaxDaysBetweenReleases() {
    	return Game::getMaxDaysBetweenReleases($this->getAllReleasedGames());
    }

    public function getStatus()
    {
    	$r = $this->getDaysSinceLastRelease() / $this->getAvgDaysBetweenReleases();
    	$days = $this->getDaysSinceLastRelease();

    	if($days > 10*365 || $r > 4) {
    		return "dead";
    	} else if($days > 8*365 || ($r > 1.5 && $days > 4*365)) {
    		return "bad";
    	} else if($days > 6*365 || $r > 0.75) {
    		return "neutral";
    	} else if($r <= 0.75) {
    		return "good";
    	} else {
    		return "dead";
    	}
    }
}
