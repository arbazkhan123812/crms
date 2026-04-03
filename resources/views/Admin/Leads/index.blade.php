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
                            <h4 class="card-title mb-0">{{ $leads->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
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
                            <th>Company</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                        <tr>
                            <td class="align-middle text-center">
                                @if($lead->lead_image)
                                    <img src="{{ asset($lead->lead_image) }}" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                @else
                                    <div class="avatar-initial rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px; background: linear-gradient(135deg, #35394F, #35394F); font-size: 12px;">
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
                                    <div><small><i class="fas fa-envelope text-muted mr-1"></i> {{ $lead->email }}</small></div>
                                @endif
                                @if($lead->phone)
                                    <div><small><i class="fas fa-phone text-muted mr-1"></i> {{ $lead->phone }}</small></div>
                                @endif
                            </td>
                            <td class="align-middle">{{ $lead->company ?: '-' }}</td>
                            <td class="align-middle">{{ $lead->owner?->full_name ?? ($lead->owner?->username ?? 'Not Assigned') }}</td>
                            <td class="align-middle">
                                @php
                                    $statusColors = ['New' => 'primary', 'Contacted' => 'info', 'Qualified' => 'success', 'Lost' => 'danger', 'Cancelled' => 'warning', 'Junk' => 'secondary'];
                                    $statusColor = $statusColors[$lead->lead_status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusColor }} px-2 py-1">{{ $lead->lead_status ?: '-' }}</span>
                            </td>
                            <td class="align-middle">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary" onclick="editLead({{ $lead->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @can('leads_delete')
                                    <button class="btn btn-sm btn-primary" onclick="deleteLead({{ $lead->id }})" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    @endcan
                                    <!-- Action Button for Notes -->
                                    <button class="btn btn-sm btn-primary" onclick="openNoteModal({{ $lead->id }}, '{{ addslashes($lead->first_name) }} {{ addslashes($lead->last_name) }}')" title="Add Note">
                                        <i class="fas fa-sticky-note"></i>
                                    </button>
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
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="leadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="lead_id">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Profile Image Section -->
                    <div class="card mb-2 border">
                        <div class="card-header bg-light py-1">
                            <h6 class="mb-0" style="font-size: 0.9rem; font-weight: 500;">
                                <i class="fas fa-image text-primary mr-2"></i>Profile Image
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <img id="leadImagePreview" src="" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover; display: none;">
                                    <div id="imagePlaceholder">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">
                                            <i class="fas fa-user fa-2x text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="custom-file" style="width: 250px;">
                                        <input type="file" name="lead_image" id="lead_image" class="custom-file-input" accept="image/*" onchange="previewImage(this)">
                                        <label class="custom-file-label form-control-sm mb-0" for="lead_image">Choose image</label>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">JPG, PNG or GIF (Max 2MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Information -->
                    <div class="card mb-2 border">
                        <div class="card-header bg-light py-1">
                            <h6 class="mb-0" style="font-size: 0.9rem; font-weight: 500;">
                                <i class="fas fa-info-circle text-primary mr-2"></i>Lead Information
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name" class="form-control form-control-sm" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Last Name</label>
                                        <input type="text" name="last_name" id="last_name" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Lead Owner <span class="text-danger">*</span></label>
                                        <select name="lead_owner" id="lead_owner" class="form-control form-control-sm" required>
                                            <option value="">Select Lead Owner</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->username ?? $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Email Address</label>
                                        <input type="email" name="email" id="email" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Phone</label>
                                        <input type="text" name="phone" id="phone" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Mobile</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Company Name</label>
                                        <input type="text" name="company" id="company" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Website</label>
                                        <input type="url" name="website" id="website" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Lead Source</label>
                                        <select name="lead_source" id="lead_source" class="form-control form-control-sm">
                                            <option value="">None</option>
                                            @foreach($leadSources as $source)
                                                @if($source)
                                                    <option value="{{ $source }}">{{ $source }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Lead Status</label>
                                        <select name="lead_status" id="lead_status" class="form-control form-control-sm">
                                            <option value="">Select Status</option>
                                            @foreach($leadStatuses as $status)
                                                @if($status)
                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Annual Revenue</label>
                                        <input type="number" name="annual_revenue" id="annual_revenue" class="form-control form-control-sm" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Employees</label>
                                        <input type="number" name="no_of_employees" id="no_of_employees" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="card mb-2 border">
                        <div class="card-header bg-light py-1">
                            <h6 class="mb-0" style="font-size: 0.9rem; font-weight: 500;">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>Address Information
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Street Address</label>
                                        <input type="text" name="street" id="street" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">City</label>
                                        <input type="text" name="city" id="city" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">State</label>
                                        <input type="text" name="state" id="state" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Country</label>
                                        <input type="text" name="country" id="country" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-0 small">Zip Code</label>
                                        <input type="text" name="zip_code" id="zip_code" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card mb-2 border">
                        <div class="card-header bg-light py-1">
                            <h6 class="mb-0" style="font-size: 0.9rem; font-weight: 500;">
                                <i class="fas fa-align-left text-primary mr-2"></i>Description
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <textarea name="description" id="description" rows="2" class="form-control form-control-sm" placeholder="Enter additional notes..."></textarea>
                            </div>
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

<!-- Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sticky-note mr-2"></i>Add Note
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="noteForm">
                @csrf
                <input type="hidden" name="lead_id" id="note_lead_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label mb-1">Lead Name</label>
                        <input type="text" id="lead_name_display" class="form-control form-control-sm" readonly disabled>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label mb-1">Note</label>
                        <textarea name="note" id="note_content" rows="5" class="form-control form-control-sm" required placeholder="Write your note here..."></textarea>
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

<script>
let currentLeadId = null;

$(document).ready(function() {
    

    $('#leadForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.lead.save") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong!' });
            }
        });
    });

    $('#noteForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.lead.addNote") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false });
                    $('#noteModal').modal('hide');
                    $('#noteForm')[0].reset();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                }
            }
        });
    });
});

function openLeadModal() {
    $('#leadForm')[0].reset();
    $('#lead_id').val('');
    $('#leadModalLabel').html('<i class="fas fa-user-plus mr-2"></i>Create New Lead');
    $('#leadImagePreview').hide();
    $('#imagePlaceholder').show();
    $('.custom-file-label').text('Choose image');
    $('#leadModal').modal('show');
}

function editLead(id) {
    $.ajax({
        url: '{{ url("admin/leads") }}/' + id,
        type: 'GET',
        success: function(response) {
            if(response.success) {
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
                
                if(l.lead_image) {
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
        if(result.isConfirmed) {
            $.ajax({
                url: '{{ url("admin/leads") }}/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false }).then(() => { location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                    }
                }
            });
        }
    });
}

function openNoteModal(id, leadName) {
    $('#note_lead_id').val(id);
    $('#lead_name_display').val(leadName);
    $('#note_content').val('');
    $('#note_type').val('internal');
    $('#noteModal').modal('show');
}
function previewImage(input) {
    if(input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#leadImagePreview').attr('src', e.target.result).show();
            $('#imagePlaceholder').hide();
        }
        reader.readAsDataURL(input.files[0]);
        let fileName = input.files[0].name;
        $(input).next('.custom-file-label').text(fileName);
    }
}
</script>

<style>
.page-title { font-size: 1.35rem; font-weight: 500; margin-bottom: 5px; }
.card-statistics { border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.2s ease; }
.card-statistics:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.icon-bg-primary { width: 55px; height: 55px; background: #35394F; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
.btn-primary { background: #35394F; border: none; border-radius: 6px; font-weight: 500; padding: 6px 16px; font-size: 13px; }
.btn-primary:hover { background: #2a2e40; }
.btn-outline-primary { border-color: #35394F; color: #35394F; }
.btn-outline-primary:hover { background: #35394F; border-color: #35394F; }
.form-control-sm { border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8125rem; padding: 0.375rem 0.75rem; }
.form-control-sm:focus { border-color: #35394F; box-shadow: 0 0 0 3px rgba(53,57,79,0.1); }
.card { border: none; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.thead-light th { background-color: #f8fafc; border-top: none; font-weight: 600; font-size: 0.8rem; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
.table td { font-size: 0.8125rem; vertical-align: middle; padding: 12px 8px; }
.table tbody tr:hover { background-color: #f8fafc; }
.badge { font-weight: 500; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; }
.modal-header.bg-primary { background: #35394F !important; border-radius: 10px 10px 0 0; }
.modal-content { border: none; border-radius: 10px; }
.modal-body { padding: 1.25rem; }
.btn-group .btn { padding: 4px 10px; font-size: 0.75rem; }
.avatar-initial { font-weight: 600; font-size: 0.85rem; }
.custom-file-label { border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8125rem; padding: 0.375rem 0.75rem; }
.custom-file-label::after { background: #35394F; color: white; border-radius: 0 6px 6px 0; content: "Browse"; font-size: 0.75rem; padding: 0.375rem 0.75rem; }
@media (max-width: 768px) {
    .content { padding: 15px; }
    .page-header { flex-direction: column; }
    .col-auto { margin-top: 12px; width: 100%; }
    .btn-primary { width: 100%; }
    .btn-group { flex-wrap: wrap; margin-bottom: 10px; }
}
</style>
@endsection