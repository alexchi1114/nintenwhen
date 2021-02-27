<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Franchise;
use App\Models\Game;

class HomeController extends Controller
{
    public function index()
    {
        $upcoming_games = Game::Where('is_upcoming', 1)->orderBy(DB::raw('ISNULL(release_date), release_date, ISNULL(release_date_tentative), display_order'), 'ASC')->get();
        $franchises_to_watch = Franchise::getFranchisesToWatch()->take(5);

        return view('home.index', [
        	'upcoming_games' => $upcoming_games,
        	'franchises_to_watch' => $franchises_to_watch
        ]);
    }
}
