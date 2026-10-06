<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class Guardian extends Model
{
    use HasFactory, LogsActivity;
    public $timestamps = false;
    protected $primaryKey = 'Guardianid';
    protected $table = 'guardian';

    protected $fillable = [
        'guardianname',
        'guardianaddress',
        'guardiantel',
        'parent_relationship',
        'guardianmob',
        'address_line_2',
        'city',
        'countyStateRegion',
        'zIPCode',
        'country',
        'branch_name',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Guardian has been {$eventName}")
            ->logOnly([
                'guardianname',
                'guardianaddress',
                'guardiantel',
                'parent_relationship',
                'guardianmob',
                'address_line_2',
                'city',
                'countyStateRegion',
                'zIPCode',
                'country',
                'branch_name',
                'branch_id'
            ])
            ->useLogName('Guardian');
    }

   public function tapActivity(Activity $activity)
{
    $activity->causer_id = auth()->user() ? auth()->id() : 0;

    // Fetch branch details based on session branch_id
    $branch = User::where('branch_id', session('branch_id'))
        ->where('is_main_branch', 1)
        ->first();

    // Set branch_name and branch_id directly on the activity
    $activity->branch_id = $branch ? $branch->branch_id : null;
    $activity->branch_name = $branch ? $branch->branch_name : null;

    // Also add them to properties if needed
    $activity->properties = $activity->properties->merge([
        'branch_id' => $branch ? $branch->branch_id : null,
        'branch_name' => $branch ? $branch->branch_name : null,
    ]);
}

public function students()
{
    return $this->hasMany(Student::class, 'guardianid', 'Guardianid');
}
}
