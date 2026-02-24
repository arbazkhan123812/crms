@extends('layout.app')

@section('content')

    <div class="content">
        <div class="page-header d-md-flex justify-content-between">
            <div class="mt-3 mt-md-0">
               
                
                <button class="btn btn-primary" onclick="openUserModal('add')">
                    <i class="fa fa-plus mr-2"></i> Add New Roles
                </button>
           
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="userTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>name</th>
                                        <th>guard name</th>
                                        <th>created at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $row)
                                        <tr>
                                            <td align="center">

                                                <button class="btn btn-sm btn-warning"
                                                    onclick="openUserModal('edit', <?= $row['id'] ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $row['id'] ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </button>

                                            </td>
                                            
                                            
                                            <td>{{ $row->name ?? 'No Role Assigned' }}</span></td>
                                            <td>{{ $row->guarded_name ?? 'No Role Assigned' }}</span></td>
                                            <td>{{ $row->created_at  ?? 'No timestamps' }}</span></td>
                                         
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

    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="userForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="role_id">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Role Name *</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Guarded Name *</label>
                                <input type="text" class="form-control" name="guarded_name" id="guarded_name" required>
                            </div>
                          

                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveUserBtn">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#userTable').DataTable();

            // Save User (Add/Edit)
            $('#userForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#saveUserBtn');
                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{route('role.save')}}",
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    complete: () => btn.prop('disabled', false).text('Save User')
                });
            });
        });
        window.openUserModal = function (mode, id = null) {
            $('#userForm')[0].reset();
            $('#user_id').val('');
            $('#pass_note').hide();
            $('#userModalLabel').text(mode === 'add' ? 'Add New User' : 'Edit User');

            if (mode === 'edit') {
                $('#pass_note').show();
                $.ajax({
                    url: "{{ route('role.get','') }}/" + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            $('#user_id').val(res.data.id);
                            $('#username').val(res.data.username);
                            $('#full_name').val(res.data.full_name);
                            $('#email').val(res.data.email);
                            $('#role_id').val(res.data.role_id).trigger('change');
                            $('#status').val(res.data.status);
                            $('#userModal').modal('show');
                        }
                    }
                });
            } else {
                $('#userModal').modal('show');
            }
        };

        window.deleteUser = function (id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "User will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{route('role.delete', '') }}/" + id,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        type: 'POST',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                            }
                        }
                    });
                }
            });
        };
    </script>
@endsection