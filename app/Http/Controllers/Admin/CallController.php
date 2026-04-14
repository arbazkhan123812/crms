<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CrmCall;
use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CallController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:calls_view')->only(['index']);
        $this->middleware('permission:calls_create')->only(['create', 'store']);
        $this->middleware('permission:calls_edit')->only(['edit', 'update']);
        $this->middleware('permission:calls_delete')->only(['destroy']);
    }

    public function index()
    {
        $leadCalls = $this->leadCallQuery()->with(['lead', 'calledBy', 'callOwner'])->get();
        $crmCalls = $this->crmCallQuery()->with(['calledBy', 'callOwner'])->get();
        $resolvedEntities = $this->resolveEntities($crmCalls);

        $calls = $leadCalls
            ->map(fn (LeadCall $call) => $this->mapLeadCall($call))
            ->concat($crmCalls->map(fn (CrmCall $call) => $this->mapCrmCall($call, $resolvedEntities)))
            ->sortByDesc(fn (array $call) => $call['start_time_sort'] ?? $call['created_at_sort'])
            ->values();

        return view('Admin.Calls.index', [
            'calls' => $calls,
            'scheduleCount' => $calls->where('mode', 'schedule')->count(),
            'loggedCount' => $calls->where('mode', 'log')->count(),
        ]);
    }

    public function create(Request $request)
    {
        $mode = in_array($request->query('mode'), ['schedule', 'log'], true) ? $request->query('mode') : 'schedule';
        return view('Admin.Calls.create', array_merge(
            $this->callFormData(),
            ['mode' => $mode]
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode' => ['required', Rule::in(['schedule', 'log'])],
            'call_owner' => ['required', 'exists:users,id'],
            'entity_type' => ['required', Rule::in(['lead', 'contact', 'account'])],
            'entity_id' => ['required', 'integer'],
            'related_to_type' => ['nullable', Rule::in(['lead', 'contact', 'account'])],
            'related_to_id' => ['nullable', 'integer'],
            'call_type' => ['required', Rule::in(['inbound', 'outbound', 'missed'])],
            'outgoing_call_status' => ['nullable', Rule::in(['answered', 'no_answer', 'voicemail', 'busy'])],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'duration' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$this->entityExists($request->input('entity_type'), (int) $request->input('entity_id'))) {
                $validator->errors()->add('entity_id', 'The selected call record is invalid.');
            }

            if ($request->filled('related_to_id') && !$this->entityExists($request->input('related_to_type'), (int) $request->input('related_to_id'))) {
                $validator->errors()->add('related_to_id', 'The selected related record is invalid.');
            }

            if ($request->input('mode') === 'schedule' && $request->input('call_type') === 'missed') {
                $validator->errors()->add('call_type', 'Missed is only available when logging a call.');
            }

            if ($request->input('mode') === 'schedule' && $request->input('call_type') === 'outbound' && !$request->filled('end_time')) {
                $validator->errors()->add('end_time', 'End time is required for outbound scheduled calls.');
            }

            if ($request->input('mode') === 'log' && !$request->filled('subject')) {
                $validator->errors()->add('subject', 'The subject field is required when logging a call.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $payload = [
            'call_type' => $request->call_type,
            'call_purpose' => $request->subject,
            'subject' => $request->subject,
            'duration' => $request->duration,
            'status' => $request->mode === 'schedule'
                ? 'scheduled'
                : ($request->call_type === 'missed' ? 'missed' : 'completed'),
            'call_date' => $request->start_time,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'called_by' => auth()->id(),
            'call_owner' => $request->call_owner,
            'related_to_type' => $request->related_to_type,
            'related_to_id' => $request->related_to_id,
            'outgoing_call_status' => $request->outgoing_call_status,
        ];

        if ($request->entity_type === 'lead') {
            LeadCall::create(array_merge($payload, [
                'lead_id' => $request->entity_id,
                'notes' => null,
            ]));
        } else {
            CrmCall::create(array_merge($payload, [
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id,
                'notes' => null,
            ]));
        }

        return redirect()
            ->route('admin.calls.index')
            ->with('success', $request->mode === 'schedule' ? 'Call scheduled successfully!' : 'Call logged successfully!');
    }

    public function edit(string $source, int $id)
    {
        $call = $this->findCall($source, $id);
        $mode = $call->status === 'scheduled' ? 'schedule' : 'log';

        return view('Admin.Calls.create', array_merge(
            $this->callFormData(),
            compact('mode', 'source', 'call')
        ));
    }

    public function update(Request $request, string $source, int $id)
    {
        $validator = $this->callValidator($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $call = $this->findCall($source, $id);
        $payload = $this->callPayload($validated);

        if ($source === 'lead') {
            $call->update(array_merge($payload, [
                'lead_id' => $validated['entity_id'],
            ]));
        } else {
            $call->update(array_merge($payload, [
                'entity_type' => $validated['entity_type'],
                'entity_id' => $validated['entity_id'],
            ]));
        }

        return redirect()
            ->route('admin.calls.index')
            ->with('success', 'Call updated successfully!');
    }

    public function destroy(string $source, int $id)
    {
        $call = $this->findCall($source, $id);
        $call->delete();

        return redirect()
            ->route('admin.calls.index')
            ->with('success', 'Call deleted successfully!');
    }

    protected function callFormData(): array
    {
        return [
            'users' => User::orderBy('username')->orderBy('full_name')->get(),
            'leads' => Lead::orderBy('first_name')->orderBy('last_name')->get(),
            'contacts' => Contact::with('account')->orderBy('first_name')->orderBy('last_name')->get(),
            'accounts' => Account::orderBy('name')->get(),
            'entityTypeOptions' => [
                'lead' => 'Lead',
                'contact' => 'Contact',
                'account' => 'Account',
            ],
            'callTypeOptions' => [
                'outbound' => 'Outbound',
                'inbound' => 'Inbound',
                'missed' => 'Missed',
            ],
            'outgoingCallStatusOptions' => [
                'answered' => 'Answered',
                'no_answer' => 'No Answer',
                'voicemail' => 'Voicemail',
                'busy' => 'Busy',
            ],
        ];
    }

    protected function callValidator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode' => ['required', Rule::in(['schedule', 'log'])],
            'call_owner' => ['required', 'exists:users,id'],
            'entity_type' => ['required', Rule::in(['lead', 'contact', 'account'])],
            'entity_id' => ['required', 'integer'],
            'related_to_type' => ['nullable', Rule::in(['lead', 'contact', 'account'])],
            'related_to_id' => ['nullable', 'integer'],
            'call_type' => ['required', Rule::in(['inbound', 'outbound', 'missed'])],
            'outgoing_call_status' => ['nullable', Rule::in(['answered', 'no_answer', 'voicemail', 'busy'])],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'duration' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$this->entityExists($request->input('entity_type'), (int) $request->input('entity_id'))) {
                $validator->errors()->add('entity_id', 'The selected call record is invalid.');
            }

            if ($request->filled('related_to_id') && !$this->entityExists($request->input('related_to_type'), (int) $request->input('related_to_id'))) {
                $validator->errors()->add('related_to_id', 'The selected related record is invalid.');
            }

            if ($request->input('mode') === 'schedule' && $request->input('call_type') === 'missed') {
                $validator->errors()->add('call_type', 'Missed is only available when logging a call.');
            }

            if ($request->input('mode') === 'schedule' && $request->input('call_type') === 'outbound' && !$request->filled('end_time')) {
                $validator->errors()->add('end_time', 'End time is required for outbound scheduled calls.');
            }

            if ($request->input('mode') === 'log' && !$request->filled('subject')) {
                $validator->errors()->add('subject', 'The subject field is required when logging a call.');
            }
        });

        return $validator;
    }

    protected function callPayload(array $validated): array
    {
        return [
            'call_type' => $validated['call_type'],
            'call_purpose' => $validated['subject'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'status' => $validated['mode'] === 'schedule'
                ? 'scheduled'
                : ($validated['call_type'] === 'missed' ? 'missed' : 'completed'),
            'call_date' => $validated['start_time'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'called_by' => auth()->id(),
            'call_owner' => $validated['call_owner'],
            'related_to_type' => $validated['related_to_type'] ?? null,
            'related_to_id' => $validated['related_to_id'] ?? null,
            'outgoing_call_status' => $validated['outgoing_call_status'] ?? null,
        ];
    }

    protected function findCall(string $source, int $id)
    {
        return match ($source) {
            'lead' => LeadCall::findOrFail($id),
            'crm' => CrmCall::findOrFail($id),
            default => abort(404),
        };
    }

    protected function leadCallQuery()
    {
        $query = LeadCall::query();
        $user = auth()->user();

        if (!$user->hasRole('Admin')) {
            $query->where(function ($builder) use ($user) {
                $builder->where('call_owner', $user->id)
                    ->orWhere('called_by', $user->id);
            });
        }

        return $query;
    }

    protected function crmCallQuery()
    {
        $query = CrmCall::query();
        $user = auth()->user();

        if (!$user->hasRole('Admin')) {
            $query->where(function ($builder) use ($user) {
                $builder->where('call_owner', $user->id)
                    ->orWhere('called_by', $user->id);
            });
        }

        return $query;
    }

    protected function resolveEntities(Collection $crmCalls): array
    {
        $entityIds = [
            'lead' => $crmCalls->where('entity_type', 'lead')->pluck('entity_id')->unique()->filter()->values(),
            'contact' => $crmCalls->where('entity_type', 'contact')->pluck('entity_id')->unique()->filter()->values(),
            'account' => $crmCalls->where('entity_type', 'account')->pluck('entity_id')->unique()->filter()->values(),
            'related_lead' => $crmCalls->where('related_to_type', 'lead')->pluck('related_to_id')->unique()->filter()->values(),
            'related_contact' => $crmCalls->where('related_to_type', 'contact')->pluck('related_to_id')->unique()->filter()->values(),
            'related_account' => $crmCalls->where('related_to_type', 'account')->pluck('related_to_id')->unique()->filter()->values(),
        ];

        return [
            'lead' => Lead::whereIn('id', $entityIds['lead']->merge($entityIds['related_lead']))->get()->keyBy('id'),
            'contact' => Contact::with('account')->whereIn('id', $entityIds['contact']->merge($entityIds['related_contact']))->get()->keyBy('id'),
            'account' => Account::whereIn('id', $entityIds['account']->merge($entityIds['related_account']))->get()->keyBy('id'),
        ];
    }

    protected function mapLeadCall(LeadCall $call): array
    {
        return [
            'source' => 'lead_call',
            'source_key' => 'lead',
            'id' => $call->id,
            'entity_type' => 'lead',
            'entity_id' => $call->lead_id,
            'related_to_type_raw' => $call->related_to_type,
            'related_to_id_raw' => $call->related_to_id,
            'call_type_raw' => $call->call_type,
            'outgoing_call_status_raw' => $call->outgoing_call_status,
            'mode' => $call->status === 'scheduled' ? 'schedule' : 'log',
            'owner_name' => $this->userLabel($call->callOwner),
            'call_owner' => $call->call_owner,
            'call_for_type' => 'Lead',
            'call_for_name' => $call->lead?->full_name ?: ('Lead #' . $call->lead_id),
            'call_for_url' => $call->lead ? route('admin.lead.show', $call->lead->id) : null,
            'related_to_name' => $this->resolveRelatedName($call->related_to_type, $call->related_to_id),
            'subject_raw' => $call->subject ?: $call->call_purpose,
            'type_label' => ucfirst($call->call_type),
            'status_label' => ucfirst(str_replace('_', ' ', $call->status)),
            'subject' => $call->subject ?: $call->call_purpose ?: '-',
            'outgoing_call_status' => $call->outgoing_call_status ? ucfirst(str_replace('_', ' ', $call->outgoing_call_status)) : '-',
            'start_time' => $call->start_time ?: $call->call_date,
            'end_time' => $call->end_time,
            'duration' => $call->duration ?: '-',
            'created_at_sort' => optional($call->created_at)->timestamp ?: 0,
            'start_time_sort' => optional($call->start_time ?: $call->call_date)->timestamp ?: 0,
        ];
    }

    protected function mapCrmCall(CrmCall $call, array $resolvedEntities): array
    {
        $callFor = $resolvedEntities[$call->entity_type][$call->entity_id] ?? null;
        $relatedTo = $call->related_to_type ? ($resolvedEntities[$call->related_to_type][$call->related_to_id] ?? null) : null;

        return [
            'source' => 'crm_call',
            'source_key' => 'crm',
            'id' => $call->id,
            'entity_type' => $call->entity_type,
            'entity_id' => $call->entity_id,
            'related_to_type_raw' => $call->related_to_type,
            'related_to_id_raw' => $call->related_to_id,
            'call_type_raw' => $call->call_type,
            'outgoing_call_status_raw' => $call->outgoing_call_status,
            'mode' => $call->status === 'scheduled' ? 'schedule' : 'log',
            'owner_name' => $this->userLabel($call->callOwner),
            'call_owner' => $call->call_owner,
            'call_for_type' => ucfirst($call->entity_type),
            'call_for_name' => $this->entityLabel($call->entity_type, $callFor, $call->entity_id),
            'call_for_url' => $this->entityUrl($call->entity_type, $callFor?->id),
            'related_to_name' => $this->entityLabel($call->related_to_type, $relatedTo, $call->related_to_id),
            'subject_raw' => $call->subject ?: $call->call_purpose,
            'type_label' => ucfirst($call->call_type),
            'status_label' => ucfirst(str_replace('_', ' ', $call->status)),
            'subject' => $call->subject ?: $call->call_purpose ?: '-',
            'outgoing_call_status' => $call->outgoing_call_status ? ucfirst(str_replace('_', ' ', $call->outgoing_call_status)) : '-',
            'start_time' => $call->start_time ?: $call->call_date,
            'end_time' => $call->end_time,
            'duration' => $call->duration ?: '-',
            'created_at_sort' => optional($call->created_at)->timestamp ?: 0,
            'start_time_sort' => optional($call->start_time ?: $call->call_date)->timestamp ?: 0,
        ];
    }

    protected function userLabel(?User $user): string
    {
        return $user?->full_name ?: $user?->username ?: $user?->name ?: '-';
    }

    protected function entityExists(?string $entityType, int $entityId): bool
    {
        if (!$entityType || $entityId <= 0) {
            return false;
        }

        return match ($entityType) {
            'lead' => Lead::whereKey($entityId)->exists(),
            'contact' => Contact::whereKey($entityId)->exists(),
            'account' => Account::whereKey($entityId)->exists(),
            default => false,
        };
    }

    protected function entityLabel(?string $entityType, $entity, ?int $fallbackId): string
    {
        if (!$entityType || !$fallbackId) {
            return '-';
        }

        if (!$entity) {
            return ucfirst($entityType) . ' #' . $fallbackId;
        }

        return match ($entityType) {
            'lead', 'contact' => $entity->full_name ?: (ucfirst($entityType) . ' #' . $fallbackId),
            'account' => $entity->name ?: ('Account #' . $fallbackId),
            default => ucfirst($entityType) . ' #' . $fallbackId,
        };
    }

    protected function entityUrl(?string $entityType, ?int $entityId): ?string
    {
        if (!$entityType || !$entityId) {
            return null;
        }

        return match ($entityType) {
            'lead' => route('admin.lead.show', $entityId),
            'contact' => route('admin.contact.show', $entityId),
            'account' => route('admin.account.show', $entityId),
            default => null,
        };
    }

    protected function resolveRelatedName(?string $entityType, ?int $entityId): string
    {
        if (!$entityType || !$entityId) {
            return '-';
        }

        $entity = match ($entityType) {
            'lead' => Lead::find($entityId),
            'contact' => Contact::find($entityId),
            'account' => Account::find($entityId),
            default => null,
        };

        return $this->entityLabel($entityType, $entity, $entityId);
    }
}
