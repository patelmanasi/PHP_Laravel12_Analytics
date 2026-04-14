<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-100 font-[Inter]">

    <div class="max-w-7xl mx-auto p-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">📊 Analytics Dashboard</h1>
                <p class="text-slate-500">Laravel Analytics Report</p>
            </div>

            <a href="/analytics/export" class="bg-green-600 text-white px-4 py-2 rounded-lg">
                Export CSV
            </a>
        </div>

        <!-- FILTER -->
        <form method="GET" class="bg-white p-4 rounded-xl shadow mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">

            <select name="days" class="border rounded-lg px-3 py-2">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
            </select>

            <input type="date" name="from" class="border rounded-lg px-3 py-2">
            <input type="date" name="to" class="border rounded-lg px-3 py-2">

            <input type="text" name="search" placeholder="Search page URL" class="border rounded-lg px-3 py-2">

            <button class="bg-indigo-600 text-white rounded-lg px-4 py-2">
                Apply
            </button>
        </form>

        <!-- TAB BUTTONS -->
        <div class="flex gap-3 mb-6">
            <button onclick="showTab('overview')" class="tab-btn bg-indigo-600 text-white px-4 py-2 rounded-lg">
                Overview
            </button>

            <button onclick="showTab('visitors')" class="tab-btn bg-gray-200 px-4 py-2 rounded-lg">
                Visitors
            </button>

            <button onclick="showTab('pages')" class="tab-btn bg-gray-200 px-4 py-2 rounded-lg">
                Top Pages
            </button>

            <button onclick="showTab('chart')" class="tab-btn bg-gray-200 px-4 py-2 rounded-lg">
                Chart
            </button>
        </div>

        @php
            $totalVisitors = collect($visitors)->sum('visitors');
            $totalPageViews = collect($visitors)->sum('pageViews');
            $avgVisitors = count($visitors) ? round($totalVisitors / count($visitors)) : 0;
        @endphp

        <!-- OVERVIEW -->
        <div id="overview" class="tab-content">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <div class="bg-white p-5 rounded-xl shadow">
                    <p class="text-slate-500">Total Visitors</p>
                    <h2 class="text-3xl font-bold text-indigo-600">{{ $totalVisitors }}</h2>
                </div>

                <div class="bg-white p-5 rounded-xl shadow">
                    <p class="text-slate-500">Total Page Views</p>
                    <h2 class="text-3xl font-bold text-green-600">{{ $totalPageViews }}</h2>
                </div>

                <div class="bg-white p-5 rounded-xl shadow">
                    <p class="text-slate-500">Avg Visitors / Day</p>
                    <h2 class="text-3xl font-bold text-purple-600">{{ $avgVisitors }}</h2>
                </div>

            </div>

        </div>

        <!-- VISITORS -->
        <div id="visitors" class="tab-content hidden">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-lg font-semibold mb-4">Daily Visitors</h2>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2">Date</th>
                            <th>Visitors</th>
                            <th>Views</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($visitors as $v)
                            <tr class="border-b">
                                <td class="py-2">{{ $v['date'] }}</td>
                                <td>{{ $v['visitors'] }}</td>
                                <td>{{ $v['pageViews'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOP PAGES -->
        <div id="pages" class="tab-content hidden">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-lg font-semibold mb-4">Top Pages</h2>

                <ul class="space-y-3">
                    @foreach($topPages as $page)
                        <li class="flex justify-between bg-slate-50 p-3 rounded-lg">
                            <span class="truncate">{{ $page['url'] }}</span>
                            <span class="text-indigo-600 font-semibold">
                                {{ $page['pageViews'] }} views
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- CHART -->
        <div id="chartTab" class="tab-content hidden bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">Visitors Trend</h2>
            <canvas id="chart"></canvas>
        </div>

    </div>

    <!-- JS -->
    <script>
        function showTab(tab) {

            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            const target = tab === 'chart' ? 'chartTab' : tab;
            document.getElementById(target).classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-gray-200');
            });

            event.target.classList.add('bg-indigo-600', 'text-white');
            event.target.classList.remove('bg-gray-200');
        }

        // Chart
        const data = @json($visitors);

        new Chart(document.getElementById('chart'), {
            type: 'line',
            data: {
                labels: data.map(i => i.date),
                datasets: [{
                    label: 'Visitors',
                    data: data.map(i => i.visitors),
                    borderWidth: 2,
                    fill: true
                }]
            }
        });
    </script>

</body>

</html>