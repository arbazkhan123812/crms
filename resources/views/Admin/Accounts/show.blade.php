@extends('layout.admin')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-building text-primary mr-2"></i>Account Profile
                    </h3>
                    <p class="text-muted mb-0">View complete account information and related contacts.</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center mr-3" style="width:60px;height:60px;">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Account</p>
                                <h4 class="card-title mb-0">{{ $account->name ?: '-' }}</h4>
                                <span class="text-muted small">{{ $account->type ?: 'Customer' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center mr-3" style="width:60px;height:60px;">
                                <i class="fas fa-industry"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Industry</p>
                                <h4 class="card-title mb-0">{{ $account->industry ?: '-' }}</h4>
                                <span class="text-muted small">Business segment</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center mr-3" style="width:60px;height:60px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Contacts</p>
                                <h4 class="card-title mb-0">{{ $account->contacts->count() }}</h4>
                                <span class="text-muted small">Linked records</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center mr-3" style="width:60px;height:60px;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Created</p>
                                <h4 class="card-title mb-0">{{ optional($account->created_at)->format('d M Y') ?: '-' }}</h4>
                                <span class="text-muted small">Record date</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle text-primary mr-2"></i>Account Details
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Email</label><div>{{ $account->email ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Phone</label><div>{{ $account->phone ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Website</label><div>{{ $account->website ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Revenue</label><div>{{ $account->annual_revenue ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Employees</label><div>{{ $account->no_of_employees ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Created By</label><div>{{ optional($account->createdBy)->username ?? optional($account->createdBy)->name ?? '-' }}</div></div></div>
                    <div class="col-md-6"><div class="form-group mb-3"><label class="mb-1 text-muted">Converted From</label><div>{{ optional($account->convertedFromLead)->full_name ?? '-' }}</div></div></div>
                    <div class="col-md-6"><div class="form-group mb-3"><label class="mb-1 text-muted">Created At</label><div>{{ optional($account->created_at)->format('d M Y, h:i A') ?: '-' }}</div></div></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0">
                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>Address & Description
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="mb-1 text-muted">Address</label>
                            <div>
                                {{ $account->street ?: '-' }}
                                @if($account->city || $account->state || $account->country || $account->zip_code)
                                    <br>{{ collect([$account->city, $account->state, $account->country, $account->zip_code])->filter()->implode(', ') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="mb-1 text-muted">Description</label>
                            <div>{{ $account->description ?: 'No description added.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0">
                    <i class="fas fa-address-book text-primary mr-2"></i>Related Contacts
                </h6>
            </div>
            <div class="card-body p-0">
                @if($account->contacts->isEmpty())
                    <div class="text-center text-muted py-5">No contacts linked with this account.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="myTable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-top-0">Contact</th>
                                    <th class="border-top-0">Title</th>
                                    <th class="border-top-0">Email</th>
                                    <th class="border-top-0">Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->full_name ?: 'Unnamed Contact' }}</td>
                                        <td>{{ $contact->title ?: '-' }}</td>
                                        <td>{{ $contact->email ?: '-' }}</td>
                                        <td>{{ $contact->phone ?: ($contact->mobile ?: '-') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
