@extends('layout.admin')

@section('content')
<div class="content deal-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>Create Deal
                </h3>
                <p class="text-muted mb-0">Capture the owner, linked account and contact, pipeline stage, and value for this new deal.</p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.deals.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Deals
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('admin.deals.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Deal Owner <span class="text-danger">*</span></label>
                            <select name="deal_owner" class="form-control" required>
                                <option value="">Select owner</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" @selected(old('deal_owner') == $owner->id)>{{ $owner->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Deal Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" min="0" step="0.01" value="{{ old('amount') }}" placeholder="Enter deal amount" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Account Name <span class="text-danger">*</span></label>
                            <select name="account_id" class="form-control" required>
                                <option value="">Select account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Contact Name <span class="text-danger">*</span></label>
                            <select name="contact_id" class="form-control" required>
                                <option value="">Select contact</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}" @selected(old('contact_id') == $contact->id)>{{ $contact->full_name ?: 'Unnamed Contact' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Stage <span class="text-danger">*</span></label>
                            <select name="stage" class="form-control" required>
                                @foreach($stages as $stage)
                                    <option value="{{ $stage }}" @selected(old('stage', $stages[0]) === $stage)>{{ $stage }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Probability (%) <span class="text-danger">*</span></label>
                            <input type="number" name="probability" class="form-control" min="0" max="100" value="{{ old('probability') }}" placeholder="0 to 100" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Deal Source <span class="text-danger">*</span></label>
                            <select name="deal_source" class="form-control" required>
                                <option value="">Select source</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source }}" @selected(old('deal_source') === $source)>{{ $source }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Closing Date</label>
                            <input type="date" name="closing_date" class="form-control" value="{{ old('closing_date') }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label>Description</label>
                            <textarea name="description" rows="5" class="form-control" placeholder="Add deal notes, context, or delivery details...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.deals.index') }}" class="btn btn-light border mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Deal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .deal-page .card {
        border-radius: 18px;
    }

    .deal-page .form-control {
        min-height: 46px;
        border-radius: 12px;
    }

    .deal-page textarea.form-control {
        min-height: 140px;
    }
</style>
@endsection
