<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Franchise;
use App\Models\Game;

class HomeController extends Controller
{
    public function index()
    {
        $upcoming_games = Game::Where('is_upcoming', 1)->orderBy('release_date', 'DESC')->get();
        $franchises_to_watch = Franchise::getFranchisesToWatch()->take(5);

        return view('home.index', [
        	'upcoming_games' => $upcoming_games,
        	'franchises_to_watch' => $franchises_to_watch
        ]);
    }
}
