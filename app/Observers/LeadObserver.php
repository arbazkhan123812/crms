<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class LeadObserver
{
    // Temporary property to store old values
    protected $oldAttributes = [];

    /**
     * Handle the Lead "creating" event.
     */
    public function creating(Lead $lead): void
    {
        Log::info('Creating lead: ' . $lead->first_name);
    }

    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        $this->logActivity('created', 'Lead created successfully', $lead, [
            'lead_id' => $lead->id,
            'lead_name' => $lead->first_name . ' ' . $lead->last_name,
            'company' => $lead->company,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'mobile' => $lead->mobile,
            'lead_source' => $lead->lead_source,
            'lead_status' => $lead->lead_status,
            'annual_revenue' => $lead->annual_revenue,
            'no_of_employees' => $lead->no_of_employees,
            'created_data' => $lead->getAttributes()
        ]);
    }

    /**
     * Handle the Lead "updating" event.
     */
    public function updating(Lead $lead): void
    {
        // Store old values before update
        $this->oldAttributes = $lead->getOriginal();
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        $changes = $lead->getChanges();
        
        if (!empty($changes)) {
            $oldData = [];
            foreach (array_keys($changes) as $field) {
                $oldData[$field] = $this->oldAttributes[$field] ?? null;
            }
            
            $this->logActivity('updated', 'Lead updated successfully', $lead, [
                'lead_id' => $lead->id,
                'lead_name' => $lead->first_name . ' ' . $lead->last_name,
                'old_values' => $oldData,
                'new_values' => $changes,
                'changed_fields' => array_keys($changes)
            ]);
        }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        $this->logActivity('deleted', 'Lead deleted successfully', $lead, [
            'lead_id' => $lead->id,
            'lead_name' => $lead->first_name . ' ' . $lead->last_name,
            'company' => $lead->company,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'mobile' => $lead->mobile,
            'lead_source' => $lead->lead_source,
            'lead_status' => $lead->lead_status,
            'annual_revenue' => $lead->annual_revenue,
            'no_of_employees' => $lead->no_of_employees,
            'deleted_data' => $lead->getAttributes()
        ]);
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        $this->logActivity('restored', 'Lead restored successfully', $lead, [
            'lead_id' => $lead->id,
            'lead_name' => $lead->first_name . ' ' . $lead->last_name,
            'restored_data' => $lead->getAttributes()
        ]);
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        $this->logActivity('force_deleted', 'Lead permanently deleted', $lead, [
            'lead_id' => $lead->id,
            'lead_name' => $lead->first_name . ' ' . $lead->last_name,
            'deleted_data' => $lead->getAttributes()
        ]);
    }

    /**
     * Log activity helper method
     */
    protected function logActivity($action, $description, $lead, $properties = [])
    {
        try {
            ActivityLog::create([
                'log_name' => $action,
                'description' => $description . ': ' . ($lead->first_name ?? '') . ' ' . ($lead->last_name ?? ''),
                'subject_type' => Lead::class,
                'subject_id' => $lead->id,
                'causer_type' => auth()->check() ? get_class(auth()->user()) : null,
                'causer_id' => auth()->id(),
                'properties' => $properties,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent()
            ]);
        } catch (\Exception $e) {
            // Log error but don't stop the main operation
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}