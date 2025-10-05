<?php

namespace App\Http\Controllers;

use App\Models\Franchise;
use App\Models\Game;
use App\Models\Tag;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $tags = Tag::all()->where('is_active')->sortBy('display_order');

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
            $q = $q->whereDoesntHave('tags', function (Builder $query) use($tags) {
                $query->whereIn('tag_id', $tags);
            });
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

    public function getFranchiseAnalysisById($id) {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $cacheKey = 'franchise_ai_analysis_' . $id;
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $franchise = Franchise::findOrFail($id);
        $games = null;

        //If child, get parent instead
        if($franchise->parent_franchise_id !== null) {
            $franchise = Franchise::findOrFail($franchise->parent_franchise_id);

            //Get games that belong to child
            $games = Game::where('franchise_id', $id)
                ->orderByRaw('release_date DESC')->get();
        } else {
            //We have the parent so get all games
            $games = Game::select('games.*')
                ->join('franchises', 'games.franchise_id', '=', 'franchises.id')
                ->where(function($q) use($franchise) {
                    $q->where('franchise_id', $franchise->id)
                        ->orWhere('parent_franchise_id', $franchise->id);
                })
                ->get()
                ->map(function ($game) {
                    return [
                        'game' => $game->name,
                        'releaseDate' => $game->release_date,
                        'isUpcoming' => $game->is_upcoming,
                        'releaseDateTentative' => $game->release_date_tentative,
                    ];
                });
        }

        $client = \OpenAI::client(getenv('OPENAI_API_KEY'));
        $response = $client->responses()->create([
            'model' => 'gpt-4.1',
            'instructions' => 'Return the output as a valid HTML snippet. Do not surround with an html tag. It is a snippet of html. Do not provide anything else other than the HTML. Do not refer to me or the prompt. This is to be used as a blurb on a website.',
            'input' => 'Here are games in the ' .$franchise->name . ' Nintendo series. Give a few sentences of analysis for the possibility of a new game coming soon. No markdown or anything like that. Here are the games: ' . $games->toJson(),
            'tool_choice' => 'required',
            'tools' => [
                ['type' => 'web_search']
            ]
        ]);
        $text = '';
        foreach ($response->output as $item) {
            if (isset($item->content[0]->text)) {
                $text .= $item->content[0]->text;
            }
        }
        Cache::put($cacheKey, $text, new DateInterval('P1D'));
        return $text;
    }

    public function getCachedFranchiseAnalysis()
    {
        $cached = Cache::get('franchise_ai_analysis');

        if ($cached) {
            return response()->json(['ai_analysis' => $cached]);
        }

        // if no cache, fallback to empty or message
        return response()->json(['ai_analysis' => NULL]);
    }

    public function getFranchiseAnalysis() {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $cacheKey = 'franchise_ai_analysis';
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $franchises_to_watch = Franchise::getFranchisesToWatch()->take(5);
        $games = Game::with('franchise:id,name')
            ->whereIn('franchise_id', $franchises_to_watch->pluck('id'))
            ->get()
            ->map(function ($game) {
                return [
                    'game' => $game->name,
                    'franchise' => $game->franchise->name,
                    'franchiseId' => $game->franchise->id,
                    'releaseDate' => $game->release_date,
                    'isUpcoming' => $game->is_upcoming,
                    'releaseDateTentative' => $game->release_date_tentative,
                ];
            });

        $client = \OpenAI::client(getenv('OPENAI_API_KEY'));
        $response = $client->responses()->create([
            'model' => 'gpt-4.1',
            'instructions' => 'Return the output as a valid HTML snippet. Do not surround with an html tag. It is a snippet of html. Do not provide anything else other than the HTML. Do not refer to me or the prompt. This is to be used as a blurb on a website.',
            'input' => 'Here are games in 5 franchises likely to have new announcements soon. Give a couple sentences of analysis for each franchise, explaining why a game announcement in the franchise might be coming soon. No markdown or anything like that. Put a line break between the analysis for each franchise to separate them out for readability. You can return the text as HTML snippet with a li for each franchise analysis, with strong tags every time the game franchise is mentioned. Here are the games: ' . $games->toJson(),
            'tool_choice' => 'required',
            'tools' => [
                ['type' => 'web_search']
            ]
        ]);
        $text = '';
        foreach ($response->output as $item) {
            if (isset($item->content[0]->text)) {
                $text .= $item->content[0]->text;
            }
        }
        Cache::put($cacheKey, $text, new DateInterval('P1D'));
        return $text;
    }

    public function streamFranchiseAnalysis()
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $cacheKey = 'franchise_ai_analysis';
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json(['ai_analysis' => $cached]);
        }

        $franchises_to_watch = Franchise::getFranchisesToWatch()->take(5);
        $games = Game::with('franchise:id,name')
            ->whereIn('franchise_id', $franchises_to_watch->pluck('id'))
            ->get()
            ->map(function ($game) {
                return [
                    'game' => $game->name,
                    'franchise' => $game->franchise->name,
                    'franchiseId' => $game->franchise->id,
                    'releaseDate' => $game->release_date,
                    'isUpcoming' => $game->is_upcoming,
                    'releaseDateTentative' => $game->release_date_tentative,
                ];
            });

        $client = \OpenAI::client(getenv('OPENAI_API_KEY'));
        return response()->stream(function () use($client, $games) {
            $stream = $client->chat()->createStreamed([
                'model' => 'gpt-4.1',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Here are games in 5 franchises likely to have new announcements soon. Give a couple sentences of analysis for each game, explaining why a game announcement might be coming soon. No markdown or anything like that. Put a line break between the analysis for each franchise to separate them out for readability. You can return the text as HTML snippet with a li for each franchise analysis, with strong tags every time the game franchise is mentioned. Here are the games: ' . $games->toJson(),
                    ]
                ],
                'functions' => [
                    [
                        'name' => 'search_web',
                        'description' => 'Search the web and return relevant results',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => ['type' => 'string'],
                            ],
                            'required' => ['query']
                        ]
                    ]
                ],
                'function_call' => 'auto',
            ]);

            foreach ($stream as $response) {
                $text = $response->choices[0]->delta->content;
                if (connection_aborted()) {
                    break;
                }

                echo "event: update\n";
                echo 'data: ' . $text;
                echo "\n\n";
                ob_flush();
                flush();
            }

            echo "event: update\n";
            echo 'data: <END_STREAMING_SSE>';
            echo "\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }
}
