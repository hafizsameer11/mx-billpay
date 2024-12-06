@extends('layouts.main')

@section('main')
<div class="container">
    <h1>Edit Slide</h1>
    <form action="{{ route('slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- Required for updating the record -->

        <div class="mb-3">
            <label for="image" class="form-label">Current Slide Image</label><br>
            <img src="{{ asset('storage/' . $slide->image) }}" alt="Slide Image" width="150">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Upload New Slide Image</label>
            <input type="file" class="form-control" name="image" id="image">
            <small class="form-text text-muted">Leave this empty if you don't want to change the image.</small>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('slides.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
