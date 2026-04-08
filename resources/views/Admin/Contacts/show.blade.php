@extends('layout.admin')

@section('content')
    <div class="content">
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title text-dark">
                        <i class="fas fa-address-book text-primary mr-2"></i>Contact Profile
                    </h3>
                    <p class="text-muted mb-0">View complete contact information and related account context.</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-primary btn-sm">
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
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Contact</p>
                                <h4 class="card-title mb-0">{{ $contact->full_name ?: 'Unnamed Contact' }}</h4>
                                <span class="text-muted small">{{ $contact->title ?: 'No title' }}</span>
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
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Account</p>
                                <h4 class="card-title mb-0">{{ optional($contact->account)->name ?: '-' }}</h4>
                                <span class="text-muted small">Linked organization</span>
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
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <p class="card-text mb-1">Phone</p>
                                <h4 class="card-title mb-0">{{ $contact->phone ?: ($contact->mobile ?: '-') }}</h4>
                                <span class="text-muted small">Primary number</span>
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
                                <h4 class="card-title mb-0">{{ optional($contact->created_at)->format('d M Y') ?: '-' }}</h4>
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
                    <i class="fas fa-info-circle text-primary mr-2"></i>Contact Details
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Email</label><div>{{ $contact->email ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Phone</label><div>{{ $contact->phone ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Mobile</label><div>{{ $contact->mobile ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Skype</label><div>{{ $contact->skype_id ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Twitter</label><div>{{ $contact->twitter ?: '-' }}</div></div></div>
                    <div class="col-md-4"><div class="form-group mb-3"><label class="mb-1 text-muted">Created By</label><div>{{ optional($contact->createdBy)->username ?? optional($contact->createdBy)->name ?? '-' }}</div></div></div>
                    <div class="col-md-6"><div class="form-group mb-3"><label class="mb-1 text-muted">Converted From</label><div>{{ optional($contact->convertedFromLead)->full_name ?? '-' }}</div></div></div>
                    <div class="col-md-6"><div class="form-group mb-3"><label class="mb-1 text-muted">Created At</label><div>{{ optional($contact->created_at)->format('d M Y, h:i A') ?: '-' }}</div></div></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0">
                    <i class="fas fa-user text-primary mr-2"></i>Profile Summary
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="mb-1 text-muted">Account</label>
                            <div>{{ optional($contact->account)->name ?: 'No linked account' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="mb-1 text-muted">Description</label>
                            <div>{{ $contact->description ?: 'No description added.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0">
                    <i class="fas fa-users text-primary mr-2"></i>Related Account Contacts
                </h6>
            </div>
            <div class="card-body p-0">
                @if(!$contact->account || $contact->account->contacts->isEmpty())
                    <div class="text-center text-muted py-5">No related contacts found for this account.</div>
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
                                @foreach($contact->account->contacts as $relatedContact)
                                    <tr>
                                        <td>{{ $relatedContact->full_name ?: 'Unnamed Contact' }}</td>
                                        <td>{{ $relatedContact->title ?: '-' }}</td>
                                        <td>{{ $relatedContact->email ?: '-' }}</td>
                                        <td>{{ $relatedContact->phone ?: ($relatedContact->mobile ?: '-') }}</td>
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
