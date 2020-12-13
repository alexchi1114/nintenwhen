<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Franchise;
use App\Models\Game;

class HomeController extends Controller
{
    public function index()
    {
        $upcoming_games = Game::Where('is_upcoming', 1)->get();
        $franchises_to_watch = Franchise::getFranchisesToWatch();

        return view('home.index', [
        	'upcoming_games' => $upcoming_games,
        	'franchises_to_watch' => $franchises_to_watch
        ]);
    }
}
