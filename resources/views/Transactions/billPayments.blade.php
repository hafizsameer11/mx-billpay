@extends('layouts.main')

@section('main')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0">All Bill Payments</h4>
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
                    <h5 class="card-title text-white">Daily Bill Payments</h5>
                    <h3 class="text-white">NGN {{ number_format($todayCount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card l-bg-cyan">
                <div class="card-body">
                    <h5 class="card-title text-white">Weekly Bill Payments</h5>
                    <h3 class="text-white">NGN {{ number_format($weekCount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card l-bg-green">
                <div class="card-body">
                    <h5 class="card-title text-white">Monthly Bill Payments</h5>
                    <h3 class="text-white">NGN {{ number_format($monthCount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card l-bg-orange">
                <div class="card-body">
                    <h5 class="card-title text-white">Yearly Bill Payments</h5>
                    <h3 class="text-white">NGN {{ number_format($yearCount, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">All Bill Payments</h6>

            <div class="row d-flex justify-content-between mb-4">
                <form method="GET" action="{{ route('billPayments.transactions') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="keyword" class="form-control" placeholder="Search here..."
                                value="{{ request('keyword') }}">
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
                            <th>Category</th>
                            <th>Provider</th>
                            <th>Item</th>
                            <th>Amount</th>
                            <th>Percentage Charges</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Created Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $item)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>{{ $item->category->category ?? 'N/A' }}</td>
                                <td>{{ $item->providerName ?? 'N/A' }}</td>
                                <td>{{ $item->billItemName ?? 'N/A' }}</td>
                                <td>{{ $item->totalAmount }}</td>
                                <td>{{ $item->category->percentage_commission ?? 'N/A' }}</td>
                                <td>{{ $item->user->account->firstName ?? 'N/A' }}
                                    {{ $item->user->account->lastName ?? 'N/A' }}
                                </td>
                                <td>{{ $item->status ?? 'N/A' }}</td>
                                <td>{{ $item->transaction->created_at ?? 'N/A' }}</td>
                                <td><a href="{{ route('billPayments.transactions.show', $item->id) }}"
                                        class="btn btn-outline-primary">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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
{{--
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#status').change(function () {
            let status = $(this).val();
            console.log(status);
            $.ajax({
                url: "{{ route('billPayments.transactions.filter') }}", // Adjust route if necessary
                type: 'GET',
                data: {
                    status: status,
                    keyword: keyword // Optional
                },
                success: function (response) {
                    $('#transactions-table').html(response); // Render response in the table
                },
                error: function (xhr) {
                    console.log(xhr.responseText); // Log any error for debugging
                }
            });

        });
    });
</script> --}}
@endsection
