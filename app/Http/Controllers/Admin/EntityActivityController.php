<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CrmCall;
use App\Models\CrmMeeting;
use App\Models\CrmNote;
use App\Models\CrmTask;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\RecordAssignedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntityActivityController extends Controller
{
    protected function resolveEntity(string $entityType, int $entityId): Model
    {
        return match ($entityType) {
            'account' => Account::findOrFail($entityId),
            'contact' => Contact::findOrFail($entityId),
            'lead' => Lead::findOrFail($entityId),
            default => abort(404),
        };
    }

    public function getNotes(string $entityType, int $entityId)
    {
        $entity = $this->resolveEntity($entityType, $entityId);

        $notes = $entity->notes()
            ->with('createdBy')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'type' => $note->type,
                    'created_at' => $note->created_at,
                    'created_by_name' => optional($note->createdBy)->username ?? optional($note->createdBy)->name ?? 'User',
                ];
            });

        return response()->json(['success' => true, 'data' => $notes]);
    }

    public function addNote(Request $request, string $entityType, int $entityId)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string|min:2',
            'type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $entity = $this->resolveEntity($entityType, $entityId);
        $note = $entity->addNote($request->note);

        return response()->json(['success' => true, 'message' => 'Note added successfully!', 'data' => $note]);
    }

    public function updateNote(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string|min:2',
            'type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $note = CrmNote::findOrFail($id);
        $note->update([
            'note' => $request->note,
            'type' => $request->type ?? $note->type,
        ]);

        return response()->json(['success' => true, 'message' => 'Note updated successfully!', 'data' => $note]);
    }

    public function deleteNote(int $id)
    {
        CrmNote::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Note deleted successfully!']);
    }

    public function getTasks(string $entityType, int $entityId)
    {
        $entity = $this->resolveEntity($entityType, $entityId);

        $tasks = $entity->tasks()
            ->with('assignedTo')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'subject' => $task->subject,
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'status' => $task->status,
                    'assigned_to' => $task->assigned_to,
                    'assigned_to_name' => optional($task->assignedTo)->username ?? optional($task->assignedTo)->name ?? '-',
                ];                                
            });

        return response()->json(['success' => true, 'data' => $tasks]);
    }

    public function addTask(Request $request, string $entityType, int $entityId)
    {
        $validator = Validator::make($request->all(), [     
            'subject' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $entity = $this->resolveEntity($entityType, $entityId);
        $task = $entity->addTask(
            $request->subject,
            $request->description,
            $request->assigned_to,
            $request->due_date
        );

        $this->notifyAssignedTaskOwner($task);

        return response()->json(['success' => true, 'message' => 'Task created successfully!', 'data' => $task]);
    }

    public function updateTask(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $task = CrmTask::findOrFail($id);
        $previousAssignedTo = $task->assigned_to;
        $task->update([
            'subject' => $request->subject,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        $this->notifyAssignedTaskOwner($task->fresh(), $previousAssignedTo);

        return response()->json(['success' => true, 'message' => 'Task updated successfully!', 'data' => $task]);
    }

    public function updateTaskStatus(Request $request, int $id)
    {
        $task = CrmTask::findOrFail($id);

        if ($request->status === 'completed') {
            $task->complete();
        } else {
            $task->status = $request->status;
            $task->completed_at = null;
            $task->save();
        }

        return response()->json(['success' => true, 'message' => 'Task status updated!']);
    }

    public function deleteTask(int $id)
    {
        CrmTask::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Task deleted successfully!']);
    }

    public function getCalls(string $entityType, int $entityId)
    {
        $entity = $this->resolveEntity($entityType, $entityId);

        $calls = $entity->calls()
            ->with(['calledBy', 'callOwner'])
            ->get();

        return response()->json(['success' => true, 'data' => $calls]);
    }

    public function addCall(Request $request, string $entityType, int $entityId)
    {
        $validator = Validator::make($request->all(), [
            'call_type' => 'required|in:inbound,outbound',
            'call_purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'duration' => 'nullable|string|max:20',
            'status' => 'required|in:scheduled,completed,missed,voicemail,no_answer',
            'call_date' => 'required|date',
            'call_owner' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $entity = $this->resolveEntity($entityType, $entityId);
        $call = $entity->addCall(
            $request->call_type,
            $request->call_purpose,
            $request->notes,
            $request->duration,
            $request->status,
            $request->call_date,
            $request->call_owner
        );

        return response()->json(['success' => true, 'message' => 'Call saved successfully!', 'data' => $call]);
    }

    public function updateCall(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'call_type' => 'required|in:inbound,outbound',
            'call_purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'duration' => 'nullable|string|max:20',
            'status' => 'required|in:scheduled,completed,missed,voicemail,no_answer',
            'call_date' => 'required|date',
            'call_owner' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $call = CrmCall::findOrFail($id);
        $call->update([
            'call_type' => $request->call_type,
            'call_purpose' => $request->call_purpose,
            'notes' => $request->notes,
            'duration' => $request->duration,
            'status' => $request->status,
            'call_date' => $request->call_date,
            'call_owner' => $request->call_owner,
            'called_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Call updated successfully!', 'data' => $call]);
    }

    public function deleteCall(int $id)
    {
        CrmCall::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Call deleted successfully!']);
    }

    public function getMeetings(string $entityType, int $entityId)
    {
        $entity = $this->resolveEntity($entityType, $entityId);

        $meetings = $entity->meetings()
            ->with('assignedTo')
            ->get();

        return response()->json(['success' => true, 'data' => $meetings]);
    }

    public function addMeeting(Request $request, string $entityType, int $entityId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_type' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:500',
            'meeting_date' => 'required|date',
            'duration' => 'nullable|string|max:50',
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $entity = $this->resolveEntity($entityType, $entityId);
        $meeting = $entity->scheduleMeeting(
            $request->title,
            $request->description,
            $request->meeting_type,
            $request->location,
            $request->meeting_link,
            $request->meeting_date,
            $request->duration,
            $request->assigned_to
        );

        return response()->json(['success' => true, 'message' => 'Meeting scheduled successfully!', 'data' => $meeting]);
    }

    public function updateMeeting(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_type' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:500',
            'meeting_date' => 'required|date',
            'duration' => 'nullable|string|max:50',
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
            'notes' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $meeting = CrmMeeting::findOrFail($id);
        $meeting->update([
            'title' => $request->title,
            'description' => $request->description,
            'meeting_type' => $request->meeting_type,
            'location' => $request->location,
            'meeting_link' => $request->meeting_link,
            'meeting_date' => $request->meeting_date,
            'duration' => $request->duration,
            'status' => $request->status,
            'notes' => $request->notes,
            'assigned_to' => $request->assigned_to,
        ]);

        return response()->json(['success' => true, 'message' => 'Meeting updated successfully!', 'data' => $meeting]);
    }

    public function completeMeeting(Request $request, int $id)
    {
        $meeting = CrmMeeting::findOrFail($id);
        $meeting->complete($request->notes);

        return response()->json(['success' => true, 'message' => 'Meeting marked as completed!']);
    }

    public function deleteMeeting(int $id)
    {
        CrmMeeting::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Meeting deleted successfully!']);
    }

    protected function notifyAssignedTaskOwner(CrmTask $task, ?int $previousAssignedTo = null): void
    {
        if (!$task->assigned_to || $task->assigned_to === auth()->id() || $task->assigned_to === $previousAssignedTo) {
            return;
        }

        $assignedUser = User::find($task->assigned_to);

        if (!$assignedUser) {
            return;
        }

        $assignedUser->notify(new RecordAssignedNotification(
            'Task',
            $task->id,
            'Task Assigned',
            'A task has been assigned to you: ' . ($task->subject ?: 'Task #' . $task->id),
            $this->taskUrl($task),
            auth()->id()
        ));
    }

    protected function taskUrl(CrmTask $task): string
    {
        return match ($task->entity_type) {
            'lead' => route('admin.lead.show', $task->entity_id),
            'contact' => route('admin.contact.show', $task->entity_id),
            'account' => route('admin.account.show', $task->entity_id),
            default => route('admin.tasks.index'),
        };
    }
}
