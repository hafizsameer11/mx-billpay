@extends('layouts.main')

@section('main')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0">Edit User</h4>
    </div>
    <div class="d-flex align-items-center flex-wrap text-nowrap">

        {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal"
            data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
    </div>
</div>
<div class="row">
    <div class="card">
        <div class="card-body ">
            <h6 class="card-title">Edit User</h6>
            <div class="table-responsive">
                <form action="{{route('user.update', $user->id)}}" method="POST" class="overflow-hidden">
                    @csrf
                    @if ($user->account->profile_picture === 'NULL')
                        <img src="{{ asset('assets/images/others/dummyImage.jpeg') }}" class="my-3" alt="User Image"
                            width="150px" height="150px">
                    @else
                        <img src="{{ asset($user->account->profile_picture) }}" class="my-3" alt="User" width="150px"
                            height="150px">
                    @endif

                    <div class="mb-3 row">
                        <div class="col-md-6 mb-3">
                            <label for="">First Name</label>
                            <input type="text" name="firstName" value="{{ $user->account->firstName ?? '' }}" id=""
                                class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Last Name</label>
                            <input type="text" name="lastName" value="{{ $user->account->lastName ?? '' }}" id=""
                                class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6 mb-3">
                            <label for="">Email</label>
                            <input type="text" name="email" value="{{ $user->email ?? '' }}" id="" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Phone</label>
                            <input type="text" name="phone" value="{{ $user->account->phone ?? '' }}" id=""
                                class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">

                        <div class="col-md-6 mb-3">
                            <label for="">Status</label>
                            <select name="status" id="" class="form-control">
                                <option value="PND" {{ $user->account->status === 'PND' ? 'selected' : '' }}>PND
                                </option>
                                <option value="RELEASED" {{ $user->account->status === 'RELEASED' ? 'selected' : '' }}>
                                    RELEASED</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-outline-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
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
