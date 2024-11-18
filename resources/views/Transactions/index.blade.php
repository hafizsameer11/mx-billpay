@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Transactions List</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Transactions List</h6>
                <div class="row d-flex justify-content-between mb-4">
                    <form method="GET" action="{{ route('all.transactions') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
                            </div>
                
                            <div class="col-md-2">
                                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
                            </div>
                
                            <div class="col-md-2">
                                <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ request('phone') }}">
                            </div>
                
                            <div class="col-md-2">
                                <input type="text" name="account_number" class="form-control" value="{{ request('account_number') }}" placeholder="Account Number">
                            </div>
                
                            <div class="col-md-2">
                                <input type="text" name="bvn" class="form-control" value="{{ request('bvn') }}" placeholder="BVN">
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Account Number</th>
                                <th>BVN</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction Date</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $item)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $item->account->firstName ?? 'N/A' }}{{$item->account->lastName ?? ''}}</td>
                                    <td>{{ $item->user->email }}</td>
                                    <td>{{ $item->account->phone ?? 'N/A' }}</td>
                                    <td>{{ $item->account->account_number ?? 'N/A' }}</td>
                                    <td>{{ $item->account->bvn ?? 'N/A' }}</td>
                                    <td>{{ $item->transaction_type ?? 'N/A' }}</td>
                                    <td>{{ $item->amount ?? 'N/A' }}</td>
                                    <td>{{ $item->status ?? 'N/A' }}</td>
                                    <td>{{ $item->transaction_date ?? 'N/A' }}</td
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        <div>
                            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
                        </div>
                        <div>
                            {{ $transactions->links('pagination::bootstrap-4') }} <!-- This generates the pagination links -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

   
@endsection

@section('additonal-script')
  
@endsection
