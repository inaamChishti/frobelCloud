<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'role',
        'branch_name',
        'visible_password',
        'login_session',
        'usertype',
        'is_main_branch',
        'is_super_user',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Branch User has been {$eventName}")
            ->logOnly([
                'name',
                'email',
                'usertype',
                'branch_id',
                'branch_name',
                'role',
                'is_main_branch',
            ])
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['login_session', 'password', 'visible_password', 'remember_token'])
            ->useLogName('BranchUser');
    }

    public function shouldLogActivity(string $eventName): bool
    {
        // Only log activities for branch users (not super_admin)
        if ($this->role === 'super_admin' || empty($this->branch_id)) {
            return false;
        }
        return true;
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

    // Define the relationship with AccessPermission
    public function permissions()
    {
        return $this->hasOne(AccessPermission::class, 'user_id', 'id');
    }
}
