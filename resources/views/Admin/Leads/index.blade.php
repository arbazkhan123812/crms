@extends('layout.admin')

@section('content')
    <div class="content">
        <!-- PAGE HEADER -->
        <div class="page-header mb-4">
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
                                            <tr>
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
                                                    @can('leads_edit')
                                                        <button class="btn btn-sm btn-primary" onclick="editLead({{ $lead->id }})" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endcan
                                                    @can('leads_delete')
                                                        <button class="btn btn-sm btn-primary" onclick="deleteLead({{ $lead->id }})"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endcan
                                                    <!-- Dropdown Action Menu -->
                                                    <div class="dropdown">
                                                        @can('leads_performactions')
                                                            <button class="btn btn-sm btn-primary dropdown-toggle hide-caret" type="button"
                                                                data-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                        @endcan
                                                        <ul class="dropdown-menu dropdown-menu-right shadow-sm" style="min-width: 180px;">
                                                            @can('leads_details')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="viewLead({{ $lead->id }})">
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
                                                            @can('leads_logcall')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openCallAction({{ $lead->id }}, '{{ addslashes($lead->first_name) }} {{ addslashes($lead->last_name) }}')">
                                                                        <i class="fas fa-phone-alt text-primary mr-2" style="width: 20px;"></i>
                                                                        Log Call
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('leads_sendemail')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openEmailAction({{ $lead->id }})">
                                                                        <i class="fas fa-envelope text-primary mr-2" style="width: 20px;"></i>
                                                                        Send Email
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('leads_schedulemeeting')
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                                                        onclick="openMeetingAction({{ $lead->id }})">
                                                                        <i class="fas fa-handshake text-primary mr-2" style="width: 20px;"></i>
                                                                        Schedule Meeting
                                                                    </a>
                                                                </li>
                                                            @endcan


                                                        </ul>
                                                    </div>
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

    <!-- Lead Modal (Create/Edit) -->
    <div class="modal fade" id="leadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="leadModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Create New Lead
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
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

    <!-- Call Modal -->
    <div class="modal fade" id="callModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-phone-alt mr-2"></i>Log Call
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="callForm">
                    @csrf
                    <input type="hidden" name="lead_id" id="call_lead_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="small mb-1">Lead Name</label>
                            <input type="text" id="call_lead_name" class="form-control form-control-sm" readonly disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Call Type <span class="text-danger">*</span></label>
                            <select name="call_type" id="call_type" class="form-control form-control-sm" required>
                                <option value="outbound"> Outbound (You called)</option>
                                <option value="inbound"> Inbound (Lead called)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Call Purpose</label>
                            <select name="call_purpose" id="call_purpose" class="form-control form-control-sm">
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
                        <div class="form-group mb-3">
                            <label class="small mb-1">Call Status <span class="text-danger">*</span></label>
                            <select name="status" id="call_status" class="form-control form-control-sm" required>
                                <option value="completed"> Completed - Successfully talked</option>
                                <option value="missed">Missed - No answer</option>
                                <option value="voicemail"> Voicemail - Left message</option>
                                <option value="no_answer"> No Answer - Call back later</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Duration (minutes:seconds)</label>
                            <input type="text" name="duration" id="call_duration" class="form-control form-control-sm"
                                placeholder="e.g., 5:30">
                        </div>
                        <div class="form-group mb-3">
                            <label class="small mb-1">Call Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="call_date" id="call_date"
                                class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small mb-1">Call Notes</label>
                            <textarea name="notes" id="call_notes" rows="3" class="form-control form-control-sm"
                                placeholder="What was discussed? Any action items?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Log Call</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        let currentLeadId = null;
        let currentLeadName = null;

        $(document).ready(function () {


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
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    }
                });
            });
          

            // Call Form Submit
            $('#callForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("admin.lead.addCall") }}',
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
                            $('#callModal').modal('hide');
                            $('#callForm')[0].reset();
                            // Optionally reload page or update call count
                            // location.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong!' });
                    }
                });
            });
        });

        
        // View Lead Details
        function viewLead(id) {
            $('#viewLeadModal').modal('show');
            $('#viewLeadContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2">Loading...</p></div>');

            $.ajax({
                url: '{{ url("admin/leads") }}/' + id,
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        let l = response.data;
                        let html = `
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        ${l.lead_image ?
                                `<img src="{{ asset('') }}${l.lead_image}" class="rounded-circle mb-3" width="100" height="100" style="object-fit: cover;">` :
                                `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-3" style="width: 100px; height: 100px;">
                                                <i class="fas fa-user fa-3x"></i>
                                            </div>`
                            }
                                        <h5>${escapeHtml(l.first_name)} ${escapeHtml(l.last_name)}</h5>
                                        <p class="text-muted small">${escapeHtml(l.title || 'No Title')}</p>
                                    </div>
                                    <div class="col-md-8">
                                        <table class="table table-sm table-borderless">
                                            <tr><th width="35%">Email:</th><td>${escapeHtml(l.email || '-')}</td></tr>
                                            <tr><th>Phone:</th><td>${escapeHtml(l.phone || '-')}</td></tr>
                                            <tr><th>Mobile:</th><td>${escapeHtml(l.mobile || '-')}</td></tr>
                                            <tr><th>Company:</th><td>${escapeHtml(l.company || '-')}</td></tr>
                                            <tr><th>Lead Status:</th><td>${escapeHtml(l.lead_status || '-')}</td></tr>
                                            <tr><th>Lead Source:</th><td>${escapeHtml(l.lead_source || '-')}</td></tr>
                                            <tr><th>Annual Revenue:</th><td>${l.annual_revenue ? 'PKR ' + parseFloat(l.annual_revenue).toLocaleString() : '-'}</td></tr>
                                            <tr><th>Owner:</th><td>${escapeHtml(l.owner?.full_name || l.owner?.username || 'Not Assigned')}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary"><i class="fas fa-map-marker-alt mr-2"></i>Address</h6>
                                        <p>${escapeHtml(l.street || '')}${l.street ? '<br>' : ''}
                                        ${escapeHtml(l.city || '')}${l.city ? ', ' : ''}
                                        ${escapeHtml(l.state || '')}${l.state ? '<br>' : ''}
                                        ${escapeHtml(l.country || '')}${l.zip_code ? ' - ' + escapeHtml(l.zip_code) : ''}</p>
                                        ${!l.street && !l.city && !l.state && !l.country ? '<p class="text-muted">No address provided</p>' : ''}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary"><i class="fas fa-align-left mr-2"></i>Description</h6>
                                        <p>${escapeHtml(l.description || 'No description provided')}</p>
                                    </div>
                                </div>
                            `;
                        $('#viewLeadContent').html(html);
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
    $('#call_lead_id').val(id);
    $('#call_lead_name').val(name);
    $('#call_type').val('outbound');
    $('#call_purpose').val('');
    $('#call_status').val('completed');
    $('#call_duration').val('');
    $('#call_notes').val('');
    // Set default call date to now
    let now = new Date();
    let formattedDate = now.toISOString().slice(0, 16);
    $('#call_date').val(formattedDate);
    $('#callModal').modal('show');
}

        function openEmailAction(id) {
            Swal.fire({ icon: 'info', title: 'Coming Soon!', text: 'Email feature will be added soon.' });
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

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function (m) {
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