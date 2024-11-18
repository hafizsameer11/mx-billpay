@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Completed Bill Payments</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Completed Bill Payments</h6>
                <div class="row d-flex justify-content-between mb-4">
                    <form method="GET" action="{{ route('complete.billPayments.transactions') }}">
                        <div class="row">
                            <div class="col-md-8">

                                <div class="input-group ">
                                    <div class="input-group-text">
                                        <i data-feather="search"></i>
                                    </div>
                                    <input type="text" name="keyword" class="form-control" placeholder="Search here..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-2 text-center">
                                <select name="status" id="status" class="form-control">
                                    <option value="" class="text-center">--Select Status--</option>
                                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="return" {{ request('status') == 'return' ? 'selected' : '' }}>Return</option>
                                </select>
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
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Charge</th>
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
                                    <td>{{ $item->billerItem->category->category ?? 'N/A' }}</td>
                                    <td>{{ $item->billerItem->paymentitemname ?? 'N/A' }}</td>
                                    <td>{{ $item->transaction->amount }}</td>
                                    <td>{{ $item->billerItem->itemFee ?? 'N/A' }}</td>
                                    <td>{{ $item->account->firstName ?? 'N/A' }} {{ $item->account->lastName ?? 'N/A' }}</td>
                                    <td>{{ $item->status ?? 'N/A' }}</td>
                                    <td>{{ $item->transaction->created_at ?? 'N/A' }}</td>
                                    <td><a href="{{route('billPayments.transactions.show', $item->user->id)}}" class="btn btn-outline-primary">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        {{-- <div>
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                        </div>
                        <div>
                            {{ $users->links('pagination::bootstrap-4') }} <!-- This generates the pagination links -->
                        </div> --}}
                    </div>

                </div>
            </div>
        </div>
    </div>

   
@endsection

@section('additonal-script')
  
@endsection
