<footer class="content-footer d-print-none">
    <div>
        © {{ date('Y') }} - <strong>Bazops Technologies</strong>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
       $(document).ready(function() {
    // 1. Initialize NiceScroll once
    var nicescroll = $(".content").niceScroll({
        cursorcolor: "#35394f",
        cursorwidth: "8px",
        cursorborder: "none",
        autohidemode: true
    });

    // 2. Re-calculate only when content changes (AJAX)
    $(document).ajaxComplete(function() {
        if (nicescroll) {
            nicescroll.resize();
        }
    });

    // 3. CSRF and Global Error Handling (Perfect!)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr) {
            if (xhr.status === 403) {
                let errorMsg = xhr.responseJSON && xhr.responseJSON.message 
                               ? xhr.responseJSON.message 
                               : "You do not have the required permissions.";

                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMsg,
                    confirmButtonColor: '#35394f',
                    confirmButtonText: 'Understood'
                });
            } else if (xhr.status === 500) {
                Swal.fire({
                    icon: 'warning',
                    title: 'System Error',
                    text: 'Something went wrong on our end. Please try again later.',
                    confirmButtonColor: '#35394f'
                });
            }
        }
    });
});
    </script>
</footer>