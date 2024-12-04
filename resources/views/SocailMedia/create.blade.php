@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Social Media</h4>
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

                    <form action="{{route('social.media.store')}}"  method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="">Title</label>
                                <input type="text" name="title" id="" class="form-control" placeholder="Title">
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Icon</label>
                            <input type="file" name="icon" id="" class="form-control">
                            <span class="text-danger">
                                @error('icon')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="">Add Link</label>
                            <input type="text" name="link" id="" class="form-control" placeholder="Add Link">
                            <span class="text-danger">
                                @error('link')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                    </form>

                
            </div>
        </div>
    </div>
@endsection

@section('additonal-script')
@endsection
