<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class AssignBook extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'family_id',
        'student_name',
        'book',
        'subject',
        'teacher',
        'paid_status',
        'price',
        'branch_name',
        'branch_id',
        'payment_method'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Assign Book has been {$eventName}")
            ->logOnly([
                'family_id',
                'student_name',
                'book',
                'subject',
                'teacher',
                'paid_status',
                'price',
                'payment_method',
                'branch_id',
                'branch_name',
            ])
            ->logOnlyDirty()
            ->useLogName('AssignBook');
    }

    public function tapActivity(Activity $activity)
    {
        $activity->causer_id = auth()->user() ? auth()->id() : 0;

        // Fetch branch details based on session branch_id
        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        // Set branch_name and branch_id directly on the activity
        $activity->branch_id = $branch ? $branch->branch_id : ($this->branch_id ?? null);
        $activity->branch_name = $branch ? $branch->branch_name : ($this->branch_name ?? null);

        // Capture URL/Route information for tracking
        $urlInfo = [];
        if (request()) {
            $urlInfo['url'] = request()->fullUrl();
            $urlInfo['route_name'] = request()->route() ? request()->route()->getName() : null;
            $urlInfo['route_path'] = request()->route() ? request()->route()->uri() : null;
            $urlInfo['method'] = request()->method();
            $urlInfo['referer'] = request()->header('referer');
        }

        // Also add them to properties if needed
        try {
            if (isset($activity->properties) && $activity->properties) {
                $activity->properties = $activity->properties->merge([
                    'branch_id' => $branch ? $branch->branch_id : ($this->branch_id ?? null),
                    'branch_name' => $branch ? $branch->branch_name : ($this->branch_name ?? null),
                    'url_info' => $urlInfo,
                ]);
            }
        } catch (\Exception $e) {
            // Properties might not be available in all contexts, ignore
        }
    }
}
