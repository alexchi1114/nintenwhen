<?php

namespace App\Http\Controllers;

use App\Models\Franchise;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class FranchiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $franchises = Franchise::whereNull('parent_franchise_id')->orderBy('name')->get();
        return view('franchise.index', ['franchises' => $franchises]);
    }

    public function show($id)
    {
        $franchise = Franchise::findOrFail($id);
        $games = null;

        //If child, get parent instead
        if($franchise->parent_franchise_id !== null) {
            $franchise = Franchise::findOrFail($franchise->parent_franchise_id);

            //Get games that belong to child
            $games = Game::where('franchise_id', $id)
                ->where('is_upcoming', '=', '0')
                ->orderByRaw('release_date DESC')->get();
        } else {
            //We have the parent so get all games
            $games = Game::select('games.*')
                ->join('franchises', 'games.franchise_id', '=', 'franchises.id')
                ->where(function($q) use($franchise) {
                    $q->where('franchise_id', $franchise->id)
                      ->orWhere('parent_franchise_id', $franchise->id);
                })
                ->where('is_upcoming', '=', '0')
                ->orderByRaw('release_date DESC')->get();
        }

        $children = Franchise::where('parent_franchise_id', '=', $franchise->id)->orderBy('name')->get();

        $upcoming_games = Game::select('games.*')
                ->join('franchises', 'games.franchise_id', '=', 'franchises.id')
                ->where(function($q) use($franchise) {
                    $q->where('franchise_id', $franchise->id)
                      ->orWhere('parent_franchise_id', $franchise->id);
                })
                ->where('is_upcoming', '=', '1')
                ->orderByRaw('release_date DESC')->get();

        $tags = Tag::all()->sortBy('display_order');

        return view('franchise.franchise', [
            'franchise' => $franchise, 
            'games' => $games,
            'days_since_last_release' => Game::getDaysSinceLastRelease($games),
            'avg_days_between_releases' => Game::getAvgDaysBetweenReleases($games),
            'max_days_between_releases' => Game::getMaxDaysBetweenReleases($games),
            'upcoming_games' => $upcoming_games,
            'tags' => $tags,
            'children' => $children,
            'selected_franchise' => $id
        ]);
    }

    public function search(Request $request) {
        $franchise = Franchise::findOrFail($request->input('franchise_id'));

        $tags =  $request->input('tags');
        $q = Game::select('games.*')
                ->join('franchises', 'games.franchise_id', '=', 'franchises.id')
                ->where(function($q) use($request) {
                    $q->where('franchise_id', $request->input('franchise_id'))
                      ->orWhere('parent_franchise_id', $request->input('franchise_id'));
                })
                ->where('is_upcoming', '=', '0');

        if(sizeof($tags) > 0)
        {
            $q = $q->whereHas('tags', function (Builder $query) use($tags) {
                $query->whereIn('tag_id', $tags);
            }, '>=', count($tags));
        }

        if($request->input('child_franchise_id') !== null) {
            $q = $q->where('franchise_id', $request->input('child_franchise_id'));
        }

        $games = $q->orderByRaw('release_date DESC')->get();
        
        return view('franchise.games', [
            'franchise' => $franchise, 
            'games' => $games,
            'days_since_last_release' => Game::getDaysSinceLastRelease($games),
            'avg_days_between_releases' => Game::getAvgDaysBetweenReleases($games),
            'max_days_between_releases' => Game::getMaxDaysBetweenReleases($games),
            'tags' => $tags
        ]);
    }

}
