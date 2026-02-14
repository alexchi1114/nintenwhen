<?php

namespace App\Http\Controllers;

use App\Models\Direct;
use App\Models\DirectTag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class DirectController extends Controller
{
    public function index()
    {
        $directs = Direct::orderBy('start_time', 'DESC')->get();
        $tags = DirectTag::where('is_active', true)->orderBy('display_order')->get();

        return view('direct.index', [
            'directs' => $directs,
            'days_since_last_direct' => Direct::getDaysSinceLastDirect($directs),
            'avg_days_between_directs' => Direct::getAvgDaysBetweenDirects($directs),
            'max_days_between_directs' => Direct::getMaxDaysBetweenDirects($directs),
            'tags' => $tags,
        ]);
    }

    public function search(Request $request)
    {
        $tags = $request->input('tags', []);

        $q = Direct::orderBy('start_time', 'DESC');

        if (sizeof($tags) > 0) {
            $q = $q->whereDoesntHave('tags', function (Builder $query) use ($tags) {
                $query->whereIn('direct_tag_id', $tags);
            });
        }

        $directs = $q->get();

        return view('direct.directs', [
            'directs' => $directs,
            'days_since_last_direct' => Direct::getDaysSinceLastDirect($directs),
            'avg_days_between_directs' => Direct::getAvgDaysBetweenDirects($directs),
            'max_days_between_directs' => Direct::getMaxDaysBetweenDirects($directs),
        ]);
    }

    public static function getPredictions()
    {
        $predictions = [];
        $tags = DirectTag::where('is_active', true)->orderBy('display_order')
            ->with(['directs' => function($query) {
                $query->orderBy('start_time', 'DESC');
            }])
            ->get();

        foreach ($tags as $tag) {
            $directs = $tag->directs;

            if ($directs->count() === 0) {
                continue;
            }

            $daysSince = Direct::getDaysSinceLastDirect($directs);
            $avgDays = Direct::getAvgDaysBetweenDirects($directs);
            $maxDays = Direct::getMaxDaysBetweenDirects($directs);

            $predictions[] = [
                'tag' => $tag,
                'directs' => $directs,
                'days_since_last' => $daysSince,
                'avg_days_between' => $avgDays,
                'max_days_between' => $maxDays,
            ];
        }

        return $predictions;
    }
}
