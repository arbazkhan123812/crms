@extends('layout.admin')

@section('content')
@php
    $callEntityType = old('entity_type', isset($call) ? ($source === 'lead' ? 'lead' : $call->entity_type) : request('entity_type'));
    $callEntityId = old('entity_id', isset($call) ? ($source === 'lead' ? $call->lead_id : $call->entity_id) : request('entity_id'));
    $callRelatedType = old('related_to_type', $call->related_to_type ?? request('related_to_type'));
    $callRelatedId = old('related_to_id', $call->related_to_id ?? request('related_to_id'));
@endphp
<div class="content call-create-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-phone-volume text-primary mr-2"></i>{{ isset($call) ? 'Edit Call' : ($mode === 'schedule' ? 'Schedule Call' : 'Log Call') }}
                </h3>
                <p class="text-muted mb-0">Choose the owner, connect the call with the right CRM record, and save it in the central calls list.</p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('admin.calls.index') }}" class="btn btn-light border">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Calls
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card call-form-card">
        <div class="card-body p-4 p-lg-5">
            <form method="POST" action="{{ isset($call) ? route('admin.calls.update', ['source' => $source, 'id' => $call->id]) : route('admin.calls.store') }}">
                @csrf
                @if(isset($call))
                    @method('PUT')
                @endif
                <input type="hidden" name="mode" id="mode" value="{{ old('mode', $mode) }}">

                @can('calls_create')
                    <div class="call-mode-switch mb-4">
                        <a href="{{ route('admin.calls.create', ['mode' => 'schedule']) }}" class="btn {{ old('mode', $mode) === 'schedule' ? 'btn-primary' : 'btn-light border' }}">Schedule Call</a>
                        <a href="{{ route('admin.calls.create', ['mode' => 'log']) }}" class="btn {{ old('mode', $mode) === 'log' ? 'btn-primary' : 'btn-light border' }}">Log Call</a>
                    </div>
                @endcan

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="call_owner">Call Owner</label>
                            <select name="call_owner" id="call_owner" class="form-control" required>
                                <option value="">Select Call Owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('call_owner', $call->call_owner ?? auth()->id()) == $user->id)>
                                        {{ $user->full_name ?: $user->username ?: $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="entity_type">Call For</label>
                            <select name="entity_type" id="entity_type" class="form-control" required data-selected="{{ $callEntityType }}">
                                <option value="">Select Type</option>
                                @foreach($entityTypeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($callEntityType === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="entity_id">Record</label>
                            <select name="entity_id" id="entity_id" class="form-control" required data-selected="{{ $callEntityId }}">
                                <option value="">Select Record</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 related-group" style="display:none;">
                        <div class="form-group">
                            <label for="related_to_type">Related To</label>
                            <select name="related_to_type" id="related_to_type" class="form-control" data-selected="{{ $callRelatedType }}">
                                <option value="">Select Related Type</option>
                                @foreach($entityTypeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($callRelatedType === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 related-group" style="display:none;">
                        <div class="form-group">
                            <label for="related_to_id">Related Record</label>
                            <select name="related_to_id" id="related_to_id" class="form-control" data-selected="{{ $callRelatedId }}">
                                <option value="">Select Related Record</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="call_type">Call Type</label>
                            <select name="call_type" id="call_type" class="form-control" required>
                                <option value="">Select Call Type</option>
                                @foreach($callTypeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('call_type', $call->call_type ?? 'outbound') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 outgoing-status-group" style="display:none;">
                        <div class="form-group">
                            <label for="outgoing_call_status">Outgoing Call Status</label>
                            <select name="outgoing_call_status" id="outgoing_call_status" class="form-control">
                                <option value="">Select Outgoing Status</option>
                                @foreach($outgoingCallStatusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('outgoing_call_status', $call->outgoing_call_status ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="start_time">Call Start Time</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', optional($call->start_time ?? $call->call_date ?? null)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6 schedule-outbound-group" style="display:none;">
                        <div class="form-group">
                            <label for="end_time">Call End Time</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', optional($call->end_time ?? null)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div class="col-lg-6 log-group" style="display:none;">
                        <div class="form-group">
                            <label for="duration">Call Duration</label>
                            <input type="text" name="duration" id="duration" class="form-control" value="{{ old('duration', $call->duration ?? '') }}" placeholder="e.g. 15 min">
                        </div>
                    </div>

                    <div class="col-lg-6 log-group" style="display:none;">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', $call->subject ?? $call->call_purpose ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="call-form-actions mt-4">
                    <a href="{{ route('admin.calls.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ isset($call) ? 'Update Call' : 'Save Call' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $leadOptions = $leads->map(function ($lead) {
        return [
            'id' => $lead->id,
            'label' => trim(($lead->full_name ?: 'Lead #' . $lead->id) . ($lead->company ? ' - ' . $lead->company : '')),
        ];
    })->values();

    $contactOptions = $contacts->map(function ($contact) {
        return [
            'id' => $contact->id,
            'label' => trim(($contact->full_name ?: 'Contact #' . $contact->id) . ($contact->email ? ' - ' . $contact->email : '')),
        ];
    })->values();

    $accountOptions = $accounts->map(function ($account) {
        return [
            'id' => $account->id,
            'label' => $account->name ?: 'Account #' . $account->id,
        ];
    })->values();
@endphp

<script>
    (function () {
        const modeInput = document.getElementById('mode');
        const entityTypeSelect = document.getElementById('entity_type');
        const entitySelect = document.getElementById('entity_id');
        const relatedTypeSelect = document.getElementById('related_to_type');
        const relatedSelect = document.getElementById('related_to_id');
        const callTypeSelect = document.getElementById('call_type');
        const logGroups = document.querySelectorAll('.log-group');
        const relatedGroups = document.querySelectorAll('.related-group');
        const scheduleOutboundGroups = document.querySelectorAll('.schedule-outbound-group');
        const outgoingStatusGroups = document.querySelectorAll('.outgoing-status-group');

        const options = {
            lead: @json($leadOptions),
            contact: @json($contactOptions),
            account: @json($accountOptions),
        };

        function fillOptions(select, type) {
            const selectedValue = select.dataset.selected || '';
            const items = options[type] || [];
            const placeholder = select.id === 'related_to_id' ? 'Select Related Record' : 'Select Record';

            select.innerHTML = `<option value="">${placeholder}</option>`;

            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.label;

                if (String(item.id) === String(selectedValue)) {
                    option.selected = true;
                }

                select.appendChild(option);
            });
        }

        function toggleGroups() {
            const mode = modeInput.value;
            const callType = callTypeSelect.value;

            logGroups.forEach((group) => {
                group.style.display = mode === 'log' ? '' : 'none';
            });

            relatedGroups.forEach((group) => {
                group.style.display = mode === 'log' ? '' : 'none';
            });

            scheduleOutboundGroups.forEach((group) => {
                group.style.display = mode === 'schedule' && callType === 'outbound' ? '' : 'none';
            });

            outgoingStatusGroups.forEach((group) => {
                group.style.display = mode === 'log' && callType === 'outbound' ? '' : 'none';
            });

            Array.from(callTypeSelect.options).forEach((option) => {
                option.hidden = mode === 'schedule' && option.value === 'missed';
            });

            if (mode === 'schedule' && callType === 'missed') {
                callTypeSelect.value = 'outbound';
            }
        }

        entityTypeSelect.addEventListener('change', function () {
            entitySelect.dataset.selected = '';
            fillOptions(entitySelect, this.value);
        });

        relatedTypeSelect.addEventListener('change', function () {
            relatedSelect.dataset.selected = '';
            fillOptions(relatedSelect, this.value);
        });

        callTypeSelect.addEventListener('change', toggleGroups);

        fillOptions(entitySelect, entityTypeSelect.value);
        fillOptions(relatedSelect, relatedTypeSelect.value);
        toggleGroups();
    })();
</script>

<style>
    .call-form-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .call-create-page .form-control {
        min-height: 46px;
        border-radius: 12px;
        border-color: #d7e0ea;
    }

    .call-create-page label {
        font-weight: 600;
        color: #334155;
    }

    .call-create-page .call-mode-switch {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
    }

    .call-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
    }

    @media (max-width: 767.98px) {
        .call-form-actions {
            flex-direction: column;
        }

        .call-form-actions .btn,
        .call-create-page .call-mode-switch .btn {
            width: 100%;
        }
    }
</style>
@endsection
