<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{

	public $timestamps = false;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'refund_from',
        'amount',
        'invoice_details_id'
        
   ];

   	public function invoiceDetail()
    {
        return $this->belongsTo('App\Modules\Student\Models\InvoiceDetail','invoice_details_id');
    }

}
