@extends('layouts.main')

@section('main')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0">Completed Bill Payments</h4>
    </div>
    <div class="d-flex align-items-center flex-wrap text-nowrap">

        {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal"
            data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
    </div>
</div>
<div class="row">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card l-bg-blue-dark">
                <div class="card-body">
                    <h5 class="card-title text-white">Daily Deposit</h5>
                    <h3 class="text-white">NGN {{ number_format($todayCount, 0) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">  <!-- Corrected: Wrapped Weekly Deposit Card -->
            <div class="card l-bg-orange">
                <div class="card-body">
                    <h5 class="card-title text-white">Weekly Deposit</h5>
                    <h3 class="text-white">NGN {{ number_format($weekCount, 0) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card l-bg-cyan">
                <div class="card-body">
                    <h5 class="card-title text-white">Monthly Deposit</h5>
                    <h3 class="text-white">NGN {{ number_format($monthCount, 0) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card l-bg-green">
                <div class="card-body">
                    <h5 class="card-title text-white">Yearly Deposit</h5>
                    <h3 class="text-white">NGN {{ number_format($yearCount, 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="card-title">All Deposits</h6>



            <div class="row d-flex justify-content-between mb-4">
                <form method="GET" action="{{ route('deposit.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <i data-feather="search"></i>
                                </div>
                                <input type="text" name="keyword" class="form-control" placeholder="Search here..."
                                    value="{{ request('keyword') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}" placeholder="Start Date">
                        </div>

                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control"
                                value="{{ request('end_date') }}" placeholder="End Date">
                        </div>

                        <div class="col-md-2 text-center">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Transaction Date</th>
                            <th>User Email</th>
                            <th>Refference</th>
                         <th>Amount</th>
                         <th>Revenue</th>
                            {{-- <th>Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $item)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>   {{ $item->created_at ? $item->created_at->format('d-m-Y H:i') : 'N/A' }}</td>
                                <td>{{ $item->user?->email ?? 'N/A' }}</td>

                                <td>{{ $item->transfer->reference }}</td>
                                <td>{{ $item->amount }}</td>
                                <td class="text-center">
                                    @php
                                        $revenue = $item->amount * 0.003;
                                    @endphp
                                @if($revenue > 300)
                                300
                                @else
                                {{ $revenue }}
                                @endif
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Links -->
                  <!-- Pagination Links -->
                  <div class="d-flex justify-content-between">
                    <div>
                        Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of
                        {{ $transactions->total() }} entries
                    </div>
                    <div>
                        {{ $transactions->links('pagination::bootstrap-4') }}
                        <!-- This generates the pagination links -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


@endsection

@section('additonal-script')

@endsection
