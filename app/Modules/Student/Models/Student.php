<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'students';

    // public $timestamps = false;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'fathers_name',
        'mothers_name',
        'student_phone_number',
        'guardian_phone_number',
        // 'schools_id',
        'batch_id',
        // 'batch_types_id',
        'students_image',
        'student_email',
        'student_permanent_id',
        'driving_license_number'
    ];
    
    public function school()
    {
        return $this->belongsTo('App\Modules\Student\Models\School', 'schools_id');
    }

    // public function batchType()
    // {
    //     return $this->belongsTo('App\Modules\Student\Models\BatchType', 'batch_types_id');
    // }
    public function batch_type()
    {
        return $this->belongsTo('App\Modules\Student\Models\BatchType', 'batch_types_id');
    }

    public function batch()
    {
        return $this->belongsToMany('App\Modules\Student\Models\Batch', 'batch_has_students', 'students_id', 'batch_id')->withPivot('last_paid_date', 'joining_date');
    }

    public function subject()
    {
        return $this->belongsToMany('App\Modules\Student\Models\Subject', 'students_has_subjects', 'students_id', 'subjects_id');
    }

    public function invoiceMaster()
    {
        return $this->hasmany('App\Modules\Student\Models\InvoiceMaster', 'students_id');
    }

    public function other_payment_master()
    {
        return $this->hasmany('App\Modules\Student\Models\OtherPaymentMaster', 'students_id');
    }
}
