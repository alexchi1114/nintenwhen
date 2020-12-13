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

    public function getUpcomingGames() {
    	$franchise = $this;
    	return Game::select('games.*')
            ->join('franchises', 'games.franchise_id', '=', 'franchises.id')
            ->where(function($q) use($franchise) {
                $q->where('franchise_id', $franchise->id)
                  ->orWhere('parent_franchise_id', $franchise->id);
            })
            ->where('is_upcoming', '=', '1')
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
    	$avg = $this->getAvgDaysBetweenReleases();
    	$days= $this->getDaysSinceLastRelease();
    	if($avg == 0 || $avg == null) {
    		return "dead";
    	}

    	$r = $days / $avg;

    	if($days > 10*365 || ($r > 4 && $days > 8*365) || ($days > 8*365 && $this->games->count() < 4)) {
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

    public static function getFranchisesToWatch()
    {   
    	$franchises = Franchise::all()->filter(function($franchise) {
            if($franchise->games->count() === 0) {
                return false;
            }
    		$status = $franchise->getStatus();
            $upcoming_games_without_release_date = $franchise->games->filter(function($game) {
                $is_port = $game->tags->filter(function($tag){
                    return $tag->code == 'port';
                })->count() > 0;
                return $game->is_upcoming && $game->release_date !== null && !$is_port;
            });
    		if($upcoming_games_without_release_date->count() > 0){
				return false;
    		} else {
    			if($status == "neutral" || $status== "bad") {
    				return true;
    			} else {
                    return false;
                }
    		}
    	});

        return $franchises;
    }
}
