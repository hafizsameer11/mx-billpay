@extends('layouts.main')

@section('main')
<div class="container">
    <h1>Add Slide</h1>
    <form action="{{ route('slides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="image" class="form-label">Slide Image</label>
            <input type="file" class="form-control" name="image" id="image" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
