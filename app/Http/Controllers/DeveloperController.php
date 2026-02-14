<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class DeveloperController extends Controller
{
    public function index()
    {
        $developers = Developer::orderBy('name')->get();
        return view('developer.index', ['developers' => $developers]);
    }

    public function show($id)
    {
        $developer = Developer::findOrFail($id);

        $games = $developer->getAllReleasedGames();
        $upcoming_games = $developer->getUpcomingGames();
        $tags = Tag::all()->where('is_active')->sortBy('display_order');

        $types = $developer->games()
            ->select('game_developer.type')
            ->distinct()
            ->pluck('type');

        return view('developer.developer', [
            'developer' => $developer,
            'games' => $games,
            'days_since_last_release' => Game::getDaysSinceLastRelease($games),
            'avg_days_between_releases' => Game::getAvgDaysBetweenReleases($games),
            'max_days_between_releases' => Game::getMaxDaysBetweenReleases($games),
            'upcoming_games' => $upcoming_games,
            'tags' => $tags,
            'types' => $types,
        ]);
    }

    public function search(Request $request) {
        $developer = Developer::findOrFail($request->input('developer_id'));

        $tags = $request->input('tags');
        $excluded_types = $request->input('excluded_types', []);

        $q = $developer->games()
            ->where('is_upcoming', '=', '0');

        if(sizeof($tags) > 0)
        {
            $q = $q->whereDoesntHave('tags', function (Builder $query) use($tags) {
                $query->whereIn('tag_id', $tags);
            });
        }

        if(sizeof($excluded_types) > 0)
        {
            $q = $q->whereNotIn('game_developer.type', $excluded_types);
        }

        $games = $q->orderByRaw('release_date DESC')->get();

        return view('developer.games', [
            'developer' => $developer,
            'games' => $games,
            'days_since_last_release' => Game::getDaysSinceLastRelease($games),
            'avg_days_between_releases' => Game::getAvgDaysBetweenReleases($games),
            'max_days_between_releases' => Game::getMaxDaysBetweenReleases($games),
            'tags' => $tags
        ]);
    }
}
