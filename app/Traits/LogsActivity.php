<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', 'Created new ' . class_basename($model), [
                'attributes' => $model->getAttributes()
            ]);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes)) {
                $oldAttributes = [];
                foreach (array_keys($changes) as $key) {
                    $oldAttributes[$key] = $model->getOriginal($key);
                }
                
                $model->logActivity('updated', 'Updated ' . class_basename($model), [
                    'old' => $oldAttributes,
                    'attributes' => $changes
                ]);
            }
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', 'Deleted ' . class_basename($model), [
                'attributes' => $model->getAttributes()
            ]);
        });
    }

    public function logActivity($action, $description, $properties = [])
    {
        ActivityLog::create([
            'log_name' => $action,
            'description' => $description,
            'subject_type' => get_class($this),
            'subject_id' => $this->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}