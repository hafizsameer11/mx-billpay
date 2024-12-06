@extends('layouts.main')

@section('main')
<div class="container">
    <h1>Slides</h1>
    <a href="{{ route('slides.create') }}" class="btn btn-primary">Add New Slide</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($slides as $slide)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><img src="{{ asset('storage/' . $slide->image) }}" alt="Slide Image" width="100"></td>
                    <td>
                        <a href="{{ route('slides.edit', $slide->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('slides.destroy', $slide->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
