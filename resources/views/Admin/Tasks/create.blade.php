@extends('layout.admin')

@section('content')
<div class="content task-create-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>Create Task
                </h3>
                <p class="text-muted mb-0">Assign a task, connect it with a lead, contact, or account, and keep the account link visible for the team.</p>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-light border">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card task-form-card">
        <div class="card-body p-4 p-lg-5">
            <form method="POST" action="{{ route('admin.tasks.store') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="assigned_to">Task Owner</label>
                            <select name="assigned_to" id="assigned_to" class="form-control" required>
                                <option value="">Select Task Owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>
                                        {{ $user->full_name ?: $user->username ?: $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="datetime-local" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="entity_type">Related Type</label>
                            <select name="entity_type" id="entity_type" class="form-control" required>
                                <option value="">Select Related Type</option>
                                @foreach($entityTypeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('entity_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="entity_id">Related Record</label>
                            <select name="entity_id" id="entity_id" class="form-control" required data-selected="{{ old('entity_id') }}">
                                <option value="">Select Related Record</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="account_id">Account</label>
                            <select name="account_id" id="account_id" class="form-control">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', \App\Models\CrmTask::STATUS_DEFERRED) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select name="priority" id="priority" class="form-control" required>
                                @foreach($priorityOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority', \App\Models\CrmTask::PRIORITY_NORMAL) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="5" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="task-form-actions mt-4">
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Task
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
            'account_id' => $contact->account_id,
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
        const entityTypeSelect = document.getElementById('entity_type');
        const entitySelect = document.getElementById('entity_id');
        const accountSelect = document.getElementById('account_id');

        const entityOptions = {
            lead: @json($leadOptions),
            contact: @json($contactOptions),
            account: @json($accountOptions),
        };

        function fillEntityOptions(type) {
            const selectedValue = entitySelect.dataset.selected || '';
            const options = entityOptions[type] || [];

            entitySelect.innerHTML = '<option value="">Select Related Record</option>';

            options.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.label;

                if (String(item.id) === String(selectedValue)) {
                    option.selected = true;
                }

                if (item.account_id) {
                    option.dataset.accountId = item.account_id;
                }

                entitySelect.appendChild(option);
            });
        }

        function syncAccountSelection() {
            const selectedOption = entitySelect.options[entitySelect.selectedIndex];

            if (!selectedOption) {
                return;
            }

            if (entityTypeSelect.value === 'contact' && selectedOption.dataset.accountId) {
                accountSelect.value = selectedOption.dataset.accountId;
            }

            if (entityTypeSelect.value === 'account') {
                accountSelect.value = selectedOption.value || '';
            }
        }

        entityTypeSelect.addEventListener('change', function () {
            entitySelect.dataset.selected = '';
            fillEntityOptions(this.value);
            syncAccountSelection();
        });

        entitySelect.addEventListener('change', syncAccountSelection);

        fillEntityOptions(entityTypeSelect.value);
        syncAccountSelection();
    })();
</script>

<style>
    .task-form-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .task-create-page .form-control {
        min-height: 46px;
        border-radius: 12px;
        border-color: #d7e0ea;
    }

    .task-create-page textarea.form-control {
        min-height: auto;
    }

    .task-create-page label {
        font-weight: 600;
        color: #334155;
    }

    .task-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
    }

    @media (max-width: 767.98px) {
        .task-form-actions {
            flex-direction: column;
        }

        .task-form-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection
