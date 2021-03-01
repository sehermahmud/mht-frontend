<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'batch';

    public $timestamps = false;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'batch_types_id',
        'grades_id',
        'teacher_details_id',
        'teacher_details_users_id',
        'start_date',
        'end_date',
        'schedule',
        'subjects_id',
        'expected_students' 
    ];
    
    public function student()
    {
        return $this->belongsToMany('App\Modules\Student\Models\Student', 'batch_has_students', 'batch_id', 'students_id')->withPivot('last_paid_date', 'joining_date');
    }

    public function batchType()
    {
        return $this->belongsTo('App\Modules\Student\Models\BatchType', 'batch_types_id');
    }

    public function grade()
    {
        return $this->belongsTo('App\Modules\Student\Models\Grade','grades_id');
    }

    public function subject()
    {
        return $this->belongsToMany('App\Modules\Student\Models\Subject', 'students_has_subjects', 'students_id', 'subjects_id');
    }

    public function teacherDetail()
    {
        return $this->belongsTo('App\Modules\Teacher\Models\TeacherDetail','teacher_details_id');
    }

    public function invoiceDetail()
    {
        return $this->hasmany('App\Modules\Student\Models\InvoiceDetail');
    }

    public function setStartDateAttribute($value) {
        $this->attributes['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateString();
    }

    public function setEndDateAttribute($value) {
        $this->attributes['end_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateString();
    }

    public function getStartDateAttribute($value) {
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    
    public function getEndDateAttribute($value) {
        return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
}
