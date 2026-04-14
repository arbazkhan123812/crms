<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CrmMeeting;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\RecordAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:meetings_view')->only(['index']);
        $this->middleware('permission:meetings_create')->only(['create', 'store']);
        $this->middleware('permission:meetings_edit')->only(['edit', 'update']);
        $this->middleware('permission:meetings_delete')->only(['destroy']);
    }

    public function index()
    {
        $meetings = $this->baseMeetingQuery()->with(['assignedTo', 'createdBy'])->latest('starts_at')->get();
        $resolvedEntities = $this->resolveEntities($meetings);

        return view('Admin.Meetings.index', [
            'meetings' => $meetings,
            'resolvedEntities' => $resolvedEntities,
            'locationTypeOptions' => $this->locationTypeOptions(),
        ]);
    }

    public function create(Request $request)
    {
        return view('Admin.Meetings.create', $this->meetingFormData());
    }

    public function store(Request $request)
    {
        $validator = $this->meetingValidator($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $meeting = CrmMeeting::create($this->meetingPayload($validated, true));

        $this->notifyHost($meeting);

        return redirect()
            ->route('admin.meetings.index')
            ->with('success', 'Meeting created successfully!');
    }

    public function edit(CrmMeeting $meeting)
    {
        return view('Admin.Meetings.create', array_merge(
            $this->meetingFormData(),
            ['meeting' => $meeting]
        ));
    }

    public function update(Request $request, CrmMeeting $meeting)
    {
        $validator = $this->meetingValidator($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $previousAssignedTo = $meeting->assigned_to;

        $meeting->update($this->meetingPayload($validated, false));

        if ($meeting->assigned_to !== $previousAssignedTo) {
            $this->notifyHost($meeting);
        }

        return redirect()
            ->route('admin.meetings.index')
            ->with('success', 'Meeting updated successfully!');
    }

    public function destroy(CrmMeeting $meeting)
    {
        $meeting->delete();

        return redirect()
            ->route('admin.meetings.index')
            ->with('success', 'Meeting deleted successfully!');
    }

    protected function baseMeetingQuery()
    {
        $query = CrmMeeting::query();
        $user = auth()->user();

        if (!$user->hasRole('Admin')) {
            $query->where(function ($builder) use ($user) {
                $builder->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        return $query;
    }

    protected function resolveEntities(Collection $meetings): array
    {
        $leadIds = $meetings->where('entity_type', 'lead')->pluck('entity_id')->filter()->unique()->values();
        $accountIds = $meetings->where('entity_type', 'account')->pluck('entity_id')->filter()->unique()->values();
        $dealIds = $meetings->where('entity_type', 'deal')->pluck('entity_id')->filter()->unique()->values();

        return [
            'lead' => Lead::whereIn('id', $leadIds)->get()->keyBy('id'),
            'account' => Account::whereIn('id', $accountIds)->get()->keyBy('id'),
            'deal' => Deal::with(['account', 'contact'])->whereIn('id', $dealIds)->get()->keyBy('id'),
        ];
    }

    protected function entityExists(?string $entityType, int $entityId): bool
    {
        if (!$entityType || $entityId <= 0) {
            return false;
        }

        return match ($entityType) {
            'lead' => Lead::whereKey($entityId)->exists(),
            'account' => Account::whereKey($entityId)->exists(),
            'deal' => Deal::whereKey($entityId)->exists(),
            'other' => true,
            default => false,
        };
    }

    protected function notifyHost(CrmMeeting $meeting): void
    {
        if (!$meeting->assigned_to || $meeting->assigned_to === auth()->id()) {
            return;
        }

        $host = User::find($meeting->assigned_to);

        if (!$host) {
            return;
        }

        $host->notify(new RecordAssignedNotification(
            'Meeting',
            $meeting->id,
            'Meeting Assigned',
            'A meeting has been assigned to you: ' . $meeting->title,
            route('admin.meetings.index'),
            auth()->id()
        ));
    }

    protected function relatedTypeOptions(): array
    {
        return [
            'lead' => 'Lead',
            'account' => 'Account',
            'deal' => 'Deal',
            'other' => 'Other',
        ];
    }

    protected function locationTypeOptions(): array
    {
        return [
            'client_location' => 'Client Location',
            'location' => 'Location',
            'virtual' => 'Virtual',
        ];
    }

    protected function meetingFormData(): array
    {
        return [
            'users' => User::orderBy('username')->orderBy('full_name')->get(),
            'leads' => Lead::orderBy('first_name')->orderBy('last_name')->get(),
            'accounts' => Account::orderBy('name')->get(),
            'deals' => Deal::with(['account', 'contact'])->latest()->get(),
            'relatedTypeOptions' => $this->relatedTypeOptions(),
            'locationTypeOptions' => $this->locationTypeOptions(),
        ];
    }

    protected function meetingValidator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'location_type' => ['required', Rule::in(array_keys($this->locationTypeOptions()))],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'assigned_to' => ['required', 'exists:users,id'],
            'entity_type' => ['required', Rule::in(array_keys($this->relatedTypeOptions()))],
            'entity_id' => ['nullable', 'integer'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $entityType = $request->input('entity_type');
            $entityId = (int) $request->input('entity_id');

            if ($entityType !== 'other' && !$this->entityExists($entityType, $entityId)) {
                $validator->errors()->add('entity_id', 'The selected related record is invalid.');
            }

            if ($request->input('location_type') === 'virtual' && !$request->filled('meeting_link')) {
                $validator->errors()->add('meeting_link', 'The meeting link field is required for virtual meetings.');
            }
        });

        return $validator;
    }

    protected function meetingPayload(array $validated, bool $isCreate): array
    {
        return [
            'entity_type' => $validated['entity_type'],
            'entity_id' => $validated['entity_type'] === 'other' ? null : ($validated['entity_id'] ?? null),
            'title' => $validated['title'],
            'meeting_type' => 'scheduled',
            'location_type' => $validated['location_type'],
            'location' => $validated['location_type'] === 'virtual' ? null : ($validated['location'] ?? null),
            'meeting_link' => $validated['location_type'] === 'virtual' ? ($validated['meeting_link'] ?? null) : null,
            'meeting_date' => $validated['starts_at'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => 'scheduled',
            'assigned_to' => $validated['assigned_to'],
            'created_by' => $isCreate ? auth()->id() : auth()->id(),
        ];
    }
}
