<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
   protected $fillable = [
      'customer_name',
      'cheque_number',
      'bank_name',
      'amount',
      'cheque_date',
      'cashable_date',
      'reminder_date',
      'status',
   ];

   public function sale()
   {
      return $this->belongsTo(Sale::class);
   }
}
