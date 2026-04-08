@extends('layout.admin')

@section('content')
    <div class="content gogi-page">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <h3 class="page-title text-dark mb-2">
                        <i class="fas fa-address-book text-primary mr-2"></i>Contacts
                    </h3>
                    <p class="text-muted mb-0">Browse customer contacts, review linked accounts, and open complete profile
                        details from the table.</p>
                </div>
                <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                    @can('contacts_addcontact')

                        <button type="button" class="btn btn-primary" onclick="openContactModal()">
                            <i class="fas fa-plus-circle mr-1"></i> New Contact
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- STATISTICS CARDS -->

        <div class="row mb-4">
            @can('contacts_showtotalcontactscard')

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-statistics h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-bg-primary rounded-circle mr-3">
                                    <i class="fas fa-address-card fa-lg text-white"></i>
                                </div>
                                <div>
                                    <p class="card-text mb-1 text-muted">Total Contacts</p>
                                    <h4 class="card-title mb-0">{{ $contacts->count() }}</h4>
                                    <span class="text-muted small">All contact records</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            @can('contacts_showlinkedaccountscard')

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-statistics h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-bg-primary rounded-circle mr-3">
                                    <i class="fas fa-building fa-lg text-white"></i>
                                </div>
                                <div>
                                    <p class="card-text mb-1 text-muted">Linked Accounts</p>
                                    <h4 class="card-title mb-0">
                                        {{ $contacts->pluck('account_id')->filter()->unique()->count() }}</h4>
                                    <span class="text-muted small">Accounts represented</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            @can('contacts_showwithjobtitlecard')

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card card-statistics h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-bg-primary rounded-circle mr-3">
                                    <i class="fas fa-briefcase fa-lg text-white"></i>
                                </div>
                                <div>
                                    <p class="card-text mb-1 text-muted">With Job Title</p>
                                    <h4 class="card-title mb-0">
                                        {{ $contacts->filter(fn($contact) => filled($contact->title))->count() }}</h4>
                                    <span class="text-muted small">Professionally tagged</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('contacts_showlatestaddcard') 
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-bg-primary rounded-circle mr-3">
                                <i class="fas fa-calendar-alt fa-lg text-white"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1 text-muted">Latest Added</p>
                                <h4 class="card-title mb-0">
                                    {{ optional($contacts->first()?->created_at)->format('d M Y') ?: '-' }}</h4>
                                <span class="text-muted small">Most recent contact</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        <!-- MAIN TABLE -->
        @can('contacts_showtable')
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="myTable">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-top-0">Contact</th>
                                <th class="border-top-0">Email</th>
                                <th class="border-top-0">Phone</th>
                                <th class="border-top-0">Account</th>
                                <th class="border-top-0">Title</th>
                                <th class="border-top-0">Created</th>
                                <th class="border-top-0 text-center" width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr class="gogi-click-row" data-href="{{ route('admin.contact.show', $contact->id) }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="gogi-avatar mr-3">{{ strtoupper(substr($contact->first_name ?? 'C', 0, 1)) }}</span>
                                            <div>
                                                <div class="text-dark">{{ $contact->full_name ?: 'Unnamed Contact' }}</div>
                                                <small
                                                    class="text-muted">{{ $contact->mobile ?: ($contact->phone ?: 'No phone') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $contact->email ?: '-' }}</td>
                                    <td>{{ $contact->phone ?: ($contact->mobile ?: '-') }}</td>
                                    <td>{{ optional($contact->account)->name ?: '-' }}</td>
                                    <td>{{ $contact->title ?: '-' }}</td>
                                    <td>{{ optional($contact->created_at)->format('d M Y') ?: '-' }}</td>
                                    <td class="text-center">
                                        @can('contacts_editcontact')
                                        
                                        <button class="btn btn-sm btn-primary"
                                            onclick="event.stopPropagation(); editContact({{ $contact->id }})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endcan

                                        @can('contacts_deletecontact')
                                        
                                        <button class="btn btn-sm btn-danger"
                                            onclick="event.stopPropagation(); deleteContact({{ $contact->id }})" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No contacts found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Contact Modal (Create/Edit) -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="contactModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Create New Contact
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" name="id" id="contact_id">
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                        <!-- Contact Information Section -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="fas fa-user-circle text-primary mr-2"></i>Contact Information
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Contact Owner</label>
                                            <select name="contact_owner" id="contact_owner"
                                                class="form-control form-control-sm">
                                                <option value="">Select Owner</option>
                                                @foreach($contactOwners as $owner)
                                                    <option value="{{ $owner->id }}">{{ $owner->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Account Name</label>
                                            <select name="account_id" id="contact_account_id"
                                                class="form-control form-control-sm">
                                                <option value="">-- Select Account --</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="contact_first_name"
                                                class="form-control form-control-sm" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Last Name</label>
                                            <input type="text" name="last_name" id="contact_last_name"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Title</label>
                                            <input type="text" name="title" id="contact_title"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Department</label>
                                            <input type="text" name="department" id="contact_department"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Lead Source</label>
                                            <select name="lead_source" id="contact_lead_source"
                                                class="form-control form-control-sm">
                                                <option value="">-- None --</option>
                                                <option value="Website">Website</option>
                                                <option value="Referral">Referral</option>
                                                <option value="Email Campaign">Email Campaign</option>
                                                <option value="Social Media">Social Media</option>
                                                <option value="Advertisement">Advertisement</option>
                                                <option value="Trade Show">Trade Show</option>
                                                <option value="Cold Call">Cold Call</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Date of Birth</label>
                                            <input type="date" name="date_of_birth" id="contact_dob"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Section -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="fas fa-phone-alt text-primary mr-2"></i>Contact Details</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Email</label>
                                            <input type="email" name="email" id="contact_email"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Phone</label>
                                            <input type="text" name="phone" id="contact_phone"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Mobile</label>
                                            <input type="text" name="mobile" id="contact_mobile"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Fax</label>
                                            <input type="text" name="fax" id="contact_fax"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Mailing Address Section -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt text-primary mr-2"></i>Mailing Address</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Mailing Street</label>
                                            <input type="text" name="mailing_street" id="contact_mailing_street"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Mailing City</label>
                                            <input type="text" name="mailing_city" id="contact_mailing_city"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Mailing State</label>
                                            <input type="text" name="mailing_state" id="contact_mailing_state"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Mailing Code</label>
                                            <input type="text" name="mailing_code" id="contact_mailing_code"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label mb-1">Mailing Country</label>
                                            <input type="text" name="mailing_country" id="contact_mailing_country"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="fas fa-align-left text-primary mr-2"></i>Description Information
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="form-group mb-0">
                                    <label class="form-label mb-1">Description</label>
                                    <textarea name="description" id="contact_description" rows="4"
                                        class="form-control form-control-sm"
                                        placeholder="Enter contact description..."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            $('#contactForm').on('submit', function (e) {
                e.preventDefault();
                let id = $('#contact_id').val();
                let url = id ? '{{ url("admin/contacts") }}/' + id : '{{ route("admin.contact.store") }}';
                let method = id ? 'PUT' : 'POST';

                let formData = $(this).serialize();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData + (id ? '&_method=' + method : ''),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message, timer: 1500, showConfirmButton: false })
                                .then(() => { location.reload(); });
                            $('#contactModal').modal('hide');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: response.message });
                        }
                    },
                    error: function (xhr) {
                        let message = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({ icon: 'error', title: 'Error!', text: message });
                    }
                });
            });
        });

        function openContactModal() {
            $('#contactForm')[0].reset();
            $('#contact_id').val('');
            $('#contactModalLabel').html('<i class="fas fa-user-plus mr-2"></i>Create New Contact');
            $('#contactModal').modal('show');
        }

        function editContact(id) {
            $.ajax({
                url: '{{ url("admin/contacts") }}/' + id + '/edit',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        let c = response.data;
                        $('#contact_id').val(c.id);
                        $('#contact_owner').val(c.contact_owner);
                        $('#contact_account_id').val(c.account_id);
                        $('#contact_first_name').val(c.first_name);
                        $('#contact_last_name').val(c.last_name);
                        $('#contact_title').val(c.title);
                        $('#contact_department').val(c.department);
                        $('#contact_lead_source').val(c.lead_source);
                        $('#contact_dob').val(c.date_of_birth);
                        $('#contact_email').val(c.email);
                        $('#contact_secondary_email').val(c.secondary_email);
                        $('#contact_phone').val(c.phone);
                        $('#contact_other_phone').val(c.other_phone);
                        $('#contact_mobile').val(c.mobile);
                        $('#contact_home_phone').val(c.home_phone);
                        $('#contact_fax').val(c.fax);
                        $('#contact_assistant_phone').val(c.assistant_phone);
                        $('#contact_skype').val(c.skype_id);
                        $('#contact_twitter').val(c.twitter);
                        $('#contact_mailing_street').val(c.mailing_street);
                        $('#contact_mailing_city').val(c.mailing_city);
                        $('#contact_mailing_state').val(c.mailing_state);
                        $('#contact_mailing_code').val(c.mailing_code);
                        $('#contact_mailing_country').val(c.mailing_country);
                        $('#contact_description').val(c.description);
                        $('#contactModalLabel').html('<i class="fas fa-edit mr-2"></i>Edit Contact');
                        $('#contactModal').modal('show');
                    }
                }
            });
        }

        function deleteContact(id) {
            Swal.fire({
                title: 'Delete Contact?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/contacts") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: function (response) {
                            if (response.success) {
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

        document.querySelectorAll('.gogi-click-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a, button, input, select, textarea')) return;
                window.location.href = row.dataset.href;
            });
        });
    </script>

    <style>
    </style>
@endsection
