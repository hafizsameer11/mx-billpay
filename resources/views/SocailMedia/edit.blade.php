@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Edit User</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body ">
                <h6 class="card-title">Edit User</h6>
                <div class="table-responsive">
                    <form action="{{ route('social.media.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')  <!-- Use PUT method for updating -->
                        
                        <!-- Display the existing icon -->
                        <div class="col-md-6 mb-3">
                            <label for="icon">Icon</label>
                            <div>
                                @if ($item->icon)
                                    <img src="{{ asset($item->icon) }}" alt="" style="width: 100px; height: 100px;"> <!-- Display the existing image -->
                                @else
                                    <img src="{{ asset('assets/images/others/dummyImage.jpg') }}" alt="" style="width: 100px; height: 100px;"> <!-- Fallback image -->
                                @endif
                            </div>
                            <input type="file" name="icon" class="form-control mt-2"> <!-- New file input for uploading -->
                            <span class="text-danger">
                                @error('icon')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <!-- Other form fields (Title, Link, etc.) -->
                        <div class="col-md-6 mb-3">
                            <label for="title">Title</label>
                            <input type="text" name="title" value="{{ old('title', $item->title) }}" class="form-control">
                            <span class="text-danger">
                                @error('title')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="link">Link</label>
                            <input type="text" name="link" value="{{ old('link', $item->link) }}" class="form-control">
                            <span class="text-danger">
                                @error('link')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                    
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
