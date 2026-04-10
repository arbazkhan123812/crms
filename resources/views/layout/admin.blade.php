<!DOCTYPE html>
<html lang="en">
@include('_global.Admin.Head')

<body class="hidden-navigation">
    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-icon"></div>
        <span>Loading...</span>
    </div>
    <!-- ./ Preloader -->


    <!-- Layout wrapper -->
    <div class="layout-wrapper">

        @include('_global.Admin.Header') <!-- Content wrapper -->
        <div class="content-wrapper">
            @include('_global.Admin.Navigation') <!-- Content body -->
            <div class="content-body">
               
                @yield('content')
                <div class="modal fade" id="rejected-modal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenter" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" id="modal_content">
                            <form method="post" accept-charset="utf-8" action="" id="rejected-form">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModal1Label">Order Rejected Form</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="reason">Reason:</label>
                                                <textarea type="text" class="form-control" name="reason"
                                                    id="rej_reason"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #dedede;">
                                    <input type="hidden" name="id" id="rej_id">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary btn_rejected" id="add">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @include('_global.Admin.Footer')
            </div>
            <!-- ./ Content body -->
        </div>
        <!-- ./ Content wrapper -->
    </div>
    <!-- ./ Layout wrapper -->



    <!-- Main JS -->


    <!-- Datatable JS -->
    <script src="{{ asset('assets/dataTable/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/examples/datatable.js') }}"></script>

    <script src="{{ asset('assets/js/examples/sweet-alert.js') }}"></script>

    <script src="{{ asset('assets/input-mask/jquery.mask.js') }}"></script>

    <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/examples/select2.js') }}"></script>

    <script src="{{ asset('assets/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('assets/js/examples/pages/form-repeater.js') }}"></script>

    <script src="{{ asset('assets/datepicker/daterangepicker.js') }}"></script>

    <script src="{{ asset('assets/clockpicker/bootstrap-clockpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/examples/clockpicker.js') }}"></script>

    <script src="{{ asset('assets/lightbox/jquery.magnific-popup.min.js') }}"></script>

    <script src="{{ asset('assets/jquery.isotope.min.js') }}"></script>
    <script src="{{ asset('assets/js/examples/pages/gallery.js') }}"></script>

    <script src="{{ asset('assets/form-wizard/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('assets/js/examples/form-wizard.js') }}"></script>

    <script src="{{ asset('assets/prism/prism.js') }}"></script>

    <script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>

</html>

<script>
    $(document).ready(function () {

        $(document).ready(function () {
            $('#myTable').DataTable();
        });

        // Function to initialize or update NiceScroll
        function initializeNiceScroll() {
            $(".content").niceScroll(); // Initialize
            $(".content").getNiceScroll().resize(); // Resize if already initialized
        }

        initializeNiceScroll();

        // Reinitialize nicescroll on every scroll event
        $(".content").scroll(function () {
            initializeNiceScroll();
        });

        // Reinitialize nicescroll on every ajax complete
        $(document).ajaxComplete(function () {
            initializeNiceScroll();
        });

        // Reinitialize NiceScroll when any jQuery/JavaScript function modifies the content
        var observer = new MutationObserver(function () {
            initializeNiceScroll();
        });
        observer.observe(document.querySelector(".content"), {
            childList: true,
            subtree: true
        });


        $('[data-input-mask="cnic"]').mask('0000000000000');
        // $('[data-input-mask="phone"]').mask('000000000000');

        $('.select2').select2({
            placeholder: 'Select'
        });

        $('.select2_multiple').select2({
            placeholder: 'Select',
            closeOnSelect: false
        });

        // Event listener to handle checkbox state when options are selected/deselected
        $('.select2_multiple').on('change', function (e) {
            // Update checkboxes based on selected values
            var selectedValues = $(this).val();
            $('.select2-results__option').each(function () {
                var optionId = $(this).data('select2-id'); // Get the option ID
                $(this).find('input[type="checkbox"]').prop('checked', selectedValues.includes(optionId));
            });
        });

        $('[data-toggle-tooltip="tooltip"]').tooltip();

        if (window.feather) {
            feather.replace();
        }

        if ($('.image-popup').length) {
            $('.image-popup').magnificPopup({
                type: 'image',
                zoom: {
                    enabled: true,
                    duration: 300,
                    easing: 'ease-in-out',
                    opener: function (openerElement) {
                        return openerElement.is('img') ? openerElement : openerElement.find('img');
                    }
                }
            });
        }

        toastr.options = {
            timeOut: 3000,
            progressBar: true,
            showMethod: "slideDown",
            hideMethod: "slideUp",
            showDuration: 200,
            hideDuration: 200
        };

        $(".toggle-password").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        $('.steps').addClass('done').attr('aria-disabled', 'false').attr('aria-selected', 'false');

        $(document).on('click', '.Order_Rejected', function () {
            var id = $(this).data("id");
            $("#rej_id").val(id);
            $('#rejected-modal').modal('show');
        });

        $(document).on('click', '.btn_rejected', function () {
            swal({
                title: "Are you sure?",
                text: "Once marked as rejected, you will not be able to undo this action!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willConfirm) => {
                if (willConfirm) {
                    var form_data = new FormData($('#rejected-form')[0]);
                    var submitButton = $(this);
                    var spinner = submitButton.find('.spinner-border');

                    submitButton.prop('disabled', true);
                    spinner.show();

                    $.ajax({
                        type: "POST",
                        url: "{{ url('Admin/Order_Book/reject_order_book') }}",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (data) {
                            console.log(data);
                            if (data && data.success) {
                                swal("Success", "Add Order Successfully", "success")
                                    .then(function () {
                                        window.location.assign("{{ url('Admin/Order_Book/status/Rejected') }}");
                                    });
                            } else {
                                var errorMsg = (typeof data.error === 'object') ? JSON.stringify(data.error) : data.error;
                                var errors = errorMsg.split('</p>');
                                errors.pop();
                                errors.forEach(function (error) {
                                    var errorMsg = error.replace('<p>', '').trim();
                                    toastr.error(errorMsg);
                                });
                            }

                            submitButton.prop('disabled', false);
                            spinner.hide();
                        },
                        error: function () {
                            swal("Error", "Please Try Again Later!", "error");

                            submitButton.prop('disabled', false);
                            spinner.hide();
                        }
                    });
                } else {
                    swal("Your order status remains unchanged.", {
                        icon: "info",
                    });
                }
            });
        });


    });
</script>
