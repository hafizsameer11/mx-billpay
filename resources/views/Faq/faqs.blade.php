@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Faqs</h4>
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

               

                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category Name</th>
                                    <th>Questions</th>
                                    <th>Answers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faqs as $item)
                                    <tr>
                                        <th>{{ $loop->index + 1 }}</th>
                                        <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $item->question ?? 'N/A' }}</td>
                                        <td>{{ Str::words($item->answer ?? 'N/A', 10, '...') }}</td>
                                        <td>
                                            <a href="{{route('faq.edit', $item->id)}}" class="btn btn-outline-primary">Edit</a>
                                            <a href="{{route('faq.delete', $item->id)}}" class="btn btn-outline-danger mx-3">Delete</a>
                                        </td>
                                        </td>
                                    </tr>
                                @endforeach
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
