@extends('layout.admin')

@section('content')
<div class="content meeting-create-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>{{ isset($meeting) ? 'Edit Meeting' : 'Create Meeting' }}
                </h3>
                <p class="text-muted mb-0">Add the meeting details, choose a host, and link it with the right CRM record.</p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('admin.meetings.index') }}" class="btn btn-light border">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Meetings
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card meeting-form-card">
        <div class="card-body p-4 p-lg-5">
            <form method="POST" action="{{ isset($meeting) ? route('admin.meetings.update', $meeting) : route('admin.meetings.store') }}">
                @csrf
                @if(isset($meeting))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $meeting->title ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="location_type">Meeting Location</label>
                            <select name="location_type" id="location_type" class="form-control" required>
                                <option value="">Select Meeting Location</option>
                                @foreach($locationTypeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('location_type', $meeting->location_type ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 physical-location-group">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $meeting->location ?? '') }}">
                        </div>
                    </div>
                    <div class="col-lg-6 virtual-link-group" style="display:none;">
                        <div class="form-group">
                            <label for="meeting_link">Meeting Link</label>
                            <input type="url" name="meeting_link" id="meeting_link" class="form-control" value="{{ old('meeting_link', $meeting->meeting_link ?? '') }}" placeholder="https://example.com/meeting-link">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="assigned_to">Host</label>
                            <select name="assigned_to" id="assigned_to" class="form-control" required>
                                <option value="">Select Host</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('assigned_to', $meeting->assigned_to ?? '') == $user->id)>{{ $user->full_name ?: $user->username ?: $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="starts_at">From</label>
                            <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ old('starts_at', optional($meeting->starts_at ?? $meeting->meeting_date ?? null)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="ends_at">To</label>
                            <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ old('ends_at', optional($meeting->ends_at ?? null)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="entity_type">Related To</label>
                            <select name="entity_type" id="entity_type" class="form-control" required data-selected="{{ old('entity_type', $meeting->entity_type ?? '') }}">
                                <option value="">Select Related Type</option>
                                @foreach($relatedTypeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('entity_type', $meeting->entity_type ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 related-record-group">
                        <div class="form-group">
                            <label for="entity_id">Related Record</label>
                            <select name="entity_id" id="entity_id" class="form-control" data-selected="{{ old('entity_id', $meeting->entity_id ?? '') }}">
                                <option value="">Select Related Record</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="meeting-form-actions mt-4">
                    <a href="{{ route('admin.meetings.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ isset($meeting) ? 'Update Meeting' : 'Save Meeting' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $leadOptions = $leads->map(fn ($lead) => ['id' => $lead->id, 'label' => $lead->full_name ?: 'Lead #' . $lead->id])->values();
    $accountOptions = $accounts->map(fn ($account) => ['id' => $account->id, 'label' => $account->name ?: 'Account #' . $account->id])->values();
    $dealOptions = $deals->map(function ($deal) {
        $label = 'Deal #' . $deal->id;
        if ($deal->account?->name) {
            $label .= ' - ' . $deal->account->name;
        } elseif ($deal->contact?->full_name) {
            $label .= ' - ' . $deal->contact->full_name;
        }
        return ['id' => $deal->id, 'label' => $label];
    })->values();
@endphp

<script>
    (function () {
        const entityTypeSelect = document.getElementById('entity_type');
        const entityIdSelect = document.getElementById('entity_id');
        const locationTypeSelect = document.getElementById('location_type');
        const relatedGroup = document.querySelector('.related-record-group');
        const physicalLocationGroup = document.querySelector('.physical-location-group');
        const virtualLinkGroup = document.querySelector('.virtual-link-group');
        const locationInput = document.getElementById('location');
        const meetingLinkInput = document.getElementById('meeting_link');
        const options = {
            lead: @json($leadOptions),
            account: @json($accountOptions),
            deal: @json($dealOptions),
            other: [],
        };

        function fillRelatedOptions(type) {
            const selectedValue = entityIdSelect.dataset.selected || '';
            const items = options[type] || [];

            entityIdSelect.innerHTML = '<option value="">Select Related Record</option>';

            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.label;
                if (String(item.id) === String(selectedValue)) {
                    option.selected = true;
                }
                entityIdSelect.appendChild(option);
            });

            relatedGroup.style.display = type === 'other' ? 'none' : '';
        }

        function toggleLocationFields() {
            const isVirtual = locationTypeSelect.value === 'virtual';

            physicalLocationGroup.style.display = isVirtual ? 'none' : '';
            virtualLinkGroup.style.display = isVirtual ? '' : 'none';

            if (isVirtual) {
                locationInput.value = '';
            } else {
                meetingLinkInput.value = '';
            }
        }

        entityTypeSelect.addEventListener('change', function () {
            entityIdSelect.dataset.selected = '';
            fillRelatedOptions(this.value);
        });

        locationTypeSelect.addEventListener('change', toggleLocationFields);

        fillRelatedOptions(entityTypeSelect.value);
        toggleLocationFields();
    })();
</script>

<style>
    .meeting-form-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .meeting-create-page .form-control {
        min-height: 46px;
        border-radius: 12px;
        border-color: #d7e0ea;
    }

    .meeting-create-page label {
        font-weight: 600;
        color: #334155;
    }

    .meeting-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
    }
</style>
@endsection
