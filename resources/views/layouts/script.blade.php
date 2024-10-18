<script src="{{ asset('assets/vendors/core/core.js') }}"></script>
<script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net/dataTables.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>



<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-maxlength.js') }}"></script>
<script src="{{ asset('assets/js/inputmask.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/typeahead.js') }}"></script>
<script src="{{ asset('assets/js/tags-input.js') }}"></script>
<script src="{{ asset('assets/js/dropzone.js') }}"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script src="{{ asset('assets/js/pickr.js') }}"></script>
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/typeahead.js/typeahead.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
<script src="{{ asset('assets/vendors/dropzone/dropzone-min.js') }}"></script>
<script src="{{ asset('assets/vendors/dropify/dist/dropify.min.js') }}"></script>
<script src="{{ asset('assets/vendors/pickr/pickr.min.js') }}"></script>
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;
    var pusher = new Pusher('0ad9e7f7e71dc1522ad6', {
        cluster: 'ap1'
    });
    var channel = pusher.subscribe('biller-categories');
    channel.bind('categories.updated', function(data) {

        Toastify({
            text: "Categories Updated:",
            duration: 3000,
            close: true,
            gravity: "top",
            position: 'right',
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            stopOnFocus: true
        }).showToast();
    });
</script>
