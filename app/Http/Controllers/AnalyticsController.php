<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Date filter
        $days = $request->days ?? 7;

        // Generate demo date range data (SAFE - NO Google API)
        $visitors = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $visitors->push([
                'date' => now()->subDays($i)->format('Y-m-d'),
                'visitors' => rand(10, 100),
                'pageViews' => rand(50, 200),
            ]);
        }

        // Top pages demo data
        $topPages = collect([
            ['url' => '/home', 'pageViews' => 120],
            ['url' => '/about', 'pageViews' => 80],
            ['url' => '/contact', 'pageViews' => 60],
            ['url' => '/products', 'pageViews' => 150],
        ]);

        // Search filter
        if ($request->search) {
            $topPages = $topPages->filter(function ($item) use ($request) {
                return str_contains(
                    strtolower($item['url']),
                    strtolower($request->search)
                );
            })->values();
        }

        return view('analytics.dashboard', [
            'visitors' => $visitors,
            'topPages' => $topPages,
            'request' => $request
        ]);
    }

    // CSV Export
    public function export()
    {
        $visitors = collect();

        for ($i = 6; $i >= 0; $i--) {
            $visitors->push([
                'date' => now()->subDays($i)->format('Y-m-d'),
                'visitors' => rand(10, 100),
                'pageViews' => rand(50, 200),
            ]);
        }

        $response = new StreamedResponse(function () use ($visitors) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Date', 'Visitors', 'Page Views']);

            foreach ($visitors as $row) {
                fputcsv($handle, [
                    $row['date'],
                    $row['visitors'],
                    $row['pageViews']
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="analytics.csv"');

        return $response;
    }
}