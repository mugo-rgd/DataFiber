@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Analytics - Dark Fibre CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Marketing Analytics</h1>
            <div class="space-x-4">
                <a href="{{ route('marketing-admin.dashboard') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Dashboard
                </a>
                <a href="{{ route('marketing-admin.customer-insights') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Customer Insights
                </a>
            </div>
        </div>

        <!-- Analytics Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Service Popularity Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Service Types</h3>
                <p class="text-3xl font-bold text-blue-600">
                    {{ count($analytics['service_popularity']) }}
                </p>
                <p class="text-sm text-gray-600 mt-2">Active service categories</p>
            </div>

            <!-- Geographic Distribution Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Locations Served</h3>
                <p class="text-3xl font-bold text-green-600">
                    {{ count($analytics['geographic_distribution']) }}
                </p>
                <p class="text-sm text-gray-600 mt-2">Different towns/cities</p>
            </div>

            <!-- Conversion Rates Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Lead Conversion</h3>
                <p class="text-3xl font-bold text-purple-600">
                    {{ $analytics['conversion_rates']['conversion_rate'] ?? '0' }}%
                </p>
                <p class="text-sm text-gray-600 mt-2">Overall conversion rate</p>
            </div>

            <!-- Total Customers Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Customers</h3>
                <p class="text-3xl font-bold text-orange-600">
                    {{ $analytics['conversion_rates']['total_customers'] ?? '0' }}
                </p>
                <p class="text-sm text-gray-600 mt-2">Active customers</p>
            </div>
        </div>

        <!-- Service Popularity Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6">Service Popularity</h2>
            @if(!empty($analytics['service_popularity']))
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-3 text-left">Service Category</th>
                                <th class="px-4 py-3 text-right">Number of Leases</th>
                                <th class="px-4 py-3 text-right">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalLeases = array_sum(array_column($analytics['service_popularity'], 'count'));
                            @endphp
                            @foreach($analytics['service_popularity'] as $service)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $service->service_category ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-right">{{ $service->count }}</td>
                                    <td class="px-4 py-3 text-right">
                                        {{ $totalLeases > 0 ? number_format(($service->count / $totalLeases) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                            @if($totalLeases > 0)
                                <tr class="bg-gray-100 font-semibold">
                                    <td class="px-4 py-3">Total</td>
                                    <td class="px-4 py-3 text-right">{{ $totalLeases }}</td>
                                    <td class="px-4 py-3 text-right">100%</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 text-lg">No service popularity data available.</p>
                </div>
            @endif
        </div>

        <!-- Geographic Distribution Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6">Geographic Distribution</h2>
            @if(!empty($analytics['geographic_distribution']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-3 text-left">Location</th>
                                    <th class="px-4 py-3 text-right">Number of Customers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['geographic_distribution'] as $location)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $location->town ?? 'Unknown' }}</td>
                                        <td class="px-4 py-3 text-right">{{ $location->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-center">
                        <canvas id="geographicChart" width="400" height="300"></canvas>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 text-lg">No geographic distribution data available.</p>
                </div>
            @endif
        </div>

        <!-- Conversion Rates Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6">Conversion Metrics</h2>
            @if(!empty($analytics['conversion_rates']))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-700">Total Leads</h3>
                        <p class="text-2xl font-bold text-blue-800">{{ $analytics['conversion_rates']['total_leads'] ?? 0 }}</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-700">Converted Customers</h3>
                        <p class="text-2xl font-bold text-green-800">{{ $analytics['conversion_rates']['total_customers'] ?? 0 }}</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-700">Conversion Rate</h3>
                        <p class="text-2xl font-bold text-purple-800">{{ $analytics['conversion_rates']['conversion_rate'] ?? 0 }}%</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 text-lg">No conversion rate data available.</p>
                </div>
            @endif
        </div>

        <!-- Service Popularity Chart Section -->
        @if(!empty($analytics['service_popularity']))
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h2 class="text-2xl font-semibold mb-6">Service Popularity Chart</h2>
            <div class="h-96">
                <canvas id="servicePopularityChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    <!-- JavaScript for Charts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Service Popularity Chart
            @if(!empty($analytics['service_popularity']))
            const serviceCtx = document.getElementById('servicePopularityChart').getContext('2d');
            const serviceChart = new Chart(serviceCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($analytics['service_popularity'], 'service_category')) !!},
                    datasets: [{
                        label: 'Number of Leases',
                        data: {!! json_encode(array_column($analytics['service_popularity'], 'count')) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
            @endif

            // Geographic Distribution Chart
            @if(!empty($analytics['geographic_distribution']))
            const geoCtx = document.getElementById('geographicChart').getContext('2d');
            const geoChart = new Chart(geoCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode(array_column($analytics['geographic_distribution'], 'town')) !!},
                    datasets: [{
                        data: {!! json_encode(array_column($analytics['geographic_distribution'], 'count')) !!},
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
            @endif
        });
    </script>
</body>
</html>
@endsection
