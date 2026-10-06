<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class Payment extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'payment';
    protected $primaryKey = 'paymentid';
    public $timestamps = false;

    protected $fillable = [
        'deleted_by',
        'cash_payment',
        'card_payment',
        'adjustment',
        'bank_transfer',
        'paymentfamilyid',
        'paymentfrom',
        'paymentto',
        'paymentdate',
        'to',
        'paid',
        'paid_up_to_date',
        'last_payment_date',
        'package',
        'collector',
        'balance',
        'comment',
        'payment_method',
        'payment_detail',
        'created_at',
        'updated_at',
        'receipt_no',
        'branch_name',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "Payment has been {$eventName}")
            ->logOnly([
                'paymentfamilyid',
                'paymentfrom',
                'paymentto',
                'paid',
                'paid_up_to_date',
                'last_payment_date',
                'package',
                'collector',
                'balance',
                'comment',
                'payment_method',
                'payment_detail',
                'branch_name',
                'branch_id'
            ])
            ->useLogName('Payment');
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
