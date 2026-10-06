@extends('layouts.superAdminApp')

@section('content')
<div class="main-content max-w-7xl mx-auto p-6 font-sans">


    @if(auth()->user()->role === 'super_admin')
        <div class="mb-6">
            <form action="{{ route('switch-branch') }}" method="POST" class="flex items-center gap-4">
                @csrf
                <label for="branch_id" class="text-gray-700 font-semibold">Switch to Branch Dashboard:</label>
                <select name="branch_id" id="branch_id" onchange="this.form.submit()" class="form-select rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Branch</option>
                    @foreach(\App\Models\User::where('role', 'branch_admin')->distinct()->get(['branch_id', 'branch_name']) as $branch)
                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

    <h1 class="text-3xl font-bold text-center mb-8 text-gray-800" id="title">
        Today's Summary ({{ \Carbon\Carbon::parse($today)->format('d/m/Y') }})
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="branch-stats-container">
        @foreach ($branchStats as $stats)
            <div class="branch-card bg-white rounded-lg shadow-lg p-6 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">{{ $stats['branch_name'] }}</h3>
                <div class="space-y-3">
                    <p class="text-gray-600">
                        <strong>Total Students Scheduled:</strong>
                        <span class="count font-medium text-blue-600" data-target="{{ $stats['totalStudents'] }}">0</span>
                    </p>
                    <p class="text-gray-600">
                        <strong>Students Attended:</strong>
                        <span class="count font-medium text-green-600" data-target="{{ $stats['attendedCount'] }}">0</span>
                    </p>
                    <p class="text-gray-600">
                        <strong>Students Absent:</strong>
                        <span class="count font-medium text-red-600" data-target="{{ $stats['totalStudents'] - $stats['attendedCount'] }}">0</span>
                    </p>
                </div>
                <div class="mt-4">
                    <canvas id="chart-{{ $stats['branch_name'] }}" class="w-full h-40"></canvas>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .branch-card {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    #title {
        animation: fadeInUp 0.5s ease-out;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        // Animate branch cards sequentially
        $('.branch-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.2) + 's');
        });

        // Count-up animation for numbers
        $('.count').each(function() {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).data('target')
            }, {
                duration: 1500,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });

        // Initialize charts for each branch
        @foreach ($branchStats as $stats)
            new Chart(document.getElementById('chart-{{ $stats['branch_name'] }}').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Scheduled', 'Attended', 'Absent'],
                    datasets: [{
                        label: '{{ $stats['branch_name'] }} Stats',
                        data: [
                            {{ $stats['totalStudents'] }},
                            {{ $stats['attendedCount'] }},
                            {{ $stats['totalStudents'] - $stats['attendedCount'] }}
                        ],
                        backgroundColor: ['#3b82f6', '#10b981', '#ef4444'],
                        borderColor: ['#2563eb', '#059669', '#dc2626'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1f2937' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#4b5563' }
                        },
                        x: {
                            ticks: { color: '#4b5563' }
                        }
                    }
                }
            });
        @endforeach
    });
</script>
@endsection
