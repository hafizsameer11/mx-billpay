@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">SMTP </h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ isset($smtp) ? route('smtp.update', $smtp->id) : route('smtp.store') }}" method="POST">
                    @csrf
             
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="host">SMTP Host</label>
                            <input type="text" name="host" id="host" class="form-control" placeholder="Host"
                                value="{{ old('host', $smtp->host ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_name">SMTP User Name</label>
                            <input type="text" name="user_name" id="user_name" class="form-control"
                                placeholder="User Name" value="{{ old('user_name', $smtp->username ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="port">SMTP Port</label>
                            <input type="text" name="port" id="port" class="form-control" placeholder="Port"
                                value="{{ old('port', $smtp->port ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="from_email">From Email</label>
                            <input type="text" name="from_email" id="from_email" class="form-control"
                                placeholder="From Email" value="{{ old('from_email', $smtp->from_email ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="app_name">App Name</label>
                            <input type="text" name="app_name" id="app_name" class="form-control" placeholder="App Name"
                                value="{{ old('app_name', $smtp->app_name ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="encryption">Encryption</label>
                            <input type="text" name="encryption" id="encryption" class="form-control"
                                placeholder="Encryption" value="{{ old('encryption', $smtp->encryption ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password">Password</label>
                            <input type="text" name="password" id="password" class="form-control"
                                placeholder="Password" value="{{ old('password', $smtp->password ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($smtp) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('additional-script')
@endsection
