@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header d-md-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title text-dark">
                <i class="fas fa-calendar-alt text-primary mr-2"></i> Leave Types
            </h3>
            <p class="text-muted mb-0 font-size-13">Manage leave policies and entitlements via modal</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button class="btn btn-primary px-4 shadow-sm" onclick="openLeaveModal('add')">
                <i class="fa fa-plus mr-2"></i> Add Leave Type
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="leaveTable" class="table table-hover table-custom" style="width:100%">
                            <thead class="bg-light text-primary text-uppercase small font-weight-bold">
                                <tr>
                                    <th width="100">Actions</th>
                                    <th>Code</th>
                                    <th>Leave Name</th>
                                    <th>Days/Yr</th>
                                    <th>Paid</th>
                                    <th>Carry Forward</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveTypes as $type)
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-warning mr-1" onclick="openLeaveModal('edit', {{ $type->id }})">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteLeave({{ $type->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-light px-2 border">{{ $type->code }}</span></td>
                                    <td class="font-weight-bold">{{ $type->name }}</td>
                                    <td>{{ $type->days_per_year }} <span class="text-muted small">Days</span></td>
                                    <td>
                                        {!! $type->is_paid ? '<span class="text-success small font-weight-bold">● Paid</span>' : '<span class="text-muted small">○ Unpaid</span>' !!}
                                    </td>
                                    <td>{{ $type->carry_forward ? ($type->max_carry_forward ?? 'Unlimited') : 'No' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $type->is_active ? 'success' : 'secondary' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="leaveModalLabel">Add Leave Type</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="leaveForm">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="leave_id">
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold small">Leave Name *</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="e.g. Annual Leave" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold small">Code *</label>
                            <input type="text" class="form-control" name="code" id="code" placeholder="e.g. AL" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold small">Days Per Year *</label>
                            <input type="number" class="form-control" name="days_per_year" id="days_per_year" min="0" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold small">Max CF Limit</label>
                            <input type="number" class="form-control shadow-none" name="max_carry_forward" id="max_cf_input" placeholder="0">
                        </div>
                        <div class="col-md-4 form-group mt-md-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="carry_forward" id="carry_forward" value="1">
                                <label class="custom-control-label" for="carry_forward">Allow CF</label>
                            </div>
                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-4 mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="is_paid" id="is_paid" value="1" checked>
                                <label class="custom-control-label font-weight-bold" for="is_paid text-success">Paid Leave</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" checked>
                                <label class="custom-control-label font-weight-bold" for="is_active">Status Active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="font-weight-bold small text-muted">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5" id="saveLeaveBtn">Save Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#leaveTable').DataTable({
            responsive: true,
            "order": [[ 2, "asc" ]]
        });

        // AJAX Form Submit
        $('#leaveForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $('#saveLeaveBtn');
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('admin.leave-types.store') }}", // Ensure this route handles both Add/Update
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    Swal.fire('Success', 'Policy updated successfully', 'success').then(() => location.reload());
                },
                error: (err) => {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                    btn.prop('disabled', false).text('Save Policy');
                }
            });
        });

        $('#carry_forward').change(function() {
            $('#max_cf_input').prop('disabled', !$(this).is(':checked'));
        });
    });

    window.openLeaveModal = function (mode, id = null) {
        $('#leaveForm')[0].reset();
        $('#leave_id').val('');
        $('#leaveModalLabel').text(mode === 'add' ? 'Add New Leave Policy' : 'Edit Leave Policy');

        if (mode === 'edit') {
            $.get("{{ url('admin/leave-types') }}/" + id + "/edit", function (data) {
                $('#leave_id').val(data.id);
                $('#name').val(data.name);
                $('#code').val(data.code);
                $('#days_per_year').val(data.days_per_year);
                $('#description').val(data.description);
                $('#max_cf_input').val(data.max_carry_forward);
                
                $('#is_paid').prop('checked', data.is_paid == 1);
                $('#is_active').prop('checked', data.is_active == 1);
                $('#carry_forward').prop('checked', data.carry_forward == 1).trigger('change');
                
                $('#leaveModal').modal('show');
            });
        } else {
            $('#leaveModal').modal('show');
        }
    };

    window.deleteLeave = function (id) {
        Swal.fire({
            title: 'Delete Policy?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/leave-types') }}/" + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        location.reload();
                    }
                });
            }
        });
    };
</script>
@endsection