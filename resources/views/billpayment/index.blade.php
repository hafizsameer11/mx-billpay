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
                <h6 class="card-title text-primary fs-5">Biller Items</h6>
                <div class="table-responsive">
                    <table class="table table-hover p-2">
                        <thead class="p-2 table-active">
                            <tr class="py-2">
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Provider</th>
                                {{-- <th>Category</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($billerItems as $item)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $item->paymentCode }}</td>
                                    <td>{{ $item->category->category ?? 'N/A' }}</td>
                                    <td>{{ $item->provider_name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#commissionModal" data-id="{{ $item->id }}"
                                            data-item-name="{{ $item->paymentitemname }}">Add Commission</button>

                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#renameModal" data-id="{{ $item->id }}"
                                            data-item-name="{{ $item->paymentitemname }}">Rename Item</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        <div>
                            Showing {{ $billerItems->firstItem() }} to {{ $billerItems->lastItem() }} of
                            {{ $billerItems->total() }} entries
                        </div>
                        <div>
                            {{ $billerItems->links('pagination::bootstrap-4') }}
                            <!-- This generates the pagination links -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding commission -->
    <div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="commissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commissionModalLabel">Add Commission for <span id="itemName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="commissionForm">
                        @csrf
                        <input type="hidden" name="item_id" id="item_id">
                        <div class="mb-3">
                            <label for="fixed_commission" class="form-label">Fixed Commission</label>
                            <input type="number" class="form-control" name="fixed_commission" id="fixed_commission"
                                value="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="percentage_commission" class="form-label">Percentage Commission</label>
                            <input type="number" class="form-control" name="percentage_commission"
                                id="percentage_commission" value="0" step="0.01">
                        </div>


                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for bulk adding commission -->
    <div class="modal fade" id="bulkCommissionModal" tabindex="-1" aria-labelledby="bulkCommissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkCommissionModalLabel">Bulk Add Commission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkCommissionForm">
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
                                @foreach ($billerCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Bulk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- modal for rename --}}
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="commissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commissionModalLabel">Rename Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="renameForm" action="{{ route('editBillerItemTitle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" id="item_id">
                        <div class="mb-3">
                            <label for="fixed_commission" class="form-label">Titlte</label>
                            <input type="text" class="form-control" name="item_title" id="item_title" value="">
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

        var commissionModal = document.getElementById('commissionModal');
        commissionModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var itemId = button.getAttribute('data-id'); // Extract info from data-* attributes
            var itemName = button.getAttribute('data-item-name');
            var itemNameSpan = commissionModal.querySelector('#item_title');
            var itemIdInput = commissionModal.querySelector('#item_id');

            itemNameSpan.value = itemName; // Update the modal's content
            itemIdInput.value = itemId;
        });

        // Handle individual commission form submission
        document.getElementById('commissionForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            var formData = new FormData(this);

            // Send the data via AJAX or any method you prefer
            fetch('{{ route('item.addCommission') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            stopOnFocus: true
                        }).showToast();
                        location.reload(); // Reload the page to see the updates
                    } else {
                        alert(data.message); // Handle error messages
                    }
                });
        });

        // Handle bulk commission form submission
        document.getElementById('bulkCommissionForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            var formData = new FormData(this);

            // Send the data via AJAX for bulk update
            fetch('{{ route('item.bulkAddCommission') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token for security
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            stopOnFocus: true
                        }).showToast();
                        location.reload(); // Reload the page to see the updates
                    } else {
                        alert(data.message); // Handle error messages
                    }
                });
        });

        var renameModal = document.getElementById('renameModal');
        renameModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var itemId = button.getAttribute('data-id'); // Extract info from data-* attributes
            var itemName = button.getAttribute('data-item-name');
            var itemNameSpan = renameModal.querySelector('#item_title');
            var itemIdInput = renameModal.querySelector('#item_id');

            itemNameSpan.textContent = itemName; // Update the modal's content
            itemIdInput.value = itemId; // Set the item ID in the hidden input
        });
    </script>
@endsection
