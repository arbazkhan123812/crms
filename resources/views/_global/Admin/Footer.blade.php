<footer class="content-footer d-print-none">
    <div>
        © {{ date('Y') }} - <strong></strong>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        // Default professional message
                        let errorMsg = "You do not have the required permissions to perform this action.";
                        
                        // Check if backend (Handler.php) returned a specific message
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Access Denied',
                            text: errorMsg,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Understood'
                        });

                    } else if (xhr.status === 500) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Internal Server Error',
                            text: 'An unexpected error occurred. Please contact the system administrator.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                }
            });
        });
    </script>
</footer>