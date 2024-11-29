@extends('layouts.main')



@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0"></h4>
        </div>
       
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-primary fs-5">Access Token</h6>
                <div class="">
                    <table class="table table-responsive table-hover">
                        <div >
                            <thead class="table-active">
                                <tr>
                                    <th>#</th>
                                    <th>Access Token</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                        </div>
                        <tbody>
                            @foreach ($accessToken as $token)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $token->accesToken }}</td>
                                    <td>{{ $token->status }}</td>
                                    <td>{{ $token->created_at }}</td>
                                    <td>
                                        <a href="{{route('editAccessToken',$token->id)}}" class="btn btn-outline-primary">Edit</a>
                                        <a href="{{route('deleteToken',$token->id)}}" class="btn btn-outline-danger mx-3">Delete</a>
                                    </td>
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
