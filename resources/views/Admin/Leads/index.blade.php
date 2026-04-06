@extends('layout.admin')

@section('content')
    <div class="content">
        <!-- PAGE HEADER -->
        <div class="page-header mb-4">
            @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Validation Errors (Agar Form fail ho jaye) --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-chart-line text-primary mr-2"></i>Leads Management
                    </h3>
                    <p class="text-muted mb-0">Manage and track your sales leads</p>
                </div>
                <div class="col-auto">
                    @can('leads_save')
                        <button type="button" class="btn btn-primary" onclick="openLeadModal()">
                            <i class="fas fa-plus-circle mr-1"></i> New Lead
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- STATISTICS CARDS -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-primary rounded-circle mr-3">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1 text-muted">Total Leads</p>
                                <h4 class="card-title mb-0">{{ $leads->count() }}</h4>
                                <span class="text-muted small">All time leads</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-primary rounded-circle mr-3">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1 text-muted">Qualified</p>
                                <h4 class="card-title mb-0">{{ $leads->where('lead_status', 'Qualified')->count() }}</h4>
                                <span class="text-muted small">Ready for conversion</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-primary rounded-circle mr-3">
                                <i class="fas fa-clock fa-2x text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1 text-muted">New This Month</p>
                                <h4 class="card-title mb-0">
                                    {{ $leads->where('created_at', '>=', now()->startOfMonth())->count() }}
                                </h4>
                                <span class="text-muted small">Last 30 days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="myTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="50"></th>
                                <th>Lead Information</th>
                                <th>Contact Details</th>
                                <th>Lead Source</th>
                                <th>Company</th>
                                <th>Owner</th>
                                <th>Status</th>
                                <th width="60">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                                            <tr class="lead-row" data-href="{{ route('admin.lead.show', $lead->id) }}">
                                                <td class="align-middle text-center">
                                                    @if($lead->lead_image)
                                                        <img src="{{ asset($lead->lead_image) }}" class="rounded-circle" width="35" height="35"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="avatar-initial rounded-circle d-inline-flex align-items-center justify-content-center text-white"
                                                            style="width: 35px; height: 35px; background: #35394F; font-size: 12px;">
                                                            {{ strtoupper(substr($lead->first_name, 0, 1)) }}{{ strtoupper(substr($lead->last_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <div><strong>{{ $lead->first_name }} {{ $lead->last_name }}</strong></div>
                                                    @if($lead->title)
                                                        <small class="text-muted">{{ $lead->title }}</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if($lead->email)
                                                        <div><small><i class="fas fa-envelope text-muted mr-1"></i> {{ $lead->email }}</small>
                                                        </div>
                                                    @endif
                                                    @if($lead->phone)
                                                        <div><small><i class="fas fa-phone text-muted mr-1"></i> {{ $lead->phone }}</small>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="align-middle">{{ $lead->company ?: '-' }}</td>
                                                <td class="align-middle">
                                                    {{ $lead->owner?->full_name ?? ($lead->owner?->username ?? 'Not Assigned') }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $lead->lead_source }}
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $statusColors = ['New' => 'primary', 'Contacted' => 'info', 'Qualified' => 'success', 'Lost' => 'danger', 'Cancelled' => 'warning', 'Junk' => 'secondary'];
                                                        $statusColor = $statusColors[$lead->lead_status] ?? 'secondary';
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $statusColor }} px-2 py-1">{{ $lead->lead_status ?: '-' }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <!-- Dropdown Action Menu -->
                                                    <div class="dropdown">

                                                        @can('leads_performactions')
                                                            <button class="btn btn-sm btn-primary dropdown-toggle hide-caret" type="button"
                                                                onclick="event.stopPropagation()"
                                                                data-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                        @endcan
                                                        <ul class="dropdown-menu dropdown-menu-right shadow-sm" style="min-width: 180px;">
                                                            @can('leads_details')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.lead.show', $lead->id) }}"
                                                                        onclick="event.stopPropagation()">
                                                                        <i class="fas fa-eye text-primary mr-2" style="width: 20px;"></i> View
                                                                        Details
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('leads_addnotes')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openNoteAction({{ $lead->id }}, '{{ addslashes($lead->first_name) }} {{ addslashes($lead->last_name) }}')">
                                                                        <i class="fas fa-sticky-note text-primary mr-2"
                                                                            style="width: 20px;"></i> Add Note
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('leads_addtasks')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openTaskAction({{ $lead->id }}, '{{ addslashes($lead->first_name) }} {{ addslashes($lead->last_name) }}')">
                                                                        <i class="fas fa-tasks text-primary mr-2" style="width: 20px;"></i>
                                                                        Create Task
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('leads_call')
                                                                <li class="dropdown-submenu">
                                                                    <a class="dropdown-item d-flex align-items-center justify-content-between call-submenu-toggle"
                                                                        href="javascript:void(0)">
                                                                        <span class="d-flex align-items-center">
                                                                            <i class="fas fa-phone-alt text-primary mr-2"
                                                                                style="width: 20px;"></i>
                                                                            Call
                                                                        </span>
                                                                        <i class="fas fa-chevron-down text-muted submenu-arrow"></i>
                                                                    </a>
                                                                    <div class="dropdown-submenu-menu">
                                                                        @can('leads_logcall')

                                                                            <a class="dropdown-item d-flex align-items-center dropdown-submenu-item"
                                                                                href="javascript:void(0)"
                                                                                onclick="openCallAction({{ $lead->id }}, '{{ addslashes($lead->first_name) }} {{ addslashes($lead->last_name) }}')">
                                                                                <i class="fas fa-phone-volume text-primary mr-2"
                                                                                    style="width: 20px;"></i>
                                                                                Log Call
                                                                            </a>
                                                                        @endcan
                                                                        @can('leads_createcall')

                                                                            <a class="dropdown-item d-flex align-items-center dropdown-submenu-item"
                                                                                href="javascript:void(0)"
                                                                                onclick="openScheduleCallModal({{ $lead->id }})">
                                                                                <i class="fas fa-calendar-plus text-primary mr-2"
                                                                                    style="width: 20px;"></i>
                                                                                Create a Call
                                                                            </a>
                                                                        @endcan
                                                                    </div>
                                                                </li>
                                                            @endcan

                                                            @can('leads_sendemail')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openEmailAction({{ $lead->id }}, '{{ addslashes($lead->first_name) }} {{ addslashes($lead->last_name) }}', '{{ $lead->email }}')">
                                                                        <i class="fas fa-envelope text-primary mr-2" style="width: 20px;"></i>
                                                                        Send Email
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                              @can('leads_convert')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center"
                                                                        href="{{ route('admin.lead.convert', $lead->id) }}">
                                                                        <i class="fas fa-exchange-alt text-primary mr-2"
                                                                            style="width: 20px;"></i>
                                                                        Convert to Customer
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('leads_schedulemeeting')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openMeetingModal({{ $lead->id }})">
                                                                        <i class="fas fa-handshake text-primary mr-2" style="width: 20px;"></i>
                                                                        Schedule Meeting
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                          
                                                        </ul>
                                                    </div>
                                                    @can('leads_edit')
                                                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); editLead({{ $lead->id }})" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endcan
                                                    
                                                    @can('leads_delete')
                                                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); deleteLead({{ $lead->id }})"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endcan

                                </div>
                                </td>
                                </tr>
                            @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-paper-plane mr-2"></i>Send Email to <span id="emailLeadName"></span>
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="emailForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="email_lead_id">
                    <div class="modal-body">
                        <!-- From Email -->

                        <!-- To Email -->
                        <div class="form-group">
                            <label class="form-label mb-1">To</label>
                            <input type="email" name="to_email" id="email_to" class="form-control" required>
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label class="form-label mb-1">Subject</label>
                            <input type="text" name="subject" id="email_subject" class="form-control" required>
                        </div>

                        <!-- Template Selector -->
                        <div class="form-group">
                            <label class="form-label mb-1">Email Template</label>
                            <div class="input-group">
                                <select name="template_id" id="email_template" class="form-control">
                                    <option value="">-- Select Template --</option>
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" onclick="openTemplateModal()">
                                        <i class="fas fa-plus"></i> New
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Use {name}, {company}, {email}, {phone} as placeholders</small>
                        </div>

                        <!-- Message Body -->
                        <div class="form-group">
                            <label class="form-label mb-1">Message</label>
                            <textarea name="body" id="email_body" rows="10" class="form-control"
                                placeholder="Write your message here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i> Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template Modal -->
    <div class="modal fade" id="templateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-save mr-2"></i>Save as Template
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="templateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label mb-1">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="template_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Category</label>
                            <input type="text" name="category" id="template_category" class="form-control"
                                placeholder="e.g., Follow-up, Proposal, Welcome">
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="template_subject" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Message Body <span class="text-danger">*</span></label>
                            <textarea name="body" id="template_body" rows="6" class="form-control" required></textarea>
                            <small class="text-muted d-block mt-1">Use: {name}, {company}, {email}, {phone}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Lead Modal (Create/Edit) -->
    <div class="modal fade" id="leadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="leadModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Create New Lead
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="leadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="lead_id">
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Profile Image -->
                        <div class="card mb-2 border">
                            <div class="card-header bg-light py-1">
                                <h6 class="mb-0"><i class="fas fa-image text-primary mr-2"></i>Profile Image</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <img id="leadImagePreview" src="" class="rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover; display: none;">
                                        <div id="imagePlaceholder">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border"
                                                style="width: 60px; height: 60px;">
                                                <i class="fas fa-user fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="file" name="lead_image" id="lead_image"
                                            class="form-control-file form-control-sm" accept="image/*"
                                            onchange="previewImage(this)">
                                        <small class="text-muted d-block mt-1">JPG, PNG or GIF (Max 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lead Information -->
                        <div class="card mb-2 border">
                            <div class="card-header bg-light py-1">
                                <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Lead Information</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="first_name"
                                                class="form-control form-control-sm" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Last Name</label>
                                            <input type="text" name="last_name" id="last_name"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Lead Owner <span class="text-danger">*</span></label>
                                            <select name="lead_owner" id="lead_owner" class="form-control form-control-sm"
                                                required>
                                                <option value="">Select Lead Owner</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Email Address</label>
                                            <input type="email" name="email" id="email"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Phone</label>
                                            <input type="text" name="phone" id="phone" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Mobile</label>
                                            <input type="text" name="mobile" id="mobile"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Company Name</label>
                                            <input type="text" name="company" id="company"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Website</label>
                                            <input type="url" name="website" id="website"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Lead Source</label>
                                            <select name="lead_source" id="lead_source"
                                                class="form-control form-control-sm">
                                                <option value="">None</option>
                                                @foreach($leadSources as $source)
                                                    <option value="{{ $source }}">{{ $source }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Lead Status</label>
                                            <select name="lead_status" id="lead_status"
                                                class="form-control form-control-sm">
                                                <option value="">Select Status</option>
                                                @foreach($leadStatuses as $status)
                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Annual Revenue</label>
                                            <input type="number" name="annual_revenue" id="annual_revenue"
                                                class="form-control form-control-sm" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Employees</label>
                                            <input type="number" name="no_of_employees" id="no_of_employees"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card mb-2 border">
                            <div class="card-header bg-light py-1">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt text-primary mr-2"></i>Address Information
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Street Address</label>
                                            <input type="text" name="street" id="street"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">City</label>
                                            <input type="text" name="city" id="city" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">State</label>
                                            <input type="text" name="state" id="state" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Country</label>
                                            <input type="text" name="country" id="country"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label class="small mb-0">Zip Code</label>
                                            <input type="text" name="zip_code" id="zip_code"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="card mb-2 border">
                            <div class="card-header bg-light py-1">
                                <h6 class="mb-0"><i class="fas fa-align-left text-primary mr-2"></i>Description</h6>
                            </div>
                            <div class="card-body p-2">
                                <textarea name="description" id="description" rows="2" class="form-control form-control-sm"
                                    placeholder="Enter additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Save Lead</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Lead Modal -->
    <div class="modal fade" id="viewLeadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-circle mr-2"></i>Lead Details
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="viewLeadContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-sticky-note mr-2"></i>Add Note
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="noteForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="note_lead_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="small mb-1">Lead Name</label>
                            <input type="text" id="note_lead_name" class="form-control form-control-sm" readonly disabled>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small mb-1">Note</label>
                            <textarea name="note" id="note_content" rows="4" class="form-control form-control-sm" required
                                placeholder="Write your note here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Add Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks mr-2"></i>Create Task
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="taskForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="task_lead_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="small mb-1">Lead Name</label>
                            <input type="text" id="task_lead_name" class="form-control form-control-sm" readonly disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="task_subject" class="form-control form-control-sm"
                                required placeholder="e.g., Call back, Send proposal">
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Assign To <span class="text-danger">*</span></label>
                            <select name="assigned_to" id="task_assigned_to" class="form-control form-control-sm" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Due Date</label>
                            <input type="datetime-local" name="due_date" id="task_due_date"
                                class="form-control form-control-sm">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small mb-1">Description</label>
                            <textarea name="description" id="task_description" rows="3" class="form-control form-control-sm"
                                placeholder="Task details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Log Call Modal -->
    <div class="modal fade" id="logCallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-phone-alt mr-2"></i>Log a Call
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="logCallForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="logCall_lead_id">
                    <input type="hidden" id="log_call_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label mb-1">Call Type</label>
                            <select name="call_type" id="log_call_type" class="form-control" required>
                                <option value="outbound"> Outbound (You called)</option>
                                <option value="inbound"> Inbound (Lead called)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Call Purpose</label>
                            <select name="call_purpose" id="log_call_purpose" class="form-control">
                                <option value="">Select Purpose</option>
                                <option value="Initial Contact">Initial Contact</option>
                                <option value="Follow-up">Follow-up</option>
                                <option value="Proposal Discussion">Proposal Discussion</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Closing">Closing</option>
                                <option value="Support">Support</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Call Status</label>
                            <select name="status" id="log_call_status" class="form-control" required>
                                <option value="completed"> Completed</option>
                                <option value="missed"> Missed</option>
                                <option value="voicemail"> Voicemail</option>
                                <option value="no_answer"> No Answer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Duration</label>
                            <input type="text" name="duration" id="log_call_duration" class="form-control"
                                placeholder="e.g., 5:30">
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Call Date & Time</label>
                            <input type="datetime-local" name="call_date" id="log_call_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label mb-1">Notes</label>
                            <textarea name="notes" id="log_call_notes" rows="3" class="form-control"
                                placeholder="Call notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Log Call</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Meeting Modal -->
    <div class="modal fade" id="meetingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt mr-2"></i>Schedule Meeting
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="meetingForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="meeting_lead_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label mb-1">Meeting Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="meeting_title" class="form-control" required
                                placeholder="e.g., Proposal Discussion, Product Demo">
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Meeting Type</label>
                            <select name="meeting_type" id="meeting_type" class="form-control">
                                <option value="virtual"> Virtual (Zoom/Google Meet)</option>
                                <option value="physical"> Physical (In-person)</option>
                                <option value="phone"> Phone Call</option>
                            </select>
                        </div>
                        <div class="form-group" id="location_field">
                            <label class="form-label mb-1">Location/Venue</label>
                            <input type="text" name="location" id="meeting_location" class="form-control"
                                placeholder="Office address, coffee shop, etc.">
                        </div>
                        <div class="form-group" id="link_field" style="display: none;">
                            <label class="form-label mb-1">Meeting Link</label>
                            <input type="url" name="meeting_link" id="meeting_link" class="form-control"
                                placeholder="https://zoom.us/... or https://meet.google.com/...">
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Meeting Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="meeting_date" id="meeting_date" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Duration</label>
                            <input type="text" name="duration" id="meeting_duration" class="form-control"
                                placeholder="e.g., 25 minutes, 1 hour, 90 mins">
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Assigned To <span class="text-danger">*</span></label>
                            <select name="assigned_to" id="meeting_assigned_to" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label mb-1">Description/Agenda</label>
                            <textarea name="description" id="meeting_description" rows="3" class="form-control"
                                placeholder="What will be discussed?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule Meeting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Call Modal -->
    <div class="modal fade" id="scheduleCallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus mr-2"></i>Create a Call
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="scheduleCallForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="scheduleCall_lead_id">
                    <input type="hidden" id="schedule_call_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label mb-1">Call Type</label>
                            <select name="call_type" id="schedule_call_type" class="form-control" required>
                                <option value="outbound"> Outbound </option>
                                <option value="inbound"> Inbound </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Call Purpose</label>
                            <select name="call_purpose" id="schedule_call_purpose" class="form-control">
                                <option value="">Select Purpose</option>
                                <option value="Initial Contact">Initial Contact</option>
                                <option value="Follow-up">Follow-up</option>
                                <option value="Proposal Discussion">Proposal Discussion</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Closing">Closing</option>
                                <option value="Support">Support</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Schedule Date & Time</label>
                            <input type="datetime-local" name="call_date" id="schedule_call_date" class="form-control"
                                required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label mb-1">Notes</label>
                            <textarea name="notes" id="schedule_call_notes" rows="3" class="form-control"
                                placeholder="Call notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule Call</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completeCallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle mr-2"></i>Complete Scheduled Call
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="completeCallForm">
                    @csrf
                    <input type="hidden" id="complete_call_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label mb-1">Duration</label>
                            <input type="text" id="complete_call_duration" class="form-control" placeholder="e.g., 5:30">
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-1">Call Status</label>
                            <select id="complete_call_status" class="form-control" required>
                                <option value="completed">Completed</option>
                                <option value="missed">Missed</option>
                                <option value="voicemail">Voicemail</option>
                                <option value="no_answer">No Answer</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label mb-1">Notes</label>
                            <textarea id="complete_call_notes" rows="3" class="form-control"
                                placeholder="Call notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completeMeetingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check mr-2"></i>Complete Meeting
                    </h5>
                    <button type="button" class="close text-primary" data-dismiss="modal">&times;</button>
                </div>
                <form id="completeMeetingForm">
                    @csrf
                    <input type="hidden" id="complete_meeting_id">
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label class="form-label mb-1">Meeting Notes</label>
                            <textarea id="complete_meeting_notes" rows="4" class="form-control"
                                placeholder="Meeting summary, decisions, next steps..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark Complete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentLeadId = null;
        let currentLeadName = null;
        let loggedCallsCache = [];
        let scheduledCallsCache = [];

        $(document).ready(function () {

            $('.lead-row').on('click', function () {
                let targetUrl = $(this).data('href');
                if (targetUrl) {
                    window.location.href = targetUrl;
                }
            });

            $('.lead-row').find('a, button, .dropdown-menu, .dropdown-toggle').on('click', function (e) {
                e.stopPropagation();
            });


            $('#leadForm').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route("admin.lead.save") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong!' });
                    }
                });
            });

            $('#noteForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("admin.lead.addNote") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#noteModal').modal('hide');
                            $('#noteForm')[0].reset();
                            if (currentLeadId) {
                                loadLeadNotes(currentLeadId);
                            }
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });

            $('#taskForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("admin.lead.addTask") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#taskModal').modal('hide');
                            $('#taskForm')[0].reset();
                            if (currentLeadId) {
                                loadLeadTasks(currentLeadId);
                            }
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });

            $('.call-submenu-toggle').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                let $submenu = $(this).closest('.dropdown-submenu');
                $('.dropdown-submenu').not($submenu).removeClass('show')
                    .children('.dropdown-submenu-menu').removeClass('show');

                $submenu.toggleClass('show');
                $submenu.children('.dropdown-submenu-menu').toggleClass('show');
            });

            $('.call-submenu-toggle').on('mouseenter', function () {
                let $submenu = $(this).closest('.dropdown-submenu');
                $submenu.addClass('show');
                $submenu.children('.dropdown-submenu-menu').addClass('show');
            });

            $('.dropdown-submenu-menu').on('mouseenter', function () {
                $(this).addClass('show').closest('.dropdown-submenu').addClass('show');
            });

            $('.dropdown-submenu').on('mouseleave', function () {
                $(this).removeClass('show');
                $(this).children('.dropdown-submenu-menu').removeClass('show');
            });

            $('.dropdown').on('hidden.bs.dropdown', function () {
                $(this).find('.dropdown-submenu').removeClass('show');
                $(this).find('.dropdown-submenu-menu').removeClass('show');
            });



            // Meeting type change event
            $(document).on('change', '#meeting_type', function () {
                toggleMeetingFields();
            });

            // Meeting Form Submit
            $('#meetingForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("admin.lead.scheduleMeeting") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#meetingModal').modal('hide');
                            $('#meetingForm')[0].reset();
                            loadLeadMeetings(currentLeadId);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong!' });
                    }
                });
            });




            // Log Call Form Submit
            $('#logCallForm').on('submit', function (e) {
                e.preventDefault();
                let now = new Date();
                let formattedDate = now.toISOString().slice(0, 16);
                if (!$('#log_call_date').val()) {
                    $('#log_call_date').val(formattedDate);
                }

                let callId = $('#log_call_id').val();
                let formData = $(this).serializeArray();

                if (callId) {
                    formData.push({ name: '_method', value: 'PUT' });
                }

                $.ajax({
                    url: callId ? ('{{ url("admin/leads/calls") }}/' + callId) : '{{ route("admin.lead.addCall") }}',
                    type: 'POST',
                    data: $.param(formData),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#logCallModal').modal('hide');
                            $('#logCallForm')[0].reset();
                            $('#log_call_id').val('');
                            loadLeadCalls(currentLeadId);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });

            // Schedule Call Form Submit
            $('#scheduleCallForm').on('submit', function (e) {
                e.preventDefault();
                let callId = $('#schedule_call_id').val();
                let formData = $(this).serializeArray();

                if (callId) {
                    formData.push({ name: '_method', value: 'PUT' });
                    formData.push({ name: 'status', value: 'scheduled' });
                }

                $.ajax({
                    url: callId ? ('{{ url("admin/leads/calls") }}/' + callId) : '{{ route("admin.lead.scheduleCall") }}',
                    type: 'POST',
                    data: $.param(formData),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#scheduleCallModal').modal('hide');
                            $('#scheduleCallForm')[0].reset();
                            $('#schedule_call_id').val('');
                            loadLeadCalls(currentLeadId);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });

            $('#completeCallForm').on('submit', function (e) {
                e.preventDefault();

                let callId = $('#complete_call_id').val();
                let call = scheduledCallsCache.find(item => item.id == callId);

                $.ajax({
                    url: '{{ url("admin/leads/calls") }}/' + callId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        lead_id: call ? call.lead_id : currentLeadId,
                        call_type: call ? call.call_type : 'outbound',
                        call_purpose: call ? (call.call_purpose || '') : '',
                        call_date: call ? call.call_date : '',
                        duration: $('#complete_call_duration').val(),
                        status: $('#complete_call_status').val(),
                        notes: $('#complete_call_notes').val(),
                        called_by: call ? call.called_by : ''
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#completeCallModal').modal('hide');
                            $('#completeCallForm')[0].reset();
                            loadLeadCalls(currentLeadId);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });

            $('#completeMeetingForm').on('submit', function (e) {
                e.preventDefault();

                let meetingId = $('#complete_meeting_id').val();

                $.ajax({
                    url: '{{ url("admin/leads/meetings") }}/' + meetingId + '/complete',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        notes: $('#complete_meeting_notes').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                            $('#completeMeetingModal').modal('hide');
                            $('#completeMeetingForm')[0].reset();
                            loadLeadMeetings(currentLeadId);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });
        });

        // Complete Meeting
        function completeMeeting(meetingId) {
            $('#complete_meeting_id').val(meetingId);
            $('#complete_meeting_notes').val('');
            $('#completeMeetingModal').modal('show');
        }

        // Delete Meeting
        function deleteMeeting(meetingId) {
            Swal.fire({
                title: 'Delete Meeting?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/leads/meetings") }}/' + meetingId,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false });
                                loadLeadMeetings(currentLeadId);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                            }
                        }
                    });
                }
            });
        }


        // View Lead Details
        function viewLead(id) {
            currentLeadId = id;
            $('#viewLeadModal').modal('show');
            $('#viewLeadContent').html(`
                                                <div class="p-3">
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                                        <p class="mt-2">Loading lead details...</p>
                                                    </div>
                                                </div>
                                            `);

            // Load lead details
            $.ajax({
                url: '{{ url("admin/leads") }}/' + id,
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        displayLeadDetails(response.data);
                        loadLeadCalls(id);
                        loadLeadMeetings(id);
                        loadLeadNotes(id);
                        loadLeadTasks(id);
                    }
                }
            });
        }

        // Load Lead Meetings
        function loadLeadMeetings(leadId) {
            $.ajax({
                url: '{{ url("admin/leads") }}/' + leadId + '/meetings',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        displayUpcomingMeetings(response.upcoming_meetings);
                        displayPastMeetings(response.completed_meetings);
                    }
                }
            });
        }

        // Display Upcoming Meetings
        function displayUpcomingMeetings(meetings) {
            if (meetings.length === 0) {
                $('#upcomingMeetingsList').html('<div class="alert alert-info">No upcoming meetings scheduled.</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead class="thead-light"><tr><th>Date/Time</th><th>Title</th><th>Type</th><th>Location/Link</th><th>Assigned To</th><th width="100">Actions</th></tr></thead><tbody>';

            meetings.forEach(meeting => {
                let typeIcon = meeting.meeting_type == 'virtual' ? '' : (meeting.meeting_type == 'physical' ? '🏢' : '📞');
                let locationDisplay = meeting.meeting_type == 'virtual' ?
                    (meeting.meeting_link ? '<a href="' + meeting.meeting_link + '" target="_blank">Join Link</a>' : meeting.location || '-') :
                    (meeting.location || '-');

                html += `
                        <tr>
                            <td>${new Date(meeting.meeting_date).toLocaleString()}</td>
                            <td><strong>${escapeHtml(meeting.title)}</strong>${meeting.description ? '<br><small class="text-muted">' + escapeHtml(meeting.description.substring(0, 50)) + '</small>' : ''}</td>
                            <td>${typeIcon} ${meeting.meeting_type}</td>
                            <td>${locationDisplay}</td>
                            <td>${escapeHtml(meeting.assigned_to_name || '-')}</td>
                            <td>
                                @can('leads_markcompleteupcommingmeeting')

                                    <button class="btn btn-sm btn-primary" onclick="completeMeeting(${meeting.id})" title="Mark Complete">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endcan
                                @can('leads_deleteupcomingmeeting') 

                                    <button class="btn btn-sm btn-danger" onclick="deleteMeeting(${meeting.id})" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    `;
            });

            html += '</tbody></table></div>';
            $('#upcomingMeetingsList').html(html);
        }

        // Display Past Meetings
        function displayPastMeetings(meetings) {
            if (meetings.length === 0) {
                $('#pastMeetingsList').html('<div class="alert alert-info">No past meetings found.</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead class="thead-light"><tr><th>Date/Time</th><th>Title</th><th>Type</th><th>Notes</th><th>Status</th><th>Actions</th></tr></thead><tbody>';

            meetings.forEach(meeting => {
                let typeIcon = meeting.meeting_type == 'virtual' ? '' : (meeting.meeting_type == 'physical' ? '🏢' : '📞');

                html += `
                        <tr>
                            <td>${new Date(meeting.meeting_date).toLocaleString()}</td>
                            <td><strong>${escapeHtml(meeting.title)}</strong>${meeting.description ? '<br><small class="text-muted">' + escapeHtml(meeting.description.substring(0, 50)) + '</small>' : ''}</td>
                            <td>${typeIcon} ${meeting.meeting_type}</td>
                            <td><small>${escapeHtml(meeting.notes ? meeting.notes.substring(0, 50) : '-')}</small></td>
                            <td><span class="badge badge-success">${meeting.status}</span></td>
                            <td>
                                @can('leads_deletepastmeeting')

                                    <button class="btn btn-sm btn-danger" onclick="deleteMeeting(${meeting.id})" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    `;
            });

            html += '</tbody></table></div>';
            $('#pastMeetingsList').html(html);
        }

        // Open Meeting Modal
        function openMeetingModal(leadId) {
            $('#meeting_lead_id').val(leadId);
            $('#meeting_title').val('');
            $('#meeting_type').val('virtual');
            $('#meeting_location').val('');
            $('#meeting_link').val('');
            $('#meeting_description').val('');
            $('#meeting_duration').val('');
            $('#meeting_assigned_to').val('');

            let now = new Date();
            now.setHours(now.getHours() + 2);
            let formattedDate = now.toISOString().slice(0, 16);
            $('#meeting_date').val(formattedDate);

            // Toggle fields based on meeting type
            toggleMeetingFields();

            $('#meetingModal').modal('show');
        }

        // Toggle Meeting Fields
        function toggleMeetingFields() {
            let meetingType = $('#meeting_type').val();
            if (meetingType === 'virtual') {
                $('#location_field').hide();
                $('#link_field').show();
                $('#meeting_link').prop('required', false);
                $('#meeting_location').prop('required', false);
            } else if (meetingType === 'physical') {
                $('#location_field').show();
                $('#link_field').hide();
                $('#meeting_location').prop('required', true);
                $('#meeting_link').prop('required', false);
            } else {
                $('#location_field').hide();
                $('#link_field').hide();
                $('#meeting_location').prop('required', false);
                $('#meeting_link').prop('required', false);
            }
        }
        function displayLeadDetails(lead) {
            let html = `
                                                <div class="p-3">
                                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                                        <div class="mr-3">
                                                            ${lead.lead_image ?
                    `<img src="{{ asset('') }}${lead.lead_image}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">` :
                    `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-user fa-1x"></i>
                                                                </div>`
                }
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-0">${escapeHtml(lead.first_name)} ${escapeHtml(lead.last_name)}</h5>
                                                            <small class="text-muted">${escapeHtml(lead.email || 'No email')} | ${escapeHtml(lead.phone || 'No phone')}</small>
                                                        </div>
                                                    </div>

                                                    <!-- Tabs -->
                                                    <ul class="nav nav-tabs" id="leadTabs" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">
                                                                <i class="fas fa-info-circle"></i> Information
                                                            </a>
                                                        </li>
                                                        @can('leads_viewschedulemeeting') 

                                                            <li class="nav-item">
                                                                <a class="nav-link" id="meetings-tab" data-toggle="tab" href="#meetings" role="tab">
                                                                    <i class="fas fa-calendar-alt"></i> Meetings
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('leads_call')
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="calls-tab" data-toggle="tab" href="#calls" role="tab">
                                                                    <i class="fas fa-phone-alt"></i> Calls
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('leads_notes')

                                                            <li class="nav-item">
                                                                <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab">
                                                                    <i class="fas fa-sticky-note"></i> Notes
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('leads_task')
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab">
                                                                    <i class="fas fa-tasks"></i> Tasks
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>

                                                    <div class="tab-content mt-3" id="leadTabsContent">
                                                        <!-- Information Tab -->
                                                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <table class="table table-sm">
                                                                        <tr><th width="35%">Company:</th><td>${escapeHtml(lead.company || '-')}</td></tr>
                                                                        <tr><th>Title:</th><td>${escapeHtml(lead.title || '-')}</td></tr>
                                                                        <tr><th>Lead Source:</th><td>${escapeHtml(lead.lead_source || '-')}</td></tr>
                                                                        <tr><th>Lead Status:</th><td>${escapeHtml(lead.lead_status || '-')}</td></tr>
                                                                        <tr><th>Annual Revenue:</th><td>${lead.annual_revenue ? 'PKR ' + parseFloat(lead.annual_revenue).toLocaleString() : '-'}</td></tr>
                                                                        <tr><th>Employees:</th><td>${escapeHtml(lead.no_of_employees || '-')}</td></tr>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <table class="table table-sm">
                                                                        <tr><th>Owner:</th><td>${escapeHtml(lead.owner?.full_name || lead.owner?.username || '-')}</td></tr>
                                                                        <tr><th>Created By:</th><td>${escapeHtml(lead.user?.full_name || lead.user?.username || '-')}</td></tr>
                                                                        <tr><th>Created At:</th><td>${new Date(lead.created_at).toLocaleString()}</td></tr>
                                                                        <tr><th>Last Contacted:</th><td>${lead.last_contacted_at ? new Date(lead.last_contacted_at).toLocaleString() : '-'}</td></tr>
                                                                    </table>
                                                                </div>
                                                                <div class="col-12">
                                                                    <hr>
                                                                    <h6>Address</h6>
                                                                    <p>${escapeHtml(lead.street || '')}${lead.street ? '<br>' : ''}
                                                                    ${escapeHtml(lead.city || '')}${lead.city ? ', ' : ''}
                                                                    ${escapeHtml(lead.state || '')}${lead.state ? '<br>' : ''}
                                                                    ${escapeHtml(lead.country || '')}${lead.zip_code ? ' - ' + escapeHtml(lead.zip_code) : ''}</p>
                                                                    <hr>
                                                                    <h6>Description</h6>
                                                                    <p>${escapeHtml(lead.description || 'No description')}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Meetings Tab -->
                <div class="tab-pane fade" id="meetings" role="tabpanel">
                    <div class="mb-3">
                        <button class="btn btn-sm btn-primary" onclick="openMeetingModal(${lead.id})">
                            <i class="fas fa-plus mr-2"></i> Schedule Meeting
                        </button>
                    </div>

                    <!-- Sub Tabs for Meetings -->
                    <ul class="nav nav-tabs" id="meetingsSubTabs" role="tablist">
                        @can('leads_viewupcomingmeeting') 

                            <li class="nav-item">
                                <a class="nav-link active" id="upcoming-meetings-tab" data-toggle="tab" href="#upcomingMeetings" role="tab">
                                    <i class="fas fa-clock"></i> Upcoming Meetings
                                </a>
                            </li>
                        @endcan
                        @can('leads_viewpastmeeting') 

                            <li class="nav-item">
                                <a class="nav-link" id="past-meetings-tab" data-toggle="tab" href="#pastMeetings" role="tab">
                                    <i class="fas fa-history"></i> Past Meetings
                                </a>
                            </li>
                        @endcan
                    </ul>

                    <div class="tab-content mt-3" id="meetingsSubTabsContent">
                        <div class="tab-pane fade show active" id="upcomingMeetings" role="tabpanel">
                            <div id="upcomingMeetingsList">
                                <div class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin text-muted"></i> Loading...
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pastMeetings" role="tabpanel">
                            <div id="pastMeetingsList">
                                <div class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin text-muted"></i> Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                                                        <!-- Calls Tab -->
                                                        <div class="tab-pane fade" id="calls" role="tabpanel">
                                                            <div class="mb-3">
                                                                @can('leads_addlogcall') 
                                                                    <button class="btn btn-sm btn-primary mr-2" onclick="openLogCallModal(${lead.id})">
                                                                        <i class="fas fa-phone-alt mr-2"></i> Log a Call
                                                                    </button>

                                                                @endcan

                                                                @can('leads_createcall')

                                                                    <button class="btn btn-sm btn-primary" onclick="openScheduleCallModal(${lead.id})">
                                                                        <i class="fas fa-calendar-plus mr-2"></i> Create a Call
                                                                    </button>
                                                                @endcan
                                                            </div>

                                                            <!-- Sub Tabs for Calls -->
                                                            <ul class="nav nav-tabs" id="callsSubTabs" role="tablist">
                                                                @can('leads_viewlogcall')
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="logged-calls-tab" data-toggle="tab" href="#loggedCalls" role="tab">
                                                                            <i class="fas fa-history"></i> Logged Calls
                                                                        </a>
                                                                    </li>

                                                                @endcan
                                                                @can('leads_schedulecalls')
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="scheduled-calls-tab" data-toggle="tab" href="#scheduledCalls" role="tab">
                                                                            <i class="fas fa-calendar"></i> Scheduled Calls
                                                                        </a>
                                                                    </li>
                                                                @endcan

                                                            </ul>

                                                            <div class="tab-content mt-3" id="callsSubTabsContent">
                                                                <!-- Logged Calls Sub Tab -->
                                                                <div class="tab-pane fade show active" id="loggedCalls" role="tabpanel">
                                                                    <div id="loggedCallsList">
                                                                        <div class="text-center py-3">
                                                                            <i class="fas fa-spinner fa-spin text-muted"></i> Loading...
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Scheduled Calls Sub Tab -->
                                                                <div class="tab-pane fade" id="scheduledCalls" role="tabpanel">
                                                                    <div id="scheduledCallsList">
                                                                        <div class="text-center py-3">
                                                                            <i class="fas fa-spinner fa-spin text-muted"></i> Loading...
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Notes Tab -->
                                                        <div class="tab-pane fade" id="notes" role="tabpanel">
                                                            <div id="notesList">
                                                                <div class="text-center py-3">
                                                                    <i class="fas fa-spinner fa-spin text-muted"></i> Loading...
                                                                </div>
                                                            </div>
                                                            @can('leads_addnote')
                                                                <button class="btn btn-sm btn-primary mt-2" onclick="openNoteAction(${lead.id}, '${escapeHtml(lead.first_name)} ${escapeHtml(lead.last_name)}')">
                                                                    <i class="fas fa-plus mr-2"></i> Add Note
                                                                </button>
                                                            @endcan
                                                        </div>

                                                        <!-- Tasks Tab -->
                                                        <div class="tab-pane fade" id="tasks" role="tabpanel">
                                                            <div id="tasksList">
                                                                <div class="text-center py-3">
                                                                    <i class="fas fa-spinner fa-spin text-muted"></i> Loading...
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-sm btn-primary mt-2" onclick="openTaskAction(${lead.id}, '${escapeHtml(lead.first_name)} ${escapeHtml(lead.last_name)}')">
                                                                <i class="fas fa-plus mr-2"></i> Add Task
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
            $('#viewLeadContent').html(html);
        }



        // Load Lead Calls
        function loadLeadCalls(leadId) {
            $.ajax({
                url: '{{ url("admin/leads") }}/' + leadId + '/calls',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        displayLoggedCalls(response.logged_calls);
                        displayScheduledCalls(response.scheduled_calls);
                    }
                }
            });
        }

        function loadLeadNotes(leadId) {
            $.ajax({
                url: '{{ url("admin/leads") }}/' + leadId + '/notes',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        displayLeadNotes(response.data);
                    } else {
                        $('#notesList').html('<div class="alert alert-warning">Unable to load notes.</div>');
                    }
                }
            });
        }

        function loadLeadTasks(leadId) {
            $.ajax({
                url: '{{ url("admin/leads") }}/' + leadId + '/tasks',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        displayLeadTasks(response.data);
                    } else {
                        $('#tasksList').html('<div class="alert alert-warning">Unable to load tasks.</div>');
                    }
                }
            });
        }

        function displayLeadNotes(notes) {
            if (!notes || notes.length === 0) {
                $('#notesList').html('<div class="alert alert-info">No notes found for this lead.</div>');
                return;
            }

            let html = '<div class="list-group">';
            notes.forEach(note => {
                let author = note.created_by_name || 'User';
                html += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong>${escapeHtml(author)}</strong>
                                    <small class="text-muted">${new Date(note.created_at).toLocaleString()}</small>
                                </div>
                                <div>${escapeHtml(note.note || '')}</div>
                            </div>
                        `;
            });
            html += '</div>';
            $('#notesList').html(html);
        }

        function displayLeadTasks(tasks) {
            if (!tasks || tasks.length === 0) {
                $('#tasksList').html('<div class="alert alert-info">No tasks found for this lead.</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead class="thead-light"><tr><th>Subject</th><th>Assigned To</th><th>Due Date</th><th>Status</th><th width="90">Actions</th></tr></thead><tbody>';

            tasks.forEach(task => {
                let assignedTo = task.assigned_to_name || '-';
                let canComplete = task.status !== 'completed';
                html += `
                            <tr>
                                <td>
                                    <strong>${escapeHtml(task.subject)}</strong>
                                    ${task.description ? '<br><small class="text-muted">' + escapeHtml(task.description) + '</small>' : ''}
                                </td>
                                <td>${escapeHtml(assignedTo)}</td>
                                <td>${task.due_date ? new Date(task.due_date).toLocaleString() : '-'}</td>
                                <td><span class="badge badge-${task.status === 'completed' ? 'success' : 'warning'}">${escapeHtml(task.status)}</span></td>
                                <td>
                                    ${canComplete ? '<button class="btn btn-sm btn-primary" onclick="updateLeadTaskStatus(' + task.id + ', \'completed\')" title="Mark Complete"><i class="fas fa-check"></i></button>' : ''}
                                </td>
                            </tr>
                        `;
            });

            html += '</tbody></table></div>';
            $('#tasksList').html(html);
        }

        // Display Logged Calls
        function displayLoggedCalls(calls) {
            loggedCallsCache = calls || [];

            if (calls.length === 0) {
                $('#loggedCallsList').html('<div class="alert alert-info">No logged calls found.</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead class="thead-light"><tr><th>Date</th><th>Type</th><th>Purpose</th><th>Duration</th><th>Status</th><th>Notes</th><th width="90">Actions</th></tr></thead><tbody>';

            calls.forEach(call => {
                let statusBadge = call.status == 'completed' ? 'success' : (call.status == 'missed' ? 'danger' : (call.status == 'voicemail' ? 'info' : 'warning'));
                html += `
                                                    <tr>
                                                        <td>${new Date(call.call_date).toLocaleString()}</td>
                                                        <td><span class="badge badge-${call.call_type == 'outbound' ? 'info' : 'warning'}">${call.call_type}</span></td>
                                                        <td>${escapeHtml(call.call_purpose || '-')}</td>
                                                        <td>${call.duration || '-'}</td>
                                                        <td><span class="badge badge-${statusBadge}">${call.status}</span></td>
                                                        <td><small>${escapeHtml(call.notes ? call.notes.substring(0, 50) : '-')}</small></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="editLoggedCall(${call.id})" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" onclick="deleteCall(${call.id})" title="Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `;
            });

            html += '</tbody></table></div>';
            $('#loggedCallsList').html(html);
        }

        // Display Scheduled Calls
        function displayScheduledCalls(calls) {
            scheduledCallsCache = calls || [];

            if (calls.length === 0) {
                $('#scheduledCallsList').html('<div class="alert alert-info">No scheduled calls found.</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead class="thead-light"><tr><th>Date</th><th>Type</th><th>Purpose</th><th>Notes</th><th width="150">Actions</th></tr></thead><tbody>';

            calls.forEach(call => {
                html += `
                                                    <tr>
                                                        <td>${new Date(call.call_date).toLocaleString()}</td>
                                                        <td><span class="badge badge-${call.call_type == 'outbound' ? 'info' : 'warning'}">${call.call_type}</span></td>
                                                        <td>${escapeHtml(call.call_purpose || '-')}</td>
                                                        <td><small>${escapeHtml(call.notes ? call.notes.substring(0, 50) : '-')}</small></td>
                                                        <td>
                                                            @can('leads_editscheduledcalls')
                                                                <button class="btn btn-sm btn-primary" onclick="editScheduledCall(${call.id})" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @endcan
                                                            @can('leads_markcompleteupcommingcall')
                                                                <button class="btn btn-sm btn-success" onclick="openCompleteScheduledCallModal(${call.id})" title="Mark Complete">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </button>
                                                            @endcan
                                                            @can('leads_deletecall')
                                                                <button class="btn btn-sm btn-danger" onclick="deleteCall(${call.id})" title="Delete">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                `;
            });

            html += '</tbody></table></div>';
            $('#scheduledCallsList').html(html);
        }

        // Open Log Call Modal
        function openLogCallModal(leadId) {
            $('#log_call_id').val('');
            $('#logCall_lead_id').val(leadId);
            $('#log_call_type').val('outbound');
            $('#log_call_purpose').val('');
            $('#log_call_status').val('completed');
            $('#log_call_duration').val('');
            $('#log_call_notes').val('');
            $('#log_call_date').val(formatDateTimeLocal(new Date().toISOString()));
            $('#logCallModal').modal('show');
        }

        // Open Schedule Call Modal
        function openScheduleCallModal(leadId) {
            $('#schedule_call_id').val('');
            $('#scheduleCall_lead_id').val(leadId);
            $('#schedule_call_type').val('outbound');
            $('#schedule_call_purpose').val('');
            $('#schedule_call_notes').val('');
            let now = new Date();
            now.setHours(now.getHours() + 1);
            let formattedDate = now.toISOString().slice(0, 16);
            $('#schedule_call_date').val(formattedDate);
            $('#scheduleCallModal').modal('show');
        }

        function editLoggedCall(callId) {
            let call = loggedCallsCache.find(item => item.id === callId);
            if (!call) return;

            $('#log_call_id').val(call.id);
            $('#logCall_lead_id').val(call.lead_id || currentLeadId);
            $('#log_call_type').val(call.call_type || 'outbound');
            $('#log_call_purpose').val(call.call_purpose || '');
            $('#log_call_status').val(call.status || 'completed');
            $('#log_call_duration').val(call.duration || '');
            $('#log_call_notes').val(call.notes || '');
            $('#log_call_date').val(formatDateTimeLocal(call.call_date));
            $('#logCallModal').modal('show');
        }

        function editScheduledCall(callId) {
            let call = scheduledCallsCache.find(item => item.id === callId);
            if (!call) return;

            $('#schedule_call_id').val(call.id);
            $('#scheduleCall_lead_id').val(call.lead_id || currentLeadId);
            $('#schedule_call_type').val(call.call_type || 'outbound');
            $('#schedule_call_purpose').val(call.call_purpose || '');
            $('#schedule_call_notes').val(call.notes || '');
            $('#schedule_call_date').val(formatDateTimeLocal(call.call_date));
            $('#scheduleCallModal').modal('show');
        }

        // Delete Call
        function deleteCall(callId) {
            Swal.fire({
                title: 'Delete Call?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/leads/calls") }}/' + callId,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false });
                                loadLeadCalls(currentLeadId);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                            }
                        }
                    });
                }
            });
        }

        function openCompleteScheduledCallModal(callId) {
            let call = scheduledCallsCache.find(item => item.id === callId);
            if (!call) return;

            $('#complete_call_id').val(call.id);
            $('#complete_call_duration').val(call.duration || '');
            $('#complete_call_status').val('completed');
            $('#complete_call_notes').val(call.notes || '');
            $('#completeCallModal').modal('show');
        }

        function updateLeadTaskStatus(taskId, status) {
            $.ajax({
                url: '{{ url("admin/tasks") }}/' + taskId + '/status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                        loadLeadTasks(currentLeadId);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                    }
                }
            });
        }

        // Note Actions
        function openNoteAction(id, name) {
            $('#note_lead_id').val(id);
            $('#note_lead_name').val(name);
            $('#note_content').val('');
            $('#noteModal').modal('show');
        }

        // Task Actions
        function openTaskAction(id, name) {
            $('#task_lead_id').val(id);
            $('#task_lead_name').val(name);
            $('#task_subject').val('');
            $('#task_description').val('');
            $('#task_due_date').val('');
            $('#task_assigned_to').val('');
            $('#taskModal').modal('show');
        }

        // Placeholder actions
        function openCallAction(id, name) {
            $('#logCall_lead_id').val(id);
            $('#log_call_type').val('outbound');
            $('#log_call_purpose').val('');
            $('#log_call_status').val('completed');
            $('#log_call_duration').val('');
            $('#log_call_notes').val('');
            // Set default call date to now
            let now = new Date();
            let formattedDate = now.toISOString().slice(0, 16);
            $('#log_call_date').val(formattedDate);
            $('#logCallModal').modal('show');
        }

        // Email Action
        function openEmailAction(id, leadName, leadEmail) {
            $('#email_lead_id').val(id);
            $('#emailLeadName').text(leadName);
            $('#email_to').val(leadEmail);
            $('#email_from').val('{{ Auth::user()->email }}');
            $('#email_subject').val('');
            $('#email_body').val('');
            $('#email_template').val('');
            $('#emailModal').modal('show');

            // Load templates
            loadEmailTemplates();
        }

        // Load Email Templates
        function loadEmailTemplates() {
            $.ajax({
                url: '{{ route("admin.email.templates") }}',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        let options = '<option value="">-- Select Template --</option>';
                        if (response.data.length > 0) {
                            response.data.forEach(function (template) {
                                let category = template.category ? ' (' + template.category + ')' : '';
                                options += '<option value="' + template.id + '">' + escapeHtml(template.name) + category + '</option>';
                            });
                        } else {
                            options += '<option disabled>No templates available</option>';
                        }
                        $('#email_template').html(options);
                    }
                }
            });
        }

        // Template Change Event
        $(document).on('change', '#email_template', function () {
            let templateId = $(this).val();
            if (templateId) {
                $.ajax({
                    url: '{{ url("admin/email-template") }}/' + templateId,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            $('#email_subject').val(response.data.subject);
                            $('#email_body').val(response.data.body);
                        }
                    }
                });
            }
        });

        // Email Form Submit
        $('#emailForm').on('submit', function (e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: '{{ route("admin.lead.sendEmail") }}',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#emailModal').modal('hide');
                        $('#emailForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function (xhr) {
                    let message = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                }
            });
        });

        // Template Form Submit
        $('#templateForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("admin.email.template.save") }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#templateModal').modal('hide');
                        $('#templateForm')[0].reset();
                        loadEmailTemplates(); // Reload templates
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                }
            });
        });

        // Open Template Modal
        function openTemplateModal() {
            let subject = $('#email_subject').val();
            let body = $('#email_body').val();
            $('#template_subject').val(subject);
            $('#template_body').val(body);
            $('#template_name').val('');
            $('#template_category').val('');
            $('#templateModal').modal('show');
        }

        // Escape HTML helper
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str).replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }


        // Toggle CC Field
        function toggleCc() {
            $('#ccRow').toggle();
        }

        // Toggle BCC Field
        function toggleBcc() {
            $('#bccRow').toggle();
        }

        // Simple Rich Text Formatting
        function formatText(command) {
            let textarea = document.getElementById('email_body');
            let start = textarea.selectionStart;
            let end = textarea.selectionEnd;
            let selectedText = textarea.value.substring(start, end);
            let replacement = '';

            switch (command) {
                case 'bold':
                    replacement = `**${selectedText}**`;
                    break;
                case 'italic':
                    replacement = `*${selectedText}*`;
                    break;
                case 'underline':
                    replacement = `_${selectedText}_`;
                    break;
            }

            textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start, start + replacement.length);
        }

        function openMeetingAction(id) {
            Swal.fire({ icon: 'info', title: 'Coming Soon!', text: 'Meeting scheduling feature will be added soon.' });
        }

        // Lead Modal Functions
        function openLeadModal() {
            $('#leadForm')[0].reset();
            $('#lead_id').val('');
            $('#leadModalLabel').html('<i class="fas fa-user-plus mr-2"></i>Create New Lead');
            $('#leadImagePreview').hide();
            $('#imagePlaceholder').show();
            $('#leadModal').modal('show');
        }

        function editLead(id) {
            $.ajax({
                url: '{{ url("admin/leads") }}/' + id,
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        let l = response.data;
                        $('#lead_id').val(l.id);
                        $('#lead_owner').val(l.lead_owner);
                        $('#first_name').val(l.first_name);
                        $('#last_name').val(l.last_name);
                        $('#title').val(l.title);
                        $('#phone').val(l.phone);
                        $('#mobile').val(l.mobile);
                        $('#lead_source').val(l.lead_source);
                        $('#annual_revenue').val(l.annual_revenue);
                        $('#company').val(l.company);
                        $('#email').val(l.email);
                        $('#website').val(l.website);
                        $('#lead_status').val(l.lead_status);
                        $('#no_of_employees').val(l.no_of_employees);
                        $('#street').val(l.street);
                        $('#city').val(l.city);
                        $('#state').val(l.state);
                        $('#country').val(l.country);
                        $('#zip_code').val(l.zip_code);
                        $('#description').val(l.description);

                        if (l.lead_image) {
                            $('#leadImagePreview').attr('src', '{{ asset("") }}' + l.lead_image).show();
                            $('#imagePlaceholder').hide();
                        } else {
                            $('#leadImagePreview').hide();
                            $('#imagePlaceholder').show();
                        }
                        $('#leadModalLabel').html('<i class="fas fa-user-edit mr-2"></i>Edit Lead');
                        $('#leadModal').modal('show');
                    }
                }
            });
        }

        function deleteLead(id) {
            Swal.fire({
                title: 'Delete Lead?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/leads") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                            }
                        }
                    });
                }
            });
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#leadImagePreview').attr('src', e.target.result).show();
                    $('#imagePlaceholder').hide();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function formatDateTimeLocal(dateString) {
            if (!dateString) return '';
            let date = new Date(dateString);
            let offset = date.getTimezoneOffset();
            let localDate = new Date(date.getTime() - (offset * 60000));
            return localDate.toISOString().slice(0, 16);
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str).replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
    </script>

    <style>
        .page-title {
            font-size: 1.35rem;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .card-statistics {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .icon-bg-primary {
            width: 55px;
            height: 55px;
            background: #35394F;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .btn-primary {
            background: #35394F;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            padding: 6px 16px;
            font-size: 13px;
        }

        .btn-primary:hover {
            background: #2a2e40;
        }

        .form-control-sm {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.8125rem;
            padding: 0.375rem 0.75rem;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .thead-light th {
            background-color: #f8fafc;
            font-weight: 600;
            font-size: 0.8rem;
            color: #475569;
        }

        .table td {
            font-size: 0.8125rem;
            vertical-align: middle;
            padding: 12px 8px;
        }

        .lead-row {
            cursor: pointer;
        }

        .lead-row:hover {
            background-color: #f8fafc;
        }

        .badge {
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .modal-header.bg-primary {
            background: #35394F !important;
        }

        .modal-content {
            border: none;
            border-radius: 10px;
        }

        .dropdown-toggle.hide-caret::after {
            display: none !important;
        }

        .dropdown-menu {
            border-radius: 10px;
            padding: 0.5rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            border: none;
        }

        .dropdown-item {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
        }

        .dropdown-divider {
            margin: 0.3rem 0;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu>.dropdown-item {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dropdown-submenu>.dropdown-item:hover,
        .dropdown-submenu.show>.dropdown-item {
            background-color: #f1f5f9;
        }

        .dropdown-submenu>.dropdown-submenu-menu {
            display: none;
            margin: 0.35rem 0 0.15rem 2.15rem;
            padding-left: 0;
            border-left: 2px solid #e2e8f0;
        }

        .dropdown-submenu.show>.dropdown-submenu-menu {
            display: block;
        }

        .dropdown-submenu-item {
            font-size: 0.78rem;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            margin-bottom: 0.15rem;
            white-space: nowrap;
        }

        .dropdown-submenu-item:last-child {
            margin-bottom: 0;
        }

        .submenu-arrow {
            font-size: 0.72rem;
            transition: transform 0.2s ease;
        }

        .dropdown-submenu.show .submenu-arrow {
            transform: rotate(180deg);
        }

        @media (max-width: 991.98px) {
            .dropdown-submenu>.dropdown-submenu-menu {
                margin-left: 1.5rem;
            }
        }

        .btn-outline-secondary {
            border-color: #e2e8f0;
        }

        .btn-outline-secondary:hover {
            background-color: #f1f5f9;
            border-color: #e2e8f0;
            color: #475569;
        }
    </style>
@endsection
