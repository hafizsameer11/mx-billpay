@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Access Token</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                {{-- <h6 class="card-title">Faq category</h6> --}}
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

                    <form action="{{route('updateToken',$accessToken->id)}}"  method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Access Token</label>
                            <input type="text" name="accesToken" value="{{$accessToken->accesToken}}" class="form-control" id="">
                            <span class="text-danger">
                                @error('accesToken')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Status</label>
                            <select name="status" id="" class="form-control">
                                <option value="">Select</option>
                                <option value="test" {{$accessToken->status === 'test' ? 'selected' : ''}}>Test</option>
                                <option value="live" {{$accessToken->status === 'live' ? 'selected' : ''}}>Live</option>
                            </select>
                            <span class="text-danger">
                                @error('status')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                    </form>

                
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
