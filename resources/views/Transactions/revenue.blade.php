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
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Completed Bill Payments</h6>
            <div class="row d-flex justify-content-between mb-4">
                <form method="GET" action="{{ route('revenue.index') }}">
                    <div class="row">
                        <div class="col-md-8">

                            <div class="input-group ">
                                <div class="input-group-text">
                                    <i data-feather="search"></i>
                                </div>
                                <input type="text" name="keyword" class="form-control" placeholder="Search here..."
                                    value="{{ request('keyword') }}">
                            </div>
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
                            <th>Service Name</th>
                            <th>Service Charge</th>
                            <th>Comission Revenue</th>
                            <th>Total Revenue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $item)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>   {{ $item->transaction->created_at ? $item->transaction->created_at->format('d-m-Y H:i') : 'N/A' }}</td>
                                <td>{{ $item->user->email}}</td>
                                <td>{{ $item->refference }}</td>
                                <td>{{ $item->billItemName }}</td>
                                <td class="text-center">{{ $item->totalAmount - $item->transaction->amount}}</td>
                                <td class="text-center">
                                    {{ ($item->amount * ($item->provider_percentage_comission / 100)) + $item->provider_fixed_comission }}
                                </td>


                                <td class="text-center">
                                    {{
                                        ($item->totalAmount - $item->transaction->amount) +
                                        (($item->amount * ($item->provider_percentage_comission / 100)) + $item->provider_fixed_comission)
                                    }}
                                </td>

                                <td><a href="{{route('billPayments.transactions.show', $item->user->id)}}"
                                        class="btn btn-outline-primary">View</a></td>
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
