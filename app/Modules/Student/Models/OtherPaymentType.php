<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class OtherPaymentType extends Model
{

    public $timestamps = false;

    protected $table = 'other_payment_type';


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'description'
    ];

    public function other_payment_master()
    {
        return $this->hasmany('App\Modules\Student\Models\OtherPaymentMaster', 'other_payment_type_id');
    }

}
