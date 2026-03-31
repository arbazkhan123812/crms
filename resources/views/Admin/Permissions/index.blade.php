@extends('layout.admin')

@section('content')
<style>
    .custom-checkbox .custom-control-input:disabled:checked~.custom-control-label::before {
    background-color: rgb(0 0 0 / 50%) !important;
}

</style>
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

                        <div class="form-group mb-4" id="user_selection_container">
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
                                    <button type="button" class="btn btn-secondary btn-sm" id="btn_deselect_all">
                                       Deselect All
                                    </button>
                                </div>
                                
                            </div>
                        </div>

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
    let allPermissions = [];
    let userRoleId = null;

    function loadAllPermissions() {
        $.ajax({
            url: '{{ route("admin.permissions.all") }}',
            type: 'GET',
            success: function(response) {
                allPermissions = response;
                console.log('All permissions loaded:', allPermissions);
            },
            error: function(xhr) {
                console.error('Error loading permissions:', xhr);
            }
        });
    }

    function groupPermissionsByModule(permissions) {
        let groups = {};
        
        permissions.forEach(permission => {
            let parts = permission.name.split('_');
            let moduleName = parts.length > 1 ? parts[0].charAt(0).toUpperCase() + parts[0].slice(1) : 'General';
            
            if (!groups[moduleName]) {
                groups[moduleName] = [];
            }
            
            groups[moduleName].push({
                name: permission.name,
                display_name: permission.name.split('_').slice(1).join(' ').toUpperCase() || permission.name.toUpperCase(),
                full_name: permission.name
            });
        });
        
        return groups;
    }

    function getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        const type = params.get('type');
        const id = params.get('id');
        return { type, id };
    }

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

    function updateUrlParams(type, id) {
        const params = new URLSearchParams(window.location.search);
        params.set('type', type);
        params.set('id', id);
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({ path: newUrl }, '', newUrl);
    }

    function clearUrlParams() {
        const newUrl = window.location.pathname;
        window.history.pushState({ path: newUrl }, '', newUrl);
    }

    function loadPermissions(type, id) {
        currentType = type;
        currentId = id;
        selectedPermissions = [];
        rolePermissions = [];

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
                console.log('Permissions response:', response); // Debug log
                
                if (response.success) {
                    $('#selected_name').text(response.name);
                    $('#selected_type').text(type === 'role' ? 'Role Permissions' : 'User Direct Permissions');
                    $('#selected_id').val(id);
                    $('#selected_type_value').val(type);
                    $('#selected_info').show();
                    $('#quick_actions').show();
                    $('#save_button').show();
                    $('#permissions_title').text(type === 'role' ? 'Role Permissions' : 'User Permissions');

                    selectedPermissions = response.permissions || [];
                    rolePermissions = response.role_permissions || [];
                    
                    console.log('Selected permissions:', selectedPermissions);
                    console.log('Role permissions:', rolePermissions);

                    if (type === 'user') {
                        $('#legend_container').show();
                        $('#copy_role_btn_container').show();

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

  function renderPermissions() {
    if (allPermissions.length === 0) {
        $('#permissions_container').html('<div class="alert alert-info">No permissions found.</div>');
        return;
    }
    
    let groups = groupPermissionsByModule(allPermissions);
    let html = '';
    
    for (const [moduleName, permissions] of Object.entries(groups)) {
        if (permissions.length > 0) {
            html += `
                <div class="card mb-2 border">
                    <div class="card-header py-2">
                        <div class="d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-2">
                                <input type="checkbox" 
                                       class="custom-control-input module-checkbox" 
                                       id="module_${moduleName.replace(/ /g, '_')}"
                                       data-module="${moduleName}"> 
                                <label class="custom-control-label font-weight-bold" for="module_${moduleName.replace(/ /g, '_')}">
                                    ${moduleName}
                                </label>
                            </div>
                            <span class="badge badge-light ml-auto">${permissions.length}</span>
                            <button type="button" 
                                    class="btn btn-sm  ml-2 toggle-operations" 
                                    data-module="${moduleName}">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3 operations-container" id="operations_${moduleName.replace(/ /g, '_')}" style="display: none;">
                        <div class="row">
            `;
            
            permissions.forEach(permission => {
                let isUserPermission = selectedPermissions.includes(permission.name);
                let isRolePermission = rolePermissions.includes(permission.name);
                
                let labelClass = '';
                let badge = '';
                let disabledAttr = '';
                let checkedAttr = '';
                
                if (currentType === 'user') {
                    if (isUserPermission) {
                        labelClass = 'text-primary font-weight-bold';
                        badge = '<span class="badge badge-primary badge-pill ml-2">Direct</span>';
                        checkedAttr = 'checked';
                    } else if (isRolePermission) {
                        labelClass = 'text-muted';
                        badge = '<span class="badge badge-secondary badge-pill ml-2">From Role</span>';
                        checkedAttr = 'checked'; // Role wali hamesha checked rahegi
                        disabledAttr = 'disabled'; // Isay user level par uncheck nahi kar sakte
                    }
                } else {
                    checkedAttr = isUserPermission ? 'checked' : '';
                }
                
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input operation-checkbox" 
                                   id="operation_${permission.name.replace(/\./g, '_')}"
                                   value="${permission.name}"
                                   data-module="${moduleName}"
                                   ${checkedAttr}
                                   ${disabledAttr}>
                            <label class="custom-control-label ${labelClass}" for="operation_${permission.name.replace(/\./g, '_')}">
                                ${permission.display_name}
                                ${badge}
                            </label>
                        </div>
                    </div>
                `;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    $('#permissions_container').html(html);
    attachEventHandlers();

    for (const moduleName of Object.keys(groups)) {
        updateModuleCheckbox(moduleName);
    }
}

    function attachEventHandlers() {
        $('.toggle-operations').click(function() {
            const moduleName = $(this).data('module');
            const operationsDiv = $(`#operations_${moduleName.replace(/ /g, '_')}`);
            const icon = $(this).find('i');

            operationsDiv.slideToggle();
            icon.toggleClass('fa-chevron-down fa-chevron-up');
        });

        $('.module-checkbox:not(:disabled)').change(function() {
            const moduleName = $(this).data('module');
            const isChecked = $(this).prop('checked');

            $(`#operations_${moduleName.replace(/ /g, '_')} .operation-checkbox:not(:disabled)`).prop('checked', isChecked);
            updateSelectedPermissions();
        });

        $('.operation-checkbox:not(:disabled)').change(function() {
            updateModuleCheckbox($(this).data('module'));
            updateSelectedPermissions();
        });
    }

    function updateModuleCheckbox(moduleName) {
    const moduleCheckbox = $(`#module_${moduleName.replace(/ /g, '_')}`);
    
    const operations = $(`#operations_${moduleName.replace(/ /g, '_')} .operation-checkbox`);
    const enabledOperations = operations.not(':disabled');

    if (currentType === 'user' && enabledOperations.length === 0) {
        moduleCheckbox.prop('disabled', true);
        moduleCheckbox.prop('checked', true); // Kyunke role wali checked hain
        return;
    } else {
        moduleCheckbox.prop('disabled', false);
    }

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

    function updateSelectedPermissions() {
        selectedPermissions = [];
        $('.operation-checkbox:checked:not(:disabled)').each(function() {
            selectedPermissions.push($(this).val());
        });
        updateSummary();
    }

    function updateSummary() {
        let totalModules = 0;
        let totalOperations = 0;
        let userDirectCount = 0;
        let roleBasedCount = 0;

        $('.card.mb-2.border').each(function() {
            totalModules++;
            const moduleName = $(this).find('.module-checkbox').data('module');
            const operations = $(`#operations_${moduleName.replace(/ /g, '_')} .operation-checkbox`).length;
            totalOperations += operations;
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

    $('#btn_select_all').click(function() {
        if (currentType === 'user') {
            $('.operation-checkbox:not(:disabled)').prop('checked', true);
        } else {
            $('.operation-checkbox').prop('checked', true);
            $('.module-checkbox').prop('checked', true).prop('indeterminate', false);
        }
        updateSelectedPermissions();
    });

    $('#btn_deselect_all').click(function() {
        if (currentType === 'user') {
            $('.operation-checkbox:not(:disabled)').prop('checked', false);
        } else {
            $('.operation-checkbox').prop('checked', false);
            $('.module-checkbox').prop('checked', false).prop('indeterminate', false);
        }
        updateSelectedPermissions();
    });

    $('#btn_copy_role').click(function() {
        if (currentType !== 'user') {
            alert('Please select a user first');
            return;
        }
        $('#copyRoleModal').modal('show');
    });

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

    loadAllPermissions();
    autoSelectFromUrl();
});
</script>

@endsection