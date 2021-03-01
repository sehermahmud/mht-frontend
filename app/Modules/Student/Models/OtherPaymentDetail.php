<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class OtherPaymentDetail extends Model
{

    public $timestamps = false;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'other_payment_type_id',
        'other_payment_masters_id',
        'price',
        'note'
    ];

    public function other_payment_master()
    {
        return $this->belongsTo('App\Modules\Student\Models\OtherPaymentMaster', 'other_payment_masters_id');
    }

    public function other_payment_type()
    {
        return $this->belongsTo('App\Modules\Student\Models\OtherPaymentType', 'other_payment_type_id');
    }

}
