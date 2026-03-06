<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- Laravel mein CSRF Token bohat zaroori hai AJAX ke liye --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page_title ?? 'HRMS' }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- <link rel="shortcut icon" href="{{ asset('assets/img/CIT-LOGO.png') }}" /> --}}

    <link rel="stylesheet" href="{{ asset('assets/bundle.css') }}" type="text/css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <link rel="stylesheet" href="{{ asset('assets/clockpicker/bootstrap-clockpicker.min.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/datepicker/daterangepicker.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/dataTable/datatables.min.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/lightbox/magnific-popup.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/form-wizard/jquery.steps.css') }}" type="text/css">
    
    <link rel="stylesheet" href="{{ asset('assets/prism/prism.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" type="text/css">

    <script src="{{ asset('assets/bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ckeditor5 JS --}}
    {{-- <script src="{{ asset('assets/ckeditor5/ckeditor.js') }}"></script> --}}

    </head>
<style>
	@media (max-width: 1200px) {
		.logo {
			height: 90% !important;
			margin-top: 1px !important;
		}

		.header-logo>a>h1 {
			margin-top: 10px !important;
		}

		/* .user_manu {
			width: unset !important;
		} */
	}

  :root {
        --primary-blue: #0f172a;
        --accent-blue: #3b82f6;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }

	/* .user_manu {
		width: 237px;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	} */

	.select2-container .select2-selection--single {
		height: 36px !important;
	}

	.table-responsive{
		padding: 10px !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 36px !important;
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 34px !important;
	}

	div.dt-buttons {
		float: left;
	}

	.dataTables_paginate {
		margin-top: -25px !important;
	}

	body:not(.horizontal-navigation) .navigation .navigation-menu-body ul li>a+ul li a {
		padding-left: 50px;
	}

	.field-icon {
		float: right;
		margin-right: 10px;
		margin-top: -25px;
		position: relative;
		z-index: 2;
	}

	.field-icon-sm {
		float: right;
		margin-right: 10px;
		margin-top: -22px;
		position: relative;
		z-index: 2;
	}

	.fa-spinner {
		font-size: 1.2em;
		margin-right: 5px;
	}

	.view-modal-div .form-group {
		display: flex;
	}

	.view-modal-div .lable-div {
		width: 20% !important;
	}

	.view-modal-div .form-group div {
		width: 80%;
		overflow: auto;
		/* or use overflow: hidden; */
		word-wrap: break-word
	}
</style>