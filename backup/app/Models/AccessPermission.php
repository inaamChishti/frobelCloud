<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class AccessPermission extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'user_id',
        'page_name',
        'can_access',
        'query',
        'branch_name',
        'branch_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Access Permission has been {$eventName}")
            ->logOnly([
                'user_id',
                'page_name',
                'can_access',
                'branch_id',
                'branch_name',
            ])
            ->logOnlyDirty()
            ->useLogName('AccessPermission');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->causer_id = auth()->user() ? auth()->id() : 0;

        // Fetch branch details based on session branch_id or model's branch_id
        $branchId = session('branch_id') ?? $this->branch_id;
        $branch = User::where('branch_id', $branchId)
            ->where('is_main_branch', 1)
            ->first();

        // Set branch_name and branch_id directly on the activity
        $activity->branch_id = $branch ? $branch->branch_id : ($this->branch_id ?? null);
        $activity->branch_name = $branch ? $branch->branch_name : ($this->branch_name ?? null);

        // Also add them to properties if needed
        try {
            if (isset($activity->properties) && $activity->properties) {
                $activity->properties = $activity->properties->merge([
                    'branch_id' => $branch ? $branch->branch_id : ($this->branch_id ?? null),
                    'branch_name' => $branch ? $branch->branch_name : ($this->branch_name ?? null),
                ]);
            }
        } catch (\Exception $e) {
            // Properties might not be available in all contexts, ignore
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
