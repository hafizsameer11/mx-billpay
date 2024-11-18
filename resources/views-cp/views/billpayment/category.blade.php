@extends('layouts.main')



@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0"></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr w-200px me-2 mb-2 mb-md-0" id="dashboardDate">
            </div>

            <a href="{{ route('category.fetch') }}" type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-download-cloud btn-icon-prepend">
                    <polyline points="8 17 12 21 16 17"></polyline>
                    <line x1="12" y1="12" x2="12" y2="21"></line>
                    <path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path>
                </svg>
                Import Categories
            </a>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-primary fs-5">Categories</h6>
                <div class="">
                    <table class="table table-responsive table-hover">
                        <div >
                            <thead class="table-active">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                        </div>
                        <tbody>
                            @foreach ($categories as $cat)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $cat->category }}</td>
                                    <td><a class="btn  btn-sm btn-outline-primary"
                                            href="{{ route('billitem.fetch', ['categoryName' => $cat->category]) }}">Import
                                            Items</a></td>
                                    {{-- <td>Otto</td> --}}
                                    {{-- <td>@mdo</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additonal-script')
    <script>
        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000, // Duration in milliseconds
                close: true, // Show close button
                gravity: "top", // `top` or `bottom`
                position: 'right', // `left`, `center` or `right`
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)", // Customize your background color
                stopOnFocus: true // Prevents dismissing of toast on hover
            }).showToast();
        @endif
    </script>
@endsection
