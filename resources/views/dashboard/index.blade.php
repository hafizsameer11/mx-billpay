@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
        {{-- <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                <span class="input-group-text input-group-addon bg-transparent border-primary" data-toggle><i
                        data-feather="calendar" class="text-primary"></i></span>
                <input type="text" class="form-control bg-transparent border-primary" placeholder="Select date" data-input>
            </div>
            <button type="button" class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="printer"></i>
                Print
            </button>
            <button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                Download Report
            </button>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1 my-4">
                <!-- Cards Section -->
                <div class="col-xl-4 col-lg-4 data">
                    <div class="card l-bg-blue-dark">
                        <div class="card-statistic-3 p-4">
                            <div class="card-icon card-icon-large"><i class="fas fa-users"></i></div>
                            <div class="mb-4">
                                <h5 class="card-title text-white mb-0">All Users</h5>
                            </div>
                            <div class="row align-items-center mb-2 d-flex">
                                <div class="col-8">
                                    <h2 class="d-flex align-items-center mb-0">
                                        {{ $totalUsers }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 data">
                    <div class="card l-bg-cyan">
                        <div class="card-statistic-3 p-4">
                            <div class="card-icon card-icon-large"><i class="fas fa-users"></i></div>
                            <div class="mb-4">
                                <h5 class="card-title mb-0 text-white">Total Bill Payments</h5>
                            </div>
                            <div class="row align-items-center mb-2 d-flex">
                                <div class="col-8">
                                    <h2 class="d-flex align-items-center mb-0">
                                        {{ $totalBillpayments }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 data">
                    <div class="card l-bg-green">
                        <div class="card-statistic-3 p-4">
                            <div class="card-icon card-icon-large"><i class="fas fa-users"></i></div>
                            <div class="mb-4">
                                <h5 class="card-title mb-0 text-white">Total Transaction</h5>
                            </div>
                            <div class="row align-items-center mb-2 d-flex">
                                <div class="col-8">
                                    <h2 class="d-flex align-items-center mb-0">
                                        {{ $totalTransaction }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Graph Section -->
            
        </div>
    </div> <!-- row -->
    <div class="row my-5">
        <div class="col-12">
            <div class="card" style="height: 500px; width: 100%;">
                <div class="card-body">
                    <h5 class="card-title">Bill Payments - Weekly, Monthly & Yearly</h5>
                    <canvas id="billPaymentsChart" style="height: 100%; width: 100%;"></canvas>
                    <p class="mt-4">
                        <strong>Total Revenue from Bill Payments:</strong> Rs. {{ number_format($totalRevenue, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    

    {{-- <div class="row">
        <div class="col-12 col-xl-12 grid-margin stretch-card">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-4 mb-md-3">
                        <h6 class="card-title mb-0 fs-md-5">Revenue</h6>
                        <div class="dropdown">
                            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton3"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye"
                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="trash" class="icon-sm me-2"></i> <span
                                        class="">Delete</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="printer" class="icon-sm me-2"></i> <span
                                        class="">Print</span></a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                        data-feather="download" class="icon-sm me-2"></i> <span
                                        class="">Download</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-start mb-2">
                        <div class="col-md-7">
                            <p class="text-muted tx-13 mb-3 mb-md-0">Revenue is the income that a business has from its
                                normal business activities, usually from the sale of goods and services to customers.</p>
                        </div>
                        <div class="col-md-5 d-flex justify-content-md-end">
                            <div class="btn-group mb-3 mb-md-0" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-outline-primary rounded">Today</button>
                                <button type="button" class="btn btn-outline-primary d-none mx-1 rounded d-md-block">Week</button>
                                <button type="button" class="btn btn-primary mx-1 rounded">Month</button>
                                <button type="button" class="btn btn-outline-primary mx-1 rounded">Year</button>
                            </div>
                        </div>
                    </div>
                    <div id="revenueChart"></div>
                </div>
            </div>
        </div>
    </div> <!-- row --> --}}



    <div class="row">
        <div class="col-lg-5 col-xl-4 grid-margin grid-margin-xl-0 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0 fs-md-5">Latest User</h6>
                        <div class="dropdown mb-2">
                            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton6"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton6">
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.index') }}"><i
                                        data-feather="eye" class="icon-sm me-2"></i> <span class="">All
                                        Users</span></a>

                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        @foreach ($UsersCount as $item)
                            <a href="javascript:;" class="d-flex align-items-center py-2 border-bottom pb-3">
                                <div class="me-3">
                                   
                                    <img src="{{ isset($item->account) && $item->account->profile_picture ? asset($item->account->profile_picture) : url('https://via.placeholder.com/35x35') }}" class="rounded-circle wd-35"
                                        alt="user">
                                </div>
                                <div class="w-100">
                                    <div class="d-flex justify-content-between items-center">
                                        <div>
                                            <h6 class="fw-normal text-body mb-1">{{$item->account->firstName ?? ''}}</h6>
                                            <div class="text-muted tx-13">{{$item->email}}</div>
                                        </div>
                                        <p class="text-muted tx-12">{{ $item->created_at->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-xl-8 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0 fs-md-5">Latest Transaction</h6>
                        <div class="dropdown mb-2">
                            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('all.transactions') }}"><i data-feather="eye"
                                        class="icon-sm me-2"></i> <span class="">Transactions List</span></a>

                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped my-3">
                            <thead class="table-condensed">
                                @php
                                    $index = 1;
                                @endphp
                                <tr class="bg-primary">
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">User Name</th>
                                    <th class="pt-0">Transaction Type</th>
                                    <th class="pt-0">Status</th>
                                    <th class="pt-0">Amount</th>
                                    <th class="pt-0">Tranaction Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactionCount as $transaction)
                                    <tr>
                                        <td>{{ $index }}</td>
                                        <td>{{ $transaction->account->firstName ?? '' }}</td>
                                        <td>{{ $transaction->transaction_type }}</td>
                                        <td>{{ $transaction->status }}</td>
                                        <td>
                                            {{ $transaction->amount }}
                                        </td>
                                        <td>{{ $transaction->transaction_date }}</td>
                                    </tr>
                                    @php
                                        $index++;
                                    @endphp
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- row -->
@endsection


<script>
    const revenueChartData = @json($revenueChartData);
    const revenueChartCategories = @json($revenueChartCategories);
    console.log(revenueChartData);
    console.log(revenueChartCategories);
</script>
<script>
    function loadRevenueData(range) {
        $.ajax({
            url: '{{ route('dashboard.index') }}', // Your endpoint to fetch data
            type: 'GET',
            data: {
                range: range
            },
            success: function(response) {
                revenueChart.data.labels = response.categories;
                revenueChart.data.datasets[0].data = response.data;
                revenueChart.update();
            }
        });
    }

    document.querySelectorAll('.btn-group button').forEach(button => {
        button.addEventListener('click', () => {
            loadRevenueData(button.innerText.toLowerCase());
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('billPaymentsChart').getContext('2d');

        // Data for the chart
        const weeklyBillPayments = {{ $weeklyBillPayments }};
        const monthlyBillPayments = {{ $monthlyBillPayments }};
        const yearlyBillPayments = {{ $yearlyBillPayments }};
        const totalRevenue = {{ $totalRevenue }}; // Revenue from backend

        new Chart(ctx, {
            type: 'bar', // Bar graph
            data: {
                labels: ['Weekly', 'Monthly', 'Yearly'], // X-axis labels
                datasets: [{
                    label: 'Bill Payments',
                    data: [weeklyBillPayments, monthlyBillPayments, yearlyBillPayments],
                    backgroundColor: ['#4CAF50', '#FFC107', '#2196F3'], // Different colors
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            footer: (tooltipItems) => {
                                return `Total Revenue: Rs. ${totalRevenue}`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

