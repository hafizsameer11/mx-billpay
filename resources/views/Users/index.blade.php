@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Users</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Users</h6>
                <div class="row d-flex justify-content-between mb-4">
                    <form method="GET" action="{{ route('user.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ request('phone') }}">
                            </div>

                            <div class="col-md-2">
                                <input type="date" name="created_at" class="form-control" value="{{ request('created_at') }}">
                            </div>

                            <div class="col-md-2">
                                <input type="date" name="updated_at" class="form-control" value="{{ request('updated_at') }}">
                            </div>

                            <div class="col-md-2 text-center">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>

                                {{-- <th>Status</th> --}}
                                <th>Account Balance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $item)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $item->account->firstName ?? 'N/A' }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->account->phone ?? 'N/A' }}</td>

                                    {{-- <td>{{ $item->account->status  ?? 'N/A' }}</td> --}}
                                    <td>{{ $item->wallet->accountBalance ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{route('user.edit',$item->id)}}" class="btn btn-outline-primary">Edit</a>
                                        <a href="{{route('user.show',$item->id)}}" class="btn btn-outline-secondary mx-2">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between">
                        <div>
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                        </div>
                        <div>
                            {{ $users->links('pagination::bootstrap-4') }} <!-- This generates the pagination links -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding commission -->
    {{-- <div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="commissionModalLabel" aria-hidden="true">
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
                            <input type="number" class="form-control" name="fixed_commission" id="fixed_commission" value="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="percentage_commission" class="form-label">Percentage Commission</label>
                            <input type="number" class="form-control" name="percentage_commission" id="percentage_commission" value="0" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for bulk adding commission -->
    <div class="modal fade" id="bulkCommissionModal" tabindex="-1" aria-labelledby="bulkCommissionModalLabel" aria-hidden="true">
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
                            <input type="number" class="form-control" name="bulk_fixed_commission" id="bulk_fixed_commission" value="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="bulk_percentage_commission" class="form-label">Percentage Commission</label>
                            <input type="number" class="form-control" name="bulk_percentage_commission" id="bulk_percentage_commission" value="0" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Bulk</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('additonal-script')
    {{-- <script>
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
        commissionModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var itemId = button.getAttribute('data-id'); // Extract info from data-* attributes
            var itemName = button.getAttribute('data-item-name');
            var itemNameSpan = commissionModal.querySelector('#itemName');
            var itemIdInput = commissionModal.querySelector('#item_id');

            itemNameSpan.textContent = itemName; // Update the modal's content
            itemIdInput.value = itemId; // Set the item ID in the hidden input
        });

        // Handle individual commission form submission
        document.getElementById('commissionForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission
            var formData = new FormData(this);

            // Send the data via AJAX or any method you prefer
            fetch('{{ route("item.addCommission") }}', {
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
        document.getElementById('bulkCommissionForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission
            var formData = new FormData(this);

            // Send the data via AJAX for bulk update
            fetch('{{ route("item.bulkAddCommission") }}', {
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
    </script> --}}
@endsection
