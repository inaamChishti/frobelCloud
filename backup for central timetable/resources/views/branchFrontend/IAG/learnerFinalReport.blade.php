@extends('layouts.branchDashboardApp')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f8f9fa;
            color: #343a40;
            display: flex;
            flex-direction: column;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: 2.5rem;
            color: #67C0EA;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 400px;
            margin-bottom: 30px;
        }

        canvas {
            border-radius: 8px;
        }

        .layout-footer {
            margin-top: auto;
            padding: 15px 0;
            background-color: #67C0EA;
            color: #ffffff;
            text-align: center;
        }
    </style>

    <div class="container">
        <h1>IAG Module Statistics</h1>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const categoriesCount = @json($categoriesCount);

        const colors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
        ];

        const ctxBar = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: Object.keys(categoriesCount),
                datasets: [{
                    label: '',
                    data: Object.values(categoriesCount),
                    backgroundColor: colors.slice(0, Object.keys(categoriesCount).length),
                    borderColor: colors.slice(0, Object.keys(categoriesCount).length).map(color => color.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        grid: {
                            color: '#e9ecef'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e9ecef'
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        const ctxPie = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: Object.keys(categoriesCount),
                datasets: [{
                    label: 'Meeting Distribution',
                    data: Object.values(categoriesCount),
                    backgroundColor: colors.slice(0, Object.keys(categoriesCount).length),
                    borderColor: [
                        'rgba(255, 255, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribution of IAG Meetings'
                    }
                }
            }
        });
    </script>

@endsection
