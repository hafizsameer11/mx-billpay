@extends('layouts.main')

@section('main')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        {{-- <li class="breadcrumb-item"><a
                href="{{route('billPayments.transactions.show',$transactions->user->id)}}">Bill Details</a></li> --}}
    </ol>
</nav>
<div class="container mt-4">
    <h3 class="mb-4">Bill Details</h3>
    <div class="row">
        <!-- Left Column (Sender Details) -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title text-primary font-weight-bold">
                            <i class="fas fa-sync-alt me-2"></i> Transaction Date
                        </h6>
                        <p class="text-muted mb-0">{{ $transactions->transaction->transaction_date ?? 'N/A' }}</p>
                    </div>

                    <div class="my-4">
                        <h6 class="text-secondary font-weight-bold">Sender:</h6>
                        <div class="d-flex align-items-center mt-2">
                            <img src="{{ asset('storage/' . $transactions->user->account->profile_picture) ?? asset('assets/images/others/dummyImage.jpg') }}"
                                alt="Sender Avatar" class="rounded-circle me-3 shadow-sm" width="50" height="50">
                            <div>
                                <strong>{{ $transactions->user->account->firstName ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $transactions->user->email ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Customer ID:</strong></p>
                        <p class="text-muted mb-0">{{ $transactions->customerId }}</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Reference:</strong></p>
                        <p class="text-muted mb-0">{{ $transactions->refference }}</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Status:</strong></p>
                        <p class="mb-0">
                            @if ($transactions->status === 'success')
                                <span class="badge bg-success text-white py-1">Success</span>
                            @elseif ($transactions->transaction->status === 'pending')
                                <span class="badge bg-warning text-dark py-1">Pending</span>
                            @else
                                <span class="badge bg-danger text-white py-1">Return</span>
                            @endif
                        </p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Exchange Rate:</strong></p>
                        <p class="text-muted mb-0">1 NGN ⇔ 1 NGN</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Date:</strong></p>
                        <p class="text-muted mb-0">{{ $transactions->transaction->created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm rounded">
                <div class="card-body">
                    <h6 class="card-title text-primary font-weight-bold mb-4">Bill Information</h6>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Category:</strong></p>
                        <p class="text-danger mb-0">{{ $transactions->category->category ?? 'N/A' }}</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Type:</strong></p>
                        <p class="text-muted mb-0">{{ $transactions->billItemName ?? 'N/A' }}</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Mobile Number:</strong></p>
                        <p class="text-muted mb-0">{{ $transactions->phoneNumber ?? 'N/A' }}</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Country Code:</strong></p>
                        <p class="text-muted mb-0">NG</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Amount:</strong></p>
                        <p class="text-muted mb-0">{{ $transactions->amount ?? 'N/A' }}</p>
                    </div>
                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Charges:</strong></p>
                        <p class="text-danger mb-0">
                            {{$transactions->totalAmount - $transactions->amount}}
                        </p>
                    </div>

                    <div class="d-flex justify-content-between my-3">
                        <p class="mb-0"><strong>Payable Amount:</strong></p>
                        <p class="text-muted mb-0">
                            {{$transactions->totalAmount ?? 'N/A'}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="d-flex justify-content-end">
        <a href="{{route('billPayments.transactions')}}" class="btn btn-primary mt-3">Back</a>
    </div>
</div>
@endsection

@section('additonal-script')
<script>
    $(document).ready(function () {
        // $('.nav-link').removeClass('active');
        $('.nav-item').removeClass('active');
        $('.collapse').removeClass('show');
    });
</script>
@endsection
