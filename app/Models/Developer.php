<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Game;

class Developer extends Model
{
    protected $cachedReleasedGames = null;

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_developer')->withPivot('type');
    }

    public function getAllReleasedGames() {
        if ($this->cachedReleasedGames !== null) {
            return $this->cachedReleasedGames;
        }

        $this->cachedReleasedGames = $this->games()
            ->where('is_upcoming', '=', '0')
            ->orderByRaw('release_date DESC')
            ->get();

        return $this->cachedReleasedGames;
    }

    public function getUpcomingGames() {
        return $this->games()
            ->where('is_upcoming', '=', '1')
            ->orderByRaw('release_date ASC')
            ->get();
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
        if(!$this->is_active) {
            return "inactive";
        }

        $days = $this->getDaysSinceLastRelease();
        $r = $this->getR();

        if($r == null) {
            return "dead";
        }

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

    public function getR(){
        $avg = $this->getAvgDaysBetweenReleases();
        $days = $this->getDaysSinceLastRelease();
        if($avg == 0 || $avg == null) {
            return null;
        }

        return ($days / $avg) * $this->predict_multiplier;
    }

    public static function getDevelopersToWatch()
    {
        $developers = Developer::where('is_active', true)->with('games.tags')->get()->filter(function($developer) {
            if($developer->games->count() === 0) {
                return false;
            }
            $status = $developer->getStatus();
            $upcoming_games_without_port = $developer->games->filter(function($game) {
                $is_port = $game->tags->filter(function($tag){
                    return $tag->code == 'port';
                })->count() > 0;
                return $game->is_upcoming && !$is_port;
            });
            if($upcoming_games_without_port->count() > 0){
                return false;
            } else {
                if($status == "neutral" || $status == "bad") {
                    return true;
                } else {
                    return false;
                }
            }
        });

        return $developers->sort(function($a, $b) {
            return $a->getR() < $b->getR();
        });
    }
}
