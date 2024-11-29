@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Biller Items</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-primary fs-5">Biller Items</h6>
                <div class="table-responsive">
                    <table class="table table-hover p-2">
                        <thead class="p-2 table-active">
                            <tr class="py-2">
                                <th>#</th>
                                <th>Title</th>
                                <th>Category</th>
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
                                        @if ($item->logo)
                                            <img src="{{ asset($item->logo) }}" alt="Logo" style="width: 50px; height: auto;">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary update-logo-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#commissionModal"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}">
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
                            Showing {{ $serviceProviders->firstItem() }} to {{ $serviceProviders->lastItem() }} of {{ $serviceProviders->total() }} entries
                        </div>
                        <div>
                            {{ $serviceProviders->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Updating Logo -->
    <div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="commissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commissionModalLabel">Update Logo for <span id="modalItemTitle"></span></h5>
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
        document.addEventListener('DOMContentLoaded', function () {
            const updateLogoButtons = document.querySelectorAll('.update-logo-btn');

            updateLogoButtons.forEach(button => {
                button.addEventListener('click', function () {
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
    </script>
@endsection
