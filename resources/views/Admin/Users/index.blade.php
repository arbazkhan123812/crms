@extends('layout.app')

@section('content')

    <div class="content">
        <div class="page-header d-md-flex justify-content-between">
            <div class="mt-3 mt-md-0">
                @can('users.create') 
                
                <button class="btn btn-primary" onclick="openUserModal('add')">
                    <i class="fa fa-plus mr-2"></i> Add New Admins
                </button>
                @endcan
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
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $row)
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
                                            <td><?= $row['username'] ?></td>
                                            <td><?= $row['email'] ?></td>
                                            <td>{{ $row->roles->first()?->name ?? 'No Role Assigned' }}</span></td>
                                            <td>
                                                <?= $row['status'] == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>' ?>
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
@if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
                    <div class="modal-body">
                        <input type="hidden" name="id" id="user_id">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Full Name *</label>
                                <input type="text" class="form-control" name="full_name" id="full_name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Username *</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email *</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Password <small id="pass_note">(Leave blank to keep current)</small></label>
                                <input type="password" class="form-control" name="password" id="password">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Role *</label>
                                <select class="form-control select2" name="role_id" id="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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
                    url: "{{route('user.save')}}",
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
                    url: "{{ route('user.get','') }}/" + id,
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
                        url: "{{route('user.delete', '') }}/" + id,
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