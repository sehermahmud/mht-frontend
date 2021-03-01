<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class BatchHasStudent extends Model
{

    public $timestamps = false;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'batch_id',
        'students_id',
        'last_paid_date',
        'joining_date'
    ];

    // public function setLastPaidDateAttribute($value) {
    //     $this->attributes['last_paid_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateTimeString();
    // }

    // public function setJoiningDateAttribute($value) {
    //     $this->attributes['joining_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateTimeString();
    // }

    public function getLastPaidDateAttribute($value) {
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }

    // public function getJoiningDateAttribute($value) {
    //     return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    // }
    
}
