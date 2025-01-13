@extends('layouts.main')

@section('main')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0"></h4>
    </div>
    @if ($smtp)
@else

    <div class="d-flex align-items-center flex-wrap text-nowrap">
        <div class="input-group flatpickr w-200px me-2 mb-2 mb-md-0" id="dashboardDate"></div>
        <a href="{{ route('smtp.create') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud btn-icon-prepend">
                <polyline points="8 17 12 21 16 17"></polyline>
                <line x1="12" y1="12" x2="12" y2="21"></line>
                <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
            </svg>

            Add SMTP Detail
        </a>
    </div>
    @endif
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



                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>Port</th>
                                    <th>App Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
@if($smtp)
                                    <tr>
                                        <th>{{ $smtp->id  }}</th>
                                        <td>{{ $smtp->username }}</td>
                                        <td>{{ $smtp->port }}</td>
                                        <td>{{ $smtp->app_name }}</td>
                                        <td>
                                            <a href="{{route('smtp.edit', $smtp->id)}}" class="btn btn-outline-primary">Edit</a>
                                            {{-- <a href="{{route('faq.delete', $smtp->id)}}" class="btn btn-outline-danger mx-3">Delete</a> --}}
                                        </td>
                                        </td>
                                    </tr>
                                    @endif

                            </tbody>
                        </table>

                        <!-- Pagination Links -->
                        {{-- <div class="d-flex justify-content-between">
                            <div>
                                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                            </div>
                            <div>
                                {{ $users->links('pagination::bootstrap-4') }} <!-- This generates the pagination links -->
                            </div>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additonal-script')
@endsection
