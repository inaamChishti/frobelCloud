<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = ['book_id', 'quantity', 'amount_paid', 'payment_receipt','branch_name','branch_id'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
