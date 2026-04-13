<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CrmTask;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = $this->baseTaskQuery()->latest()->get();
        $resolvedEntities = $this->resolveTaskEntities($tasks);
        $statusOptions = CrmTask::statusOptions();
        $priorityOptions = CrmTask::priorityOptions();

        return view('Admin.Tasks.index', compact('tasks', 'resolvedEntities', 'statusOptions', 'priorityOptions'));
    }

    public function create()
    {
        $users = User::orderBy('username')->get();
        $leads = Lead::orderBy('first_name')->orderBy('last_name')->get();
        $contacts = Contact::with('account')->orderBy('first_name')->orderBy('last_name')->get();
        $accounts = Account::orderBy('name')->get();
        $statusOptions = CrmTask::statusOptions();
        $priorityOptions = CrmTask::priorityOptions();
        $entityTypeOptions = [
            'lead' => 'Lead',
            'contact' => 'Contact',
            'account' => 'Account',
        ];

        return view('Admin.Tasks.create', compact(
            'users',
            'leads',
            'contacts',
            'accounts',
            'statusOptions',
            'priorityOptions',
            'entityTypeOptions'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'entity_type' => ['required', Rule::in(['lead', 'contact', 'account'])],
            'entity_id' => ['required', 'integer'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'status' => ['required', Rule::in(array_keys(CrmTask::statusOptions()))],
            'priority' => ['required', Rule::in(array_keys(CrmTask::priorityOptions()))],
            'description' => ['nullable', 'string'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$this->entityExists($request->input('entity_type'), (int) $request->input('entity_id'))) {
                $validator->errors()->add('entity_id', 'The selected related record is invalid.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $task = CrmTask::create([
            'entity_type' => $request->entity_type,
            'entity_id' => $request->entity_id,
            'account_id' => $request->account_id ?: $this->resolveAccountId($request->entity_type, (int) $request->entity_id),
            'subject' => $request->subject,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'priority' => $request->priority,
            'completed_at' => $request->status === CrmTask::STATUS_COMPLETED ? now() : null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function updateStatus(Request $request, CrmTask $task)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(CrmTask::statusOptions()))],
        ]);

        $task->status = $request->status;
        $task->completed_at = $request->status === CrmTask::STATUS_COMPLETED ? now() : null;
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
        ]);
    }

    protected function baseTaskQuery(): Builder
    {
        $query = CrmTask::with(['assignedTo', 'createdBy', 'account']);
        $user = auth()->user();

        if (!$user->hasRole('Admin')) {
            $query->where(function (Builder $builder) use ($user) {
                $builder->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        return $query;
    }

    protected function resolveTaskEntities(Collection $tasks): array
    {
        $leadIds = $tasks->where('entity_type', 'lead')->pluck('entity_id')->unique()->filter()->values();
        $contactIds = $tasks->where('entity_type', 'contact')->pluck('entity_id')->unique()->filter()->values();
        $accountIds = $tasks->where('entity_type', 'account')->pluck('entity_id')->unique()->filter()->values();

        return [
            'lead' => Lead::whereIn('id', $leadIds)->get()->keyBy('id'),
            'contact' => Contact::with('account')->whereIn('id', $contactIds)->get()->keyBy('id'),
            'account' => Account::whereIn('id', $accountIds)->get()->keyBy('id'),
        ];
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

    protected function resolveAccountId(string $entityType, int $entityId): ?int
    {
        return match ($entityType) {
            'contact' => Contact::whereKey($entityId)->value('account_id'),
            'account' => $entityId,
            default => null,
        };
    }
}
