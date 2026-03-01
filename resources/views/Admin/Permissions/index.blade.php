@extends('layout.app') {{-- Adjust this to match your layout --}}

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header mb-3">
                    <h4 class="page-title mb-0">Permission Management</h4>
                    <p class="text-muted mb-0">Manage role and user permissions</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Selection Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Select Target</h5>
                    </div>
                    <div class="card-body">
                       
                        <div class="form-group mb-4">
                            <label class="form-label">Role</label>
                            <select class="form-control" id="role_select">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Manage permissions for a role</small>
                        </div>

                        <div class="form-group mb-4" id="user_selection_container" style="">
                            <label class="form-label font-weight-bold">User</label>
                            <select class="form-control" id="user_select">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-role="{{ $user->roles->first()->id ?? '' }}">
                                        {{ $user->username ?? $user->name ?? $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Manage direct permissions for this user</small>
                        </div>

                        <!-- Selected Info -->
                        <div id="selected_info" class="alert alert-light border" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-1" id="selected_name"></h6>
                                    <p class="mb-0 text-muted small">
                                        <span id="selected_type"></span>
                                        <span id="role_info" class="ml-2"></span>
                                    </p>
                                    <input type="hidden" id="selected_id">
                                    <input type="hidden" id="selected_type_value">
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div id="quick_actions" style="display: none;">
                            <hr>
                            <h6 class="mb-3">Quick Actions</h6>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <button type="button" class="btn btn-primary btn-block btn-sm" id="btn_select_all">
                                        Select All
                                    </button>
                                </div>
                                <div class="col-6 mb-2">
                                    <button type="button" class="btn btn-secondary btn-sm " id="btn_deselect_all">
                                       Deselect All
                                    </button>
                                </div>
                                <div class="col-12 mb-2" id="copy_role_btn_container" style="display: none;">
                                    <button type="button" class="btn btn-info btn-block btn-sm" id="btn_copy_role">
                                        <i class="fas fa-copy mr-1"></i> Copy From Role
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div id="permission_summary" class="mt-4">
                            <h6 class="mb-3">Summary</h6>
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-lock fa-3x text-muted"></i>
                                </div>
                                <p class="text-muted mb-0">Select a role or user to view permissions summary</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Panel -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" id="permissions_title">Permissions</h5>
                        <div id="save_button" style="display: none;">
                            <button type="button" class="btn btn-primary btn-sm" id="btn_save_permissions">
                                <i class="fas fa-save mr-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Legend for user permissions -->
                        <div id="legend_container" class="alert alert-info mb-3" style="display: none;">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-primary mr-2">User Direct</span>
                                <span class="text-muted mr-3">- Directly assigned to user</span>
                                <span class="badge badge-secondary mr-2">From Role</span>
                                <span class="text-muted">- Inherited from role (read-only)</span>
                            </div>
                        </div>

                        <div id="permissions_container">
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-user-shield fa-3x text-light"></i>
                                </div>
                                <h5 class="text-muted">No Selection Made</h5>
                                <p class="text-muted">Please select a role or user from the left panel</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Role Modal -->
<div class="modal fade" id="copyRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Copy Role Permissions</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Select role to copy permissions from:</p>
                <select class="form-control" id="copy_from_role">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn_confirm_copy">Copy</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        let currentType = null;
        let currentId = null;
        let selectedPermissions = [];
        let rolePermissions = [];
        let moduleOperations = {};
        let userRoleId = null;

        // Build module operations map
        @foreach($modules as $module)
            @if($module->operations->count() > 0)
                moduleOperations[{{ $module->id }}] = [
                    @foreach($module->operations as $op)
                        {{ $op->id }},
                    @endforeach
                ];
            @endif
        @endforeach

        // Get URL parameters
        function getUrlParams() {
            const params = new URLSearchParams(window.location.search);
            const type = params.get('type');
            const id = params.get('id');
            return { type, id };
        }

        // Auto-select based on URL parameters
        function autoSelectFromUrl() {
            const { type, id } = getUrlParams();

            if (type && id) {
                if (type === 'user') {
                    $('#user_select').val(id);
                    userRoleId = $('#user_select option:selected').data('role');
                } else if (type === 'role') {
                    $('#role_select').val(id);
                }

                if (type && id) {
                    loadPermissions(type, id);
                }
            }
        }

        // Role select change
        $('#role_select').change(function() {
            $('#user_select').val('');
            const roleId = $(this).val();
            if (roleId) {
                updateUrlParams('role', roleId);
                loadPermissions('role', roleId);
            } else {
                resetView();
                clearUrlParams();
            }
        });

        // User select change
        $('#user_select').change(function() {
            $('#role_select').val('');
            const userId = $(this).val();
            if (userId) {
                userRoleId = $(this).find(':selected').data('role');
                updateUrlParams('user', userId);
                loadPermissions('user', userId);
            } else {
                resetView();
                clearUrlParams();
            }
        });

        // Update URL parameters
        function updateUrlParams(type, id) {
            const params = new URLSearchParams(window.location.search);
            params.set('type', type);
            params.set('id', id);
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({ path: newUrl }, '', newUrl);
        }

        // Clear URL parameters
        function clearUrlParams() {
            const newUrl = window.location.pathname;
            window.history.pushState({ path: newUrl }, '', newUrl);
        }

        // Load permissions
        function loadPermissions(type, id) {
            currentType = type;
            currentId = id;

            $.ajax({
                url: '{{ route("admin.permissions.get") }}',
                type: 'POST',
                data: {
                    type: type,
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#permissions_container').html(`
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Loading permissions...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    if (response.success) {
                        $('#selected_name').text(response.name);
                        $('#selected_type').text(type === 'role' ? 'Role Permissions' : 'User Direct Permissions');
                        $('#selected_id').val(id);
                        $('#selected_type_value').val(type);
                        $('#selected_info').show();
                        $('#quick_actions').show();
                        $('#save_button').show();
                        $('#permissions_title').text(type === 'role' ? 'Role Permissions' : 'User Permissions');

                        // Store permissions
                        selectedPermissions = response.permissions || [];
                        rolePermissions = response.role_permissions || [];

                        // Show/hide legend and copy button
                        if (type === 'user') {
                            $('#legend_container').show();
                            $('#copy_role_btn_container').show();
                            
                            // Get role name if available
                            if (response.role_id) {
                                $.ajax({
                                    url: '{{ route("admin.permissions.role-name", "") }}/' + response.role_id,
                                    type: 'GET',
                                    success: function(roleRes) {
                                        $('#role_info').html(`<i class="fas fa-users mr-1"></i>Role: ${roleRes.name}`);
                                    }
                                });
                            }
                        } else {
                            $('#legend_container').hide();
                            $('#copy_role_btn_container').hide();
                            $('#role_info').empty();
                        }

                        renderPermissions();
                        updateSummary();
                    } else {
                        alert('Error loading permissions: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while loading permissions.');
                    console.error(xhr);
                }
            });
        }

        // Render permissions
        function renderPermissions() {
            let html = '';

            @foreach($modules as $module)
                var moduleId = {{ $module->id }};
                var moduleName = "{{ $module->name }}";
                var operations = moduleOperations[moduleId] || [];

                if (operations.length > 0) {
                    html += `
                        <div class="card mb-2 border">
                            <div class="card-header py-2">
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" 
                                               class="custom-control-input module-checkbox" 
                                               id="module_${moduleId}"
                                               data-module-id="${moduleId}"
                                               ${currentType === 'user' ? 'disabled' : ''}>
                                        <label class="custom-control-label font-weight-bold" for="module_${moduleId}">
                                            ${moduleName}
                                        </label>
                                    </div>
                                    <span class="badge badge-light ml-auto">${operations.length}</span>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary text-dark ml-2 toggle-operations" 
                                            data-module-id="${moduleId}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-3 operations-container" id="operations_${moduleId}" style="display: none;">
                                <div class="row">
                    `;

                    @foreach($module->operations as $op)
                        var opId = {{ $op->id }};
                        var opName = "{{ $op->display_name }}";
                        var permKey = moduleId + '_' + opId;
                        var isUserPermission = selectedPermissions.includes(permKey);
                        var isRolePermission = rolePermissions.includes(permKey);

                        var labelClass = '';
                        var badge = '';
                        var disabledAttr = '';
                        var checkedAttr = '';

                        if (currentType === 'user') {
                            if (isUserPermission) {
                                labelClass = 'text-primary font-weight-bold';
                                badge = '<span class="badge badge-primary badge-pill ml-2">Direct</span>';
                                checkedAttr = 'checked';
                            } else if (isRolePermission) {
                                labelClass = 'text-muted';
                                badge = '<span class="badge badge-secondary badge-pill ml-2">From Role</span>';
                                disabledAttr = 'disabled';
                            } else {
                                labelClass = '';
                                badge = '';
                            }
                        } else {
                            checkedAttr = isUserPermission ? 'checked' : '';
                        }

                        html += `
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input operation-checkbox" 
                                           id="operation_${opId}"
                                           value="${permKey}"
                                           data-module-id="${moduleId}"
                                           ${checkedAttr}
                                           ${disabledAttr}>
                                    <label class="custom-control-label ${labelClass}" for="operation_${opId}">
                                        ${opName}
                                        ${badge}
                                    </label>
                                </div>
                            </div>
                        `;
                    @endforeach

                    html += `
                                </div>
                            </div>
                        </div>
                    `;
                }
            @endforeach

            $('#permissions_container').html(html || '<div class="alert alert-info">No permissions available</div>');
            attachEventHandlers();
        }

        // Attach event handlers
        function attachEventHandlers() {
            $('.toggle-operations').click(function() {
                const moduleId = $(this).data('module-id');
                const operationsDiv = $(`#operations_${moduleId}`);
                const icon = $(this).find('i');

                operationsDiv.slideToggle();
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });

            $('.module-checkbox:not(:disabled)').change(function() {
                const moduleId = $(this).data('module-id');
                const isChecked = $(this).prop('checked');

                $(`#operations_${moduleId} .operation-checkbox:not(:disabled)`).prop('checked', isChecked);
                updateSelectedPermissions();
            });

            $('.operation-checkbox:not(:disabled)').change(function() {
                updateModuleCheckbox($(this).data('module-id'));
                updateSelectedPermissions();
            });
        }

        // Update module checkbox state
        function updateModuleCheckbox(moduleId) {
            if (currentType === 'user') return;

            const moduleCheckbox = $(`#module_${moduleId}`);
            const operations = $(`#operations_${moduleId} .operation-checkbox:not(:disabled)`);
            const checkedCount = operations.filter(':checked').length;
            const totalCount = operations.length;

            if (checkedCount === 0) {
                moduleCheckbox.prop('checked', false);
                moduleCheckbox.prop('indeterminate', false);
            } else if (checkedCount === totalCount) {
                moduleCheckbox.prop('checked', true);
                moduleCheckbox.prop('indeterminate', false);
            } else {
                moduleCheckbox.prop('checked', false);
                moduleCheckbox.prop('indeterminate', true);
            }
        }

        // Update selected permissions array
        function updateSelectedPermissions() {
            selectedPermissions = [];
            $('.operation-checkbox:checked:not(:disabled)').each(function() {
                selectedPermissions.push($(this).val());
            });
            updateSummary();
        }

        // Update summary section
        function updateSummary() {
            const totalModules = $('.card.mb-2.border').length;
            let totalOperations = 0;
            let userDirectCount = 0;
            let roleBasedCount = 0;

            $('.card.mb-2.border').each(function() {
                const moduleId = $(this).find('.module-checkbox').data('module-id');
                totalOperations += (moduleOperations[moduleId] || []).length;
            });

            if (currentType === 'user') {
                userDirectCount = selectedPermissions.length;
                roleBasedCount = rolePermissions.length;
                const totalEffective = userDirectCount + roleBasedCount;
                const percentage = totalOperations > 0 ? Math.round((totalEffective / totalOperations) * 100) : 0;
                const directPercentage = totalOperations > 0 ? Math.round((userDirectCount / totalOperations) * 100) : 0;
                const rolePercentage = totalOperations > 0 ? Math.round((roleBasedCount / totalOperations) * 100) : 0;

                let html = `
                    <h6 class="mb-3">Summary</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>${totalEffective} of ${totalOperations} permissions</small>
                            <small>${percentage}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: ${directPercentage}%"></div>
                            <div class="progress-bar bg-secondary" style="width: ${rolePercentage}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-primary">${userDirectCount} User Direct</small>
                            <small class="text-secondary">${roleBasedCount} From Role</small>
                        </div>
                    </div>
                `;

                $('#permission_summary').html(html);
            } else {
                const selectedCount = selectedPermissions.length;
                const percentage = totalOperations > 0 ? Math.round((selectedCount / totalOperations) * 100) : 0;

                let html = `
                    <h6 class="mb-3">Summary</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>${selectedCount} of ${totalOperations} permissions</small>
                            <small>${percentage}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded bg-light">
                                <div class="h5 mb-1">${totalModules}</div>
                                <small class="text-muted">Modules</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded bg-light">
                                <div class="h5 mb-1">${selectedCount}</div>
                                <small class="text-muted">Selected</small>
                            </div>
                        </div>
                    </div>
                `;

                $('#permission_summary').html(html);
            }
        }

        // Select all
        $('#btn_select_all').click(function() {
            if (currentType === 'user') {
                $('.operation-checkbox:not(:disabled)').prop('checked', true);
            } else {
                $('.operation-checkbox').prop('checked', true);
                $('.module-checkbox').prop('checked', true).prop('indeterminate', false);
            }
            updateSelectedPermissions();
        });

        // Deselect all
        $('#btn_deselect_all').click(function() {
            if (currentType === 'user') {
                $('.operation-checkbox:not(:disabled)').prop('checked', false);
            } else {
                $('.operation-checkbox').prop('checked', false);
                $('.module-checkbox').prop('checked', false).prop('indeterminate', false);
            }
            updateSelectedPermissions();
        });

        // Show copy role modal
        $('#btn_copy_role').click(function() {
            if (currentType !== 'user') {
                alert('Please select a user first');
                return;
            }
            $('#copyRoleModal').modal('show');
        });

        // Confirm copy role
        $('#btn_confirm_copy').click(function() {
            const fromRoleId = $('#copy_from_role').val();
            if (!fromRoleId) {
                alert('Please select a role to copy from');
                return;
            }

            $.ajax({
                url: '{{ route("admin.permissions.copy-role-to-user") }}',
                type: 'POST',
                data: {
                    user_id: currentId,
                    role_id: fromRoleId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#copyRoleModal').modal('hide');
                        loadPermissions('user', currentId);
                        toastr?.success?.(response.message) || alert(response.message);
                    } else {
                        toastr?.error?.(response.message) || alert('Error copying permissions: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while copying permissions.');
                }
            });
        });

        // Save permissions
        $('#btn_save_permissions').click(function() {
            if (!currentId || !currentType) {
                alert('Please select a role or user first');
                return;
            }

            const permissionsToSave = currentType === 'user' ?
                $('.operation-checkbox:checked:not(:disabled)').map(function() {
                    return $(this).val();
                }).get() :
                selectedPermissions;

            $.ajax({
                url: '{{ route("admin.permissions.save") }}',
                type: 'POST',
                data: {
                    type: currentType,
                    id: currentId,
                    permissions: permissionsToSave,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#btn_save_permissions').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving');
                },
                success: function(response) {
                    if (response.success) {
                        toastr?.success?.(response.message) || alert(response.message);
                        loadPermissions(currentType, currentId);
                    } else {
                        toastr?.error?.(response.message) || alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while saving permissions.');
                },
                complete: function() {
                    $('#btn_save_permissions').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Changes');
                }
            });
        });

        // Reset view
        function resetView() {
            currentType = null;
            currentId = null;
            selectedPermissions = [];
            rolePermissions = [];

            $('#selected_info').hide();
            $('#quick_actions').hide();
            $('#save_button').hide();
            $('#legend_container').hide();
            $('#copy_role_btn_container').hide();
            $('#permissions_title').text('Permissions');
            $('#permissions_container').html(`
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-user-shield fa-3x text-light"></i>
                    </div>
                    <h5 class="text-muted">No Selection Made</h5>
                    <p class="text-muted">Please select a role or user from the left panel</p>
                </div>
            `);
            $('#permission_summary').html(`
                <h6 class="mb-3">Summary</h6>
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-lock fa-3x text-muted"></i>
                    </div>
                    <p class="text-muted mb-0">Select a role or user to view permissions summary</p>
                </div>
            `);
        }

        // Initialize
        autoSelectFromUrl();
    });
</script>

@endsection


