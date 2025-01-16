@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Biller Items</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add
                Commission</button>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h6 class="card-title text-primary fs-5">Biller Items</h6>
                    <form method="GET" action="{{ route('service.provider') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="title" class="form-control" placeholder="Title"
                                    value="{{ request('title') }}">
                            </div>



                            <div class="col-md-2 text-center">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover p-2">
                        <thead class="p-2 table-active">
                            <tr class="py-2">
                                <th>#</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Change Status</th>
                                <th>Provider Title</th>
                                <th>Provider Description</th>
                                <th>Select Title</th>
                                <th>Logo</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($serviceProviders as $item)
                                <tr>
                                    <th>{{ $item->id }}</th> <!-- Unique ID -->
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->category->category }}</td>
                                    <td>
                                        <a href="{{ route('chnageProviderStatus', ['id' => $item->id]) }}"
                                            class="btn btn-outline-primary">
                                            {{ $item->status ? 'Deactivate' : 'Activate' }}
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#categoryTitleModal" data-id="{{ $item->id }}"
                                            data-title="{{ $item->provider_title }}">
                                            {{ $item->provider_title ?? 'Add Title' }}
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#categoryDescriptionModal" data-id="{{ $item->id }}"
                                            data-title="{{ $item->provider_description }}">
                                            Description
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#selectTitleModel" data-id="{{ $item->id }}"
                                            data-title="{{ $item->select_title }}">
                                            {{ $item->select_title ?? 'Select Title' }}
                                        </button>
                                    </td>
                                    <td>
                                        @if ($item->logo)
                                            <img src="{{ asset($item->logo) }}" alt="Logo"
                                                style="width: 50px; height: auto;">
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-primary update-logo-btn"
                                            data-bs-toggle="modal" data-bs-target="#commissionModal"
                                            data-id="{{ $item->id }}" data-title="{{ $item->title }}">
                                            Update Logo
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            Showing {{ $serviceProviders->firstItem() }} to {{ $serviceProviders->lastItem() }} of
                            {{ $serviceProviders->total() }} entries
                        </div>
                        <div>
                            {{ $serviceProviders->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <div class="modal fade" id="bulkCommissionModal" tabindex="-1" aria-labelledby="bulkCommissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkCommissionModalLabel">Bulk Add Commission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkCommissionForm" method="POST" action="{{route('provider.bulkAddCommission')}}" >
                        @csrf
                        <div class="mb-3">
                            <label for="bulk_fixed_commission" class="form-label">Fixed Commission</label>
                            <input type="number" class="form-control" name="bulk_fixed_commission"
                                id="bulk_fixed_commission" value="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="bulk_percentage_commission" class="form-label">Percentage Commission</label>
                            <input type="number" class="form-control" name="bulk_percentage_commission"
                                id="bulk_percentage_commission" value="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="total_commission" class="form-label">Category Base </label>
                            <select name="biller_category" id="biller_category" class="form-select">
                                <option value="">Select Category</option>
                                @foreach ($options as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Bulk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryTitleModal" tabindex="-1" aria-labelledby="categoryTitleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryTitleModalLabel">Provider Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('provider.storeOrUpdate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="modal_category_id_title">
                        <div class="mb-3">
                            <label for="provider_title" class="form-label">Provider Title</label>
                            <input type="text" class="form-control" name="provider_title" id="modal_category_title">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="categoryDescriptionModal" tabindex="-1" aria-labelledby="categoryDescriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryDescriptionModalLabel">provider Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('provider.description.storeOrUpdate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="modal_category_id_desc">
                        <div class="mb-3">
                            <label for="provider_description" class="form-label">Provider Description</label>
                            <input type="text" class="form-control" name="provider_description"
                                id="modal_category_description">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="selectTitleModel" tabindex="-1" aria-labelledby="selectTitleModelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectTitleModelLabel">Select Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('provider.selectTitle.storeOrUpdate') }}" method="POST">
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

    <!-- Modal for Updating Logo -->
    <div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="commissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commissionModalLabel">Update Logo for <span id="modalItemTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('service.provider.logo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="item_id" id="modalItemId">
                        <div class="mb-3">
                            <label for="logo" class="form-label">Upload New Logo</label>
                            <input type="file" class="form-control" name="logo" id="logo" required>
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
        document.addEventListener('DOMContentLoaded', function() {
            const updateLogoButtons = document.querySelectorAll('.update-logo-btn');

            updateLogoButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-id');
                    const itemTitle = this.getAttribute('data-title');

                    document.getElementById('modalItemId').value = itemId;
                    document.getElementById('modalItemTitle').textContent = itemTitle;
                });
            });
        });

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

        const categoryTitleModal = document.getElementById('categoryTitleModal');
        categoryTitleModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('modal_category_id_title').value = button.getAttribute('data-id');
            document.getElementById('modal_category_title').value = button.getAttribute('data-title') || '';
        });


        const categoryDescriptionModal = document.getElementById('categoryDescriptionModal');
        categoryDescriptionModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('modal_category_id_desc').value = button.getAttribute('data-id');
            document.getElementById('modal_category_description').value = button.getAttribute('data-title') || '';
        });


        const selectTitleModel = document.getElementById('selectTitleModel');
        selectTitleModel.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('modal_category_id_selectTitle').value = button.getAttribute('data-id');
            document.getElementById('modal_selectTitle').value = button.getAttribute('data-title') || '';
        });
    </script>
@endsection
