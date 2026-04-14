# PHP_Laravel12_Analytics

##  Project Introduction

This project demonstrates how to **integrate Google Analytics into a Laravel 12 application** using the official **Spatie Laravel Analytics** package.

The goal of this project is to:

* Connect Laravel with **Google Analytics Data API**
* Fetch **visitors, page views, and top pages**
* Display analytics data inside a **Laravel dashboard**
* Follow **proper Laravel 12 folder structure**
* Provide **clear step-by-step implementation** for students and beginners

---

##  Step 1 — Create Laravel 12 Project

Open terminal and run:

```bash
composer create-project laravel/laravel PHP_Laravel12_Analytics
cd PHP_Laravel12_Analytics
```

Start development server:

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

##  Step 2 — Install Spatie Laravel Analytics Package

Install the official package:

```bash
composer require spatie/laravel-analytics
```

Publish configuration file:

```bash
php artisan vendor:publish --provider="Spatie\Analytics\AnalyticsServiceProvider"
```

This creates:

```
config/analytics.php
```

---

##  Step 3 — Google Analytics Setup (Detailed with Links)

To connect Laravel with Google Analytics, follow all steps carefully.

## 3.1 Create Google Cloud Project

Open Google Cloud Console:
[https://console.cloud.google.com/](https://console.cloud.google.com/)

Steps:

1. Click **Select Project → New Project**
2. Enter project name: `Laravel12-Analytics`
3. Click **Create**

---

## 3.2 Enable Google Analytics Data API

Open API Library:
[https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com](https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com)

Steps:

1. Search **Analytics Data API**
2. Click **Enable**

- This API is required for **Google Analytics 4 (GA4)** data access.

---

## 3.3 Create Service Account

Open Service Accounts page:
[https://console.cloud.google.com/iam-admin/serviceaccounts](https://console.cloud.google.com/iam-admin/serviceaccounts)

Steps:

1. Click **Create Service Account**
2. Name: `laravel12-analytics-sa`
3. Click **Create and Continue**
4. Skip role selection → Click **Done**

---

## 3.4 Download Service Account JSON Key

1. Open the created **Service Account**
2. Go to **Keys tab**
3. Click **Add Key → Create New Key**
4. Choose **JSON → Create**

A JSON file will download.

Rename downloaded file 

```
service-account-credentials.json
```


Place this file inside Laravel:

```
storage/app/analytics/service-account-credentials.json
```

(Create the folder if it does not exist.)

---


## 3.5 Create GA4 Property **and** Give Permission in Google Analytics

Laravel can read analytics data **only if a GA4 property exists and the service account has access**.
Follow both parts carefully.

---

### A. Create GA4 Property (if you don’t have one)


Open Google Analytics Admin:

[https://analytics.google.com/](https://analytics.google.com/)

Steps:

1. Click **Admin (bottom-left corner)**
2. In the **Property** column, click **+ Create Property**
3. Enter details:

   * **Property Name:** Laravel12 Analytics
   * **Time Zone:** Your country
   * **Currency:** Your currency
4. Click **Next → Create → Accept Terms**

---

### B. Create Web Data Stream

After property creation:

1. Choose **Web**
2. Enter:

   * **Website URL:** `http://127.0.0.1:8000`
   * **Stream Name:** Laravel Local
3. Click **Create Stream**

---

### C. Copy Required IDs

#### Measurement ID

Format:

```
G-XXXXXXXXXX
```

Add to `.env`:

```
GA_MEASUREMENT_ID=G-XXXXXXXXXX
```

---

#### Property ID

Find here:

```
Admin → Property Settings → Property ID
```

Add to `.env`:

```
ANALYTICS_PROPERTY_ID=123456789
```

---

### D. Give Permission to Service Account

1. Go to **Admin → Property Access Management**
2. Click **+ Add Users**
3. Enter **Service Account Email**, for example:

```
laravel12-analytics-sa@project-id.iam.gserviceaccount.com
```

4. Select role:

```
Viewer
```

5. Click **Add**

 **Viewer role is enough** for Laravel to read analytics data.

---


##  Step 4 — Configure analytics.php

Open:

```
config/analytics.php
```

```php
<?php

return [

    /*
     * The property id of which you want to display data.
     */
    'property_id' => env('ANALYTICS_PROPERTY_ID'),

    /*
     * Path to the client secret json file. Take a look at the README of this package
     * to learn how to get this file. You can also pass the credentials as an array
     * instead of a file path.
     */
    'service_account_credentials_json' => storage_path('app/analytics/service-account-credentials.json'),

    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,

    /*
     * Here you may configure the "store" that the underlying Google_Client will
     * use to store it's data.  You may also add extra parameters that will
     * be passed on setCacheConfig (see docs for google-api-php-client).
     *
     * Optional parameters: "lifetime", "prefix"
     */
    'cache' => [
        'store' => 'file',
    ],
];

```

---

## Step 5 — Configure GA4 Measurement ID in Laravel

### 5.1 Add GA4 config in config/services.php

Open:

config/services.php


Add inside the return array:

'ga4' => [
    'measurement_id' => env('GA_MEASUREMENT_ID'),
],

---

##  Step 6 — Update .env File

Open `.env` and add:

```env
ANALYTICS_PROPERTY_ID=YOUR_GOOGLE_ANALYTICS_PROPERTY_ID
GA_MEASUREMENT_ID=G-XXXXXXXXXX
```

You can find **Property ID** in:

**Google Analytics → Admin → Property Settings**

---

## Step 7 — Create Analytics Controller

Run command:

```bash
php artisan make:controller AnalyticsController
```

Open:

```
app/Http/Controllers/AnalyticsController.php
```

Add code:

```php
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
```

---

## Step 8 — Create Route

Open:

```
routes/web.php
```

Add:

```php
use App\Http\Controllers\AnalyticsController;

Route::get('/analytics', [AnalyticsController::class, 'index']);
```

---

## Step 9 — Create Dashboard View

Create folder:

```
resources/views/analytics
```

Create file:

```
dashboard.blade.php
```

Add code:

```blade
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
```

---

## Step 10 — Run the Project

Start server:

```bash
php artisan serve
```

Open dashboard:

```
http://127.0.0.1:8000/analytics
```

You will see:

* Visitors count
* Page views
* Most visited pages

---

## Output

### Analytics Dashboard

<img width="1901" height="1029" alt="Screenshot 2026-02-10 181452" src="https://github.com/user-attachments/assets/841c7f34-adc9-42c9-849c-d0508792b04d" />

---

## Project Folder Structure

```
PHP_Laravel12_Analytics
│
├── app
│   └── Http
│       └── Controllers
│           └── AnalyticsController.php
│
├── config
│   ├── analytics.php
│   └── services.php
│
├── resources
│   └── views
│       └── analytics
│           └── dashboard.blade.php
│
├── routes
│   └── web.php
│
├── storage
│   └── app
│       └── analytics
│           └── service-account-credentials.json
│
└── .env
```

---


Your **PHP_Laravel12_Analytics** is now complete.
<<<<<<< HEAD
=======

>>>>>>> development
