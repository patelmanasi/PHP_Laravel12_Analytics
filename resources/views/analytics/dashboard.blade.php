<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CDN (for quick modern UI) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

   <!-- Google Analytics GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga4.measurement_id') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{{ config('services.ga4.measurement_id') }}');
</script>

</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-200 min-h-screen font-[Inter]">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <!-- Header -->
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">📊 Laravel Analytics Dashboard</h1>
                <p class="text-slate-500 mt-1">Last 7 days website performance overview</p>
            </div>

            <div class="bg-white shadow-md rounded-2xl px-4 py-2 text-sm text-slate-600">
                Laravel 12 • Google Analytics GA4
            </div>
        </div>

        <!-- Stats Cards -->
        @php
        $totalVisitors = collect($visitors)->sum('visitors');
        $totalPageViews = collect($visitors)->sum('pageViews');
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <p class="text-slate-500 text-sm">Total Visitors</p>
                <h2 class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalVisitors }}</h2>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6">
                <p class="text-slate-500 text-sm">Total Page Views</p>
                <h2 class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalPageViews }}</h2>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-10">
            <h2 class="text-lg font-semibold text-slate-700 mb-4">Visitors Trend (Last 7 Days)</h2>
            <canvas id="visitorsChart" height="100"></canvas>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Visitors Table -->
            <div class="bg-white rounded-2xl shadow-md p-6 overflow-x-auto">
                <h2 class="text-lg font-semibold text-slate-700 mb-4">Daily Visitors</h2>

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="py-2">Date</th>
                            <th class="py-2">Visitors</th>
                            <th class="py-2">Page Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visitors as $row)
                        <tr class="border-b last:border-none">
                            <td class="py-2 text-slate-600">{{ $row['date'] }}</td>
                            <td class="py-2 font-medium">{{ $row['visitors'] }}</td>
                            <td class="py-2 text-slate-500">{{ $row['pageViews'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Top Pages -->
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-lg font-semibold text-slate-700 mb-4">Top Visited Pages</h2>

                <ul class="space-y-3">
                    @foreach($topPages as $page)
                    <li class="flex justify-between items-center bg-slate-50 rounded-xl px-4 py-3">
                        <span class="text-slate-700 truncate">{{ $page['url'] }}</span>
                        <span class="text-indigo-600 font-semibold">{{ $page['pageViews'] }} views</span>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-slate-400 mt-12">
            © {{ date('Y') }} Laravel Analytics Dashboard • Built with ❤️ using Laravel 12 & GA4
        </div>

    </div>


    <!-- Chart Script -->
    <script>
        const visitorsData = @json($visitors);

        const labels = visitorsData.map(v => v.date);
        const visitors = visitorsData.map(v => v.visitors);

        const ctx = document.getElementById('visitorsChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Visitors',
                    data: visitors,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>