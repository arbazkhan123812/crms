@extends('layout.admin')

@section('content')
<div class="content gogi-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-building text-primary mr-2"></i>Accounts
                </h3>
                <p class="text-muted mb-0">Manage customer accounts, review key details, and open full profiles from the list below.</p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                @can('accounts_addaccount')

                <button type="button" class="btn btn-primary" onclick="openAccountModal()">
                    <i class="fas fa-plus-circle mr-1"></i> New Account
                </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- STATISTICS CARDS -->
    <div class="row mb-4">
        @can('accounts_showtotalaccountscard')
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg-primary rounded-circle mr-3">
                            <i class="fas fa-building fa-lg text-white"></i>
                        </div>
                        <div>
                            <p class="card-text mb-1 text-muted">Total Accounts</p>
                            <h4 class="card-title mb-0">{{ $accounts->count() }}</h4>
                            <span class="text-muted small">All customer records</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('accounts_showlinkedcontactscard')
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg-primary rounded-circle mr-3">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <p class="card-text mb-1 text-muted">Linked Contacts</p>
                            <h4 class="card-title mb-0">{{ $accounts->sum(fn($account) => $account->contacts->count()) }}</h4>
                            <span class="text-muted small">Contacts across accounts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('accounts_showtotalindustriestaggedcard')
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg-primary rounded-circle mr-3">
                            <i class="fas fa-industry fa-lg text-white"></i>
                        </div>
                        <div>
                            <p class="card-text mb-1 text-muted">Industries Tagged</p>
                            <h4 class="card-title mb-0">{{ $accounts->pluck('industry')->filter()->unique()->count() }}</h4>
                            <span class="text-muted small">Distinct industries</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('accounts_showtotalrecentaddedcar')
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg-primary rounded-circle mr-3">
                            <i class="fas fa-calendar-alt fa-lg text-white"></i>
                        </div>
                        <div>
                            <p class="card-text mb-1 text-muted">Latest Added</p>
                            <h4 class="card-title mb-0">{{ optional($accounts->first()?->created_at)->format('d M Y') ?: '-' }}</h4>
                            <span class="text-muted small">Most recent account</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- MAIN TABLE -->
    @can('accounts_showtable')
    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="myTable">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-top-0">Account</th>
                            <th class="border-top-0">Email</th>
                            <th class="border-top-0">Phone</th>
                            <th class="border-top-0">Industry</th>
                            <th class="border-top-0 text-center">Contacts</th>
                            <th class="border-top-0">Created</th>
                            <th class="border-top-0 text-center" width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr class="gogi-click-row" data-href="{{ route('admin.account.show', $account->id) }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="gogi-avatar mr-3">{{ strtoupper(substr($account->name ?? 'A', 0, 1)) }}</span>
                                    <div>
                                        <div class="text-dark">{{ $account->name ?: '-' }}</div>
                                        <small class="text-muted">{{ $account->type ?: 'Customer' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $account->email ?: '-' }}</td>
                            <td>{{ $account->phone ?: '-' }}</td>
                            <td>{{ $account->industry ?: '-' }}</td>
                            <td class="text-center"><span class="badge badge-light px-3 py-2">{{ $account->contacts->count() }}</span></td>
                            <td>{{ optional($account->created_at)->format('d M Y') ?: '-' }}</td>
                            <td class="text-center">
                                @can('accounts_editaccount')
                                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); editAccount({{ $account->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan
                                @can('accounts_deleteaccount')
                                <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteAccount({{ $account->id }})" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No accounts found yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan
</div>

<!-- Account Modal (Create/Edit) -->
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="accountModalLabel">
                    <i class="fas fa-building mr-2"></i>Create New Account
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="accountForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="account_id">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    
                    <!-- Account Information Section -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Account Information</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Account Owner</label>
                                        <select name="account_owner" id="account_owner" class="form-control form-control-sm">
                                            <option value="">Select Owner</option>
                                            @foreach($accountOwners as $owner)
                                                <option value="{{ $owner->id }}">{{ $owner->username }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Account Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="account_name" class="form-control form-control-sm" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Account Site</label>
                                        <input type="text" name="account_site" id="account_site" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Parent Account</label>
                                        <select name="parent_account_id" id="parent_account_id" class="form-control form-control-sm">
                                            <option value="">-- None --</option>
                                            @foreach($parentAccounts as $parent)
                                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Account Number</label>
                                        <input type="text" name="account_number" id="account_number" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Account Type</label>
                                        <select name="type" id="account_type" class="form-control form-control-sm">
                                            <option value="">-- None --</option>
                                            <option value="Customer">Customer</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Vendor">Vendor</option>
                                            <option value="Prospect">Prospect</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Industry</label>
                                        <select name="industry" id="account_industry" class="form-control form-control-sm">
                                            <option value="">-- None --</option>
                                            <option value="Agriculture">Agriculture</option>
                                            <option value="Automotive">Automotive</option>
                                            <option value="Banking">Banking</option>
                                            <option value="Construction">Construction</option>
                                            <option value="Education">Education</option>
                                            <option value="Healthcare">Healthcare</option>
                                            <option value="IT Services">IT Services</option>
                                            <option value="Manufacturing">Manufacturing</option>
                                            <option value="Retail">Retail</option>
                                            <option value="Telecommunications">Telecommunications</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Annual Revenue (PKR)</label>
                                        <input type="number" name="annual_revenue" id="account_annual_revenue" class="form-control form-control-sm" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Rating</label>
                                        <select name="rating" id="account_rating" class="form-control form-control-sm">
                                            <option value="">-- None --</option>
                                            <option value="Hot">Hot</option>
                                            <option value="Warm">Warm</option>
                                            <option value="Cold">Cold</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Employees</label>
                                        <input type="number" name="no_of_employees" id="account_employees" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Ticker Symbol</label>
                                        <input type="text" name="ticker_symbol" id="account_ticker" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Ownership</label>
                                        <select name="ownership" id="account_ownership" class="form-control form-control-sm">
                                            <option value="">-- None --</option>
                                            <option value="Public">Public</option>
                                            <option value="Private">Private</option>
                                            <option value="Government">Government</option>
                                            <option value="Non-Profit">Non-Profit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">SIC Code</label>
                                        <input type="text" name="sic_code" id="account_sic" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-phone-alt text-primary mr-2"></i>Contact Information</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Phone</label>
                                        <input type="text" name="phone" id="account_phone" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Fax</label>
                                        <input type="text" name="fax" id="account_fax" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Email</label>
                                        <input type="email" name="email" id="account_email" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Website</label>
                                        <input type="url" name="website" id="account_website" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address Section -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-map-marker-alt text-primary mr-2"></i>Billing Address</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Billing Street</label>
                                        <input type="text" name="street" id="account_street" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Billing City</label>
                                        <input type="text" name="city" id="account_city" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Billing State</label>
                                        <input type="text" name="state" id="account_state" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Billing Code</label>
                                        <input type="text" name="zip_code" id="account_zip" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Billing Country</label>
                                        <input type="text" name="country" id="account_country" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address Section -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-truck text-primary mr-2"></i>Shipping Address</h6>
                            <div class="float-right">
                                <button type="button" class="btn btn-sm btn-link" onclick="copyBillingToShipping()">
                                    <i class="fas fa-copy"></i> Copy Billing Address
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Shipping Street</label>
                                        <input type="text" name="shipping_street" id="account_shipping_street" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Shipping City</label>
                                        <input type="text" name="shipping_city" id="account_shipping_city" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Shipping State</label>
                                        <input type="text" name="shipping_state" id="account_shipping_state" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Shipping Code</label>
                                        <input type="text" name="shipping_code" id="account_shipping_code" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label mb-1">Shipping Country</label>
                                        <input type="text" name="shipping_country" id="account_shipping_country" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="card mb-3 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-align-left text-primary mr-2"></i>Description Information</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-0">
                                <label class="form-label mb-1">Description</label>
                                <textarea name="description" id="account_description" rows="4" class="form-control form-control-sm" placeholder="Enter account description..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
   

    $('#accountForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#account_id').val();
        let url = id ? '{{ url("admin/accounts") }}/' + id : '{{ route("admin.account.store") }}';
        let method = id ? 'PUT' : 'POST';
        
        let formData = $(this).serialize();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData + (id ? '&_method=' + method : ''),
            success: function(response) {
                if(response.success) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false })
                        .then(() => { location.reload(); });
                    $('#accountModal').modal('hide');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                }
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error!', text: message });
            }
        });
    });
});

function openAccountModal() {
    $('#accountForm')[0].reset();
    $('#account_id').val('');
    $('#accountModalLabel').html('<i class="fas fa-building mr-2"></i>Create New Account');
    $('#accountModal').modal('show');
}

function editAccount(id) {
    $.ajax({
        url: '{{ url("admin/accounts") }}/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            if(response.success) {
                let a = response.data;
                $('#account_id').val(a.id);
                $('#account_owner').val(a.account_owner);
                $('#account_name').val(a.name);
                $('#account_site').val(a.account_site);
                $('#parent_account_id').val(a.parent_account_id);
                $('#account_number').val(a.account_number);
                $('#account_type').val(a.type);
                $('#account_industry').val(a.industry);
                $('#account_annual_revenue').val(a.annual_revenue);
                $('#account_rating').val(a.rating);
                $('#account_employees').val(a.no_of_employees);
                $('#account_ticker').val(a.ticker_symbol);
                $('#account_ownership').val(a.ownership);
                $('#account_sic').val(a.sic_code);
                $('#account_phone').val(a.phone);
                $('#account_fax').val(a.fax);
                $('#account_email').val(a.email);
                $('#account_website').val(a.website);
                $('#account_street').val(a.street);
                $('#account_city').val(a.city);
                $('#account_state').val(a.state);
                $('#account_zip').val(a.zip_code);
                $('#account_country').val(a.country);
                $('#account_shipping_street').val(a.shipping_street);
                $('#account_shipping_city').val(a.shipping_city);
                $('#account_shipping_state').val(a.shipping_state);
                $('#account_shipping_code').val(a.shipping_code);
                $('#account_shipping_country').val(a.shipping_country);
                $('#account_description').val(a.description);
                $('#accountModalLabel').html('<i class="fas fa-edit mr-2"></i>Edit Account');
                $('#accountModal').modal('show');
            }
        }
    });
}

function deleteAccount(id) {
    Swal.fire({
        title: 'Delete Account?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: '{{ url("admin/accounts") }}/' + id,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, timer: 1500, showConfirmButton: false })
                            .then(() => { location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                    }
                }
            });
        }
    });
}

function copyBillingToShipping() {
    $('#account_shipping_street').val($('#account_street').val());
    $('#account_shipping_city').val($('#account_city').val());
    $('#account_shipping_state').val($('#account_state').val());
    $('#account_shipping_code').val($('#account_zip').val());
    $('#account_shipping_country').val($('#account_country').val());
}

document.querySelectorAll('.gogi-click-row').forEach(function(row) {
    row.addEventListener('click', function(e) {
        if(e.target.closest('a, button, input, select, textarea')) return;
        window.location.href = row.dataset.href;
    });
});
</script>

<style>
</style>
@endsection
