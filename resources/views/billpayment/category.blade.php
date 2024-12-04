@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0"></h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr w-200px me-2 mb-2 mb-md-0" id="dashboardDate"></div>
            <a href="{{ route('category.fetch') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download-cloud btn-icon-prepend">
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
                <table class="table table-responsive table-hover">
                    <thead class="table-active">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category Title</th>
                            <th>Category Description</th>
                            <th>Select Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $cat)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>{{ $cat->category }}</td>
                                <td>
                                    <button class="btn btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#categoryTitleModal"
                                            data-id="{{ $cat->id }}"
                                            data-title="{{ $cat->category_title }}">
                                        {{ $cat->category_title ?? 'Add Title' }}
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#categoryDescriptionModal"
                                            data-id="{{ $cat->id }}"
                                            data-title="{{ $cat->category_description }}">
                                        {{ $cat->category_description ?? 'Add Description' }}
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#selectTitleModel"
                                            data-id="{{ $cat->id }}"
                                            data-title="{{ $cat->select_title }}">
                                        {{ $cat->select_title ?? 'Select Title' }}
                                    </button>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="{{ route('billitem.fetch', ['categoryName' => $cat->category]) }}">
                                        Import Items
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Category Title -->
    <div class="modal fade" id="categoryTitleModal" tabindex="-1" aria-labelledby="categoryTitleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryTitleModalLabel">Category Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('category.storeOrUpdate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="modal_category_id_title">
                        <div class="mb-3">
                            <label for="category_title" class="form-label">Category Title</label>
                            <input type="text" class="form-control" name="category_title" id="modal_category_title">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="categoryDescriptionModal" tabindex="-1" aria-labelledby="categoryDescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryDescriptionModalLabel">Category Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('description.storeOrUpdate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="modal_category_id_desc">
                        <div class="mb-3">
                            <label for="category_description" class="form-label">Category Description</label>
                            <input type="text" class="form-control" name="category_description" id="modal_category_description">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="selectTitleModel" tabindex="-1" aria-labelledby="selectTitleModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectTitleModelLabel">Select Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('selectTitle.storeOrUpdate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="modal_category_id_selectTitle">

                        <div class="mb-3">
                            <label for="selectTitle" class="form-label">Select Title</label>
                            <input type="text" class="form-control" name="select_title" id="modal_selectTitle">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additonal-script')
    <script>
        // Success toast
        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: 'right',
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                stopOnFocus: true
            }).showToast();
        @endif

        // Modal Logic
        const categoryTitleModal = document.getElementById('categoryTitleModal');
        categoryTitleModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('modal_category_id_title').value = button.getAttribute('data-id');
            document.getElementById('modal_category_title').value = button.getAttribute('data-title') || '';
        });

        const categoryDescriptionModal = document.getElementById('categoryDescriptionModal');
        categoryDescriptionModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('modal_category_id_desc').value = button.getAttribute('data-id');
            document.getElementById('modal_category_description').value = button.getAttribute('data-title') || '';
        });

        const selectTitleModel = document.getElementById('selectTitleModel');
selectTitleModel.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('modal_category_id_selectTitle').value = button.getAttribute('data-id');
    document.getElementById('modal_selectTitle').value = button.getAttribute('data-title') || '';
});

    </script>
@endsection
