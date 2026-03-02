<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use SoftDeletes;

    protected $table = 'job_applications';

    protected $fillable = [
        'job_id',
        'candidate_id',
        'status',
        'cover_note',
        'expected_salary',
        'notice_period_days',
        'current_ctc',
        'expected_ctc',
        'referral_name',
        'referral_employee_id',
        'resume_path',
        'cover_letter_path',
        'portfolio_link',
        'linkedin_profile',
        'github_profile',
        'additional_documents',
        'application_source',
        'ip_address',
        'user_agent',
        'viewed_at',
        'shortlisted_at',
        'rejected_at',
        'rejection_reason',
        'notes'
    ];

    protected $casts = [
        'additional_documents' => 'array',
        'expected_salary' => 'decimal:2',
        'current_ctc' => 'decimal:2',
        'expected_ctc' => 'decimal:2',
        'notice_period_days' => 'integer',
        'viewed_at' => 'datetime',
        'shortlisted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the job this application belongs to
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the candidate this application belongs to
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the referring employee if any
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referral_employee_id');
    }

    /**
     * Scope for applications by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for applications by job
     */
    public function scopeByJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope for applications by candidate
     */
    public function scopeByCandidate($query, $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'applied');
    }

    /**
     * Scope for shortlisted applications
     */
    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for applications applied today
     */
    public function scopeAppliedToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for applications applied this week
     */
    public function scopeAppliedThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for applications by date range
     */
    public function scopeAppliedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if application is pending
     */
    public function isPending()
    {
        return $this->status === 'applied';
    }

    /**
     * Check if application is shortlisted
     */
    public function isShortlisted()
    {
        return $this->status === 'shortlisted';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'applied' => 'badge-secondary',
            'shortlisted' => 'badge-primary',
            'rejected' => 'badge-danger',
            'withdrawn' => 'badge-dark'
        ];
        return $classes[$this->status] ?? 'badge-secondary';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get application age in days
     */
    public function getApplicationAgeAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get days since viewed
     */
    public function getDaysSinceViewedAttribute()
    {
        return $this->viewed_at ? $this->viewed_at->diffInDays(now()) : null;
    }

    /**
     * Mark as viewed
     */
    public function markAsViewed()
    {
        $this->update(['viewed_at' => now()]);
    }

    /**
     * Shortlist application
     */
    public function shortlist()
    {
        $this->update([
            'status' => 'shortlisted',
            'shortlisted_at' => now()
        ]);
    }

    /**
     * Reject application
     */
    public function reject($reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason
        ]);
    }

    /**
     * Withdraw application
     */
    public function withdraw()
    {
        $this->update(['status' => 'withdrawn']);
    }

    /**
     * Get application summary
     */
    public function getSummaryAttribute()
    {
        return $this->candidate->full_name . ' - ' . $this->job->title . ' (' . $this->status_label . ')';
    }

    /**
     * Get resume url
     */
    public function getResumeUrlAttribute()
    {
        return $this->resume_path ? asset('storage/' . $this->resume_path) : null;
    }

    /**
     * Get cover letter url
     */
    public function getCoverLetterUrlAttribute()
    {
        return $this->cover_letter_path ? asset('storage/' . $this->cover_letter_path) : null;
    }

    /**
     * Check if has resume
     */
    public function getHasResumeAttribute()
    {
        return !is_null($this->resume_path);
    }

    /**
     * Get application timeline
     */
    public function getTimelineAttribute()
    {
        $timeline = [
            ['date' => $this->created_at, 'action' => 'Applied', 'status' => 'applied']
        ];

        if ($this->shortlisted_at) {
            $timeline[] = ['date' => $this->shortlisted_at, 'action' => 'Shortlisted', 'status' => 'shortlisted'];
        }

        if ($this->rejected_at) {
            $timeline[] = ['date' => $this->rejected_at, 'action' => 'Rejected', 'status' => 'rejected'];
        }

        return collect($timeline)->sortByDesc('date')->values();
    }
}