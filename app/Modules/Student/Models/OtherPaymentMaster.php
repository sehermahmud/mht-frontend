<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class OtherPaymentMaster extends Model
{

    public $timestamps = true;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'payment_date',
        'created_at',
        'updated_at',
        'price',
        'serial_number',
        'students_id',
        'other_payment_type_id',
        'note'
    ];

    public function student() {
        return $this->belongsTo('App\Modules\Student\Models\Student', 'students_id');
    }

    // public function other_payment_detail()
    // {
    //     return $this->hasmany('App\Modules\Student\Models\OtherPaymentDetail', 'other_payment_masters_id');
    // }

    public function other_payment_type()
    {
        return $this->belongsTo('App\Modules\Student\Models\OtherPaymentType', 'other_payment_type_id');
    }

    public function setPaymentDateAttribute($value) {
        $this->attributes['payment_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateTimeString();
    }

    public function getPaymentDateAttribute($value) {
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    
}
