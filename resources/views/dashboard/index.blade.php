@extends('layouts.main')

@section('main')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0">Welcome to Your Insightful Dashboard</h4>
    </div>
</div>

<!-- Cards Section -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card l-bg-blue-dark">
            <div class="card-body">
                <h5 class="card-title text-white">Total Users</h5>
                <h2 class="text-white">{{ $totalUsers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card l-bg-cyan">
            <div class="card-body">
                <h5 class="card-title text-white">Total Bill Payments</h5>
                <h2 class="text-white">{{ $totalBillpayments }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card l-bg-green">
            <div class="card-body">
                <h5 class="card-title text-white">Total Transactions</h5>
                <h2 class="text-white">{{ $totalTransaction }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card l-bg-orange">
            <div class="card-body">
                <h5 class="card-title text-white">Total Revenue</h5>
                <h2 class="text-white">NGN {{ number_format($totalRevenue, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bill Payments (Weekly, Monthly, Yearly)</h5>
                <canvas id="billPaymentsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daily Revenue (Last 30 Days)</h5>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 30px">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daily Transactions (Last 30 Days)</h5>
                <canvas id="transactionsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Latest Entries -->
<div class="row" style="margin-top: 60px">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Latest Users</h5>
                <ul class="list-group">
                    @foreach ($latestUsers as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $user->account->firstName ?? 'N/A' }}
                            <span>{{ $user->email }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Latest Bill Payments</h5>
                <ul class="list-group">
                    @foreach ($latestBillPayments as $payment)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $payment->user->email ?? 'N/A' }}
                            <span>NGN {{ number_format($payment->amount) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bill Payments Chart
        new Chart(document.getElementById('billPaymentsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Weekly', 'Monthly', 'Yearly'],
                datasets: [{
                    label: 'Bill Payments',
                    data: [{{ $weeklyBillPayments }}, {{ $monthlyBillPayments }}, {{ $yearlyBillPayments }}],
                    backgroundColor: ['#4CAF50', '#FFC107', '#2196F3'],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                },
            }
        });

        // Daily Revenue Chart
        new Chart(document.getElementById('revenueChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartCategories),
                datasets: [{
                    label: 'Revenue (Rs)',
                    data: @json($dailyRevenue),
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 20,
                            padding: 10,
                        },
                    },
                },
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Revenue (Rs)' }, beginAtZero: true },
                },
            }
        });

        // Daily Transactions Chart
        new Chart(document.getElementById('transactionsChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartCategories),
                datasets: [{
                    label: 'Transactions',
                    data: @json($dailyTransactions),
                    borderColor: '#FFC107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 20,
                            padding: 10,
                        },
                    },
                },
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Count' }, beginAtZero: true },
                },
            }
        });
    });
</script>
