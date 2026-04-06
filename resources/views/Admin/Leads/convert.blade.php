@extends('layout.admin')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-exchange-alt text-primary mr-2"></i>Convert Lead to Customer
                </h3>
                <p class="text-muted mb-0">Convert lead to account and contact</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Leads
                </a>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        Converting lead <strong>{{ $lead->first_name }} {{ $lead->last_name }}</strong> to customer. This will create an account and contact.
    </div>

    <form action="{{ route('admin.lead.processConversion', $lead->id) }}" method="POST">
        @csrf
        <div class="row">
            <!-- Account Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building mr-2"></i> Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $lead->company) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="account_email" class="form-control" value="{{ old('account_email', $lead->email) }}">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="account_phone" class="form-control" value="{{ old('account_phone', $lead->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <input type="url" name="account_website" class="form-control" value="{{ old('account_website', $lead->website) }}">
                        </div>
                        <div class="form-group">
                            <label>Industry</label>
                            <input type="text" name="account_industry" class="form-control" value="{{ old('account_industry') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Annual Revenue</label>
                                    <input type="number" name="account_annual_revenue" class="form-control" step="0.01" value="{{ old('account_annual_revenue', $lead->annual_revenue) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. of Employees</label>
                                    <input type="number" name="account_no_of_employees" class="form-control" value="{{ old('account_no_of_employees', $lead->no_of_employees) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Street</label>
                            <input type="text" name="account_street" class="form-control" value="{{ old('account_street', $lead->street) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="account_city" class="form-control" value="{{ old('account_city', $lead->city) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" name="account_state" class="form-control" value="{{ old('account_state', $lead->state) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" name="account_country" class="form-control" value="{{ old('account_country', $lead->country) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="account_zip_code" class="form-control" value="{{ old('account_zip_code', $lead->zip_code) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="account_description" class="form-control" rows="3">{{ old('account_description', $lead->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle mr-2"></i> Contact Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_first_name" class="form-control" value="{{ old('contact_first_name', $lead->first_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="contact_last_name" class="form-control" value="{{ old('contact_last_name', $lead->last_name) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="contact_title" class="form-control" value="{{ old('contact_title', $lead->title) }}">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $lead->email) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $lead->phone) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile</label>
                                    <input type="text" name="contact_mobile" class="form-control" value="{{ old('contact_mobile', $lead->mobile) }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="contact_description" class="form-control" rows="3">{{ old('contact_description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-exchange-alt mr-2"></i> Convert to Customer
                        </button>
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .card-header {
        background: #35394F !important;
    }
    .btn-success {
        background: #28a745;
        border: none;
    }
    .btn-success:hover {
        background: #218838;
    }
</style>
@endsection