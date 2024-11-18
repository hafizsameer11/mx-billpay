@extends('layouts.main')

@section('main')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            {{-- <li class="breadcrumb-item"><a href="{{route('billPayments.transactions.show',$transactions->user->id)}}">Bill Details</a></li> --}}
        </ol>
    </nav>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-4">User Detail</h3>
            <a href="{{ route('user.index') }}" class="btn btn-secondary mt-3 mb-4">Back</a>
        </div>
        <div class="row">
            <!-- Left Column (Sender Details) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title"><i class="fas fa-sync-alt mr-2 text-primary"></i>user Detail</h6>
                            {{-- <p class="text-muted">{{ $transactions->transaction->transaction_date ?? 'N/A' }}</p> --}}
                        </div>

                        <div class="my-3">
                            <h6>Sender:</h6>
                            <div class="d-flex align-items-center">
                              
                                @if (isset($user->account->profile_picture))

                                    <img src="{{ asset($user->account->profile_picture) }}" alt="Sender Avatar" class="rounded-circle mr-2" width="40" height="40">
                                    @else
                                    <img src="{{ asset('assets/images/others/dummyImage.jpeg') }}" alt="Sender Avatar" class="rounded-circle mr-2" width="40" height="40">
                                @endif


                                <div>
                                    <strong>{{ $user->account->firstName ?? 'N/A' }}</strong><br>
                                    <small>{{ $user->email ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>

                        <p class="my-3"><strong>Phone:</strong> {{ $user->account->phone }}</p>

                    </div>
                </div>
            </div>

            <!-- Right Column (Bill Information) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Account Details</h6>
                        <p class="my-3"><strong>Account Number:</strong> {{ $user->account->account_number }}</p>
                        <p class="my-3"><strong>Bvn:</strong> {{ $user->account->bvn }}</p>
                        <p><strong>Account Type:</strong> {{ $user->account->account_type ?? 'N/A' }}</p>
                        <p class="my-3"><strong>Status :</strong> {{ $user->account->status ?? 'N/A' }}</p>
                        <p class="my-3"><strong>Account Balance:</strong>{{ $user->account->accountBalance ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Left Column (Sender Details) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Transactions</h6>
                        <table class="table">
                            @php
                                $index = 1;
                            @endphp
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Transaction At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->tranaction as $item)
                                    <tr>
                                        <th scope="row">{{ $index }}</th>
                                        <td>{{ $item->amount }}</td>
                                        <td>{{ $item->transaction_type }}</td>
                                        <td>{{ $item->status ?? 'N/A' }}</td>
                                        <td>{{ $item->transaction_date ?? 'N/A' }}</td>
                                    </tr>
                                @php
                                    $index++
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column (Bill Information) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body" style="overflow: auto">
                        <h6 class="card-title" >Bill payments</h6>
                        <table class="table">
                            @php
                                $index = 1;
                            @endphp
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Type</th>
                                    {{-- <th scope="col">Amount</th> --}}
                                    <th scope="col">Charge</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created time
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->billPayment as $item)
                                    <tr>
                                        <th scope="row">{{ $index }}</th>
                                        <td>{{ $item->billerItem->category->category }}</td>
                                        <td>{{ $item->billerItem->paymentitemname }}</td>
                                        <td>{{ $item->billerItem->itemFee }}</td>
                                        <td>{{ $item->status ?? 'N/A' }}</td>
                                        <td>{{ $item->created_at ?? 'N/A' }}</td>
                                    </tr>
                                @php
                                    $index++
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('additonal-script')
<script>
    $(document).ready(function() {
        // $('.nav-link').removeClass('active');
        $('.nav-item').removeClass('active');
        $('.collapse').removeClass('show');
    });
</script>
@endsection
