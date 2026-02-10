<?php

namespace App\Http\Controllers;

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    public function index()
    {
        $period = Period::days(7);

        // Fetch data
        $rawVisitors = Analytics::fetchTotalVisitorsAndPageViews($period);
        $rawTopPages = Analytics::fetchMostVisitedPages($period);

        // Ensure $visitors is always an array with 'date', 'visitors', 'pageViews'
        $visitors = $rawVisitors->map(function ($item) {
            return [
                'date' => $item['date'] ?? 'N/A',
                'visitors' => $item['visitors'] ?? 0,
                'pageViews' => $item['pageViews'] ?? 0,
            ];
        })->toArray();

        // Ensure $topPages is always an array with 'url' and 'pageViews'
        $topPages = $rawTopPages->map(function ($item) {
            return [
                'url' => $item['url'] ?? 'N/A',
                'pageViews' => $item['pageViews'] ?? 0,
            ];
        })->toArray();

        return view('analytics.dashboard', compact('visitors', 'topPages'));
    }
}
