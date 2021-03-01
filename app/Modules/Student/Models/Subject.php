<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    public $timestamps = false;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function student()
    {
        return $this->belongsToMany('App\Modules\Student\Models\Subject', 'students_has_subjects', 'subjects_id','students_id');
    }

    public function subject()
    {
        return $this->hasmany('App\Modules\Student\Models\Batch');
    }

    // public function teacher()
    // {
    //     return $this->hasmany('App\Modules\Teacher\Models\TeacherDetail');
    // }

    public function invoiceDetail()
    {
        return $this->hasmany('App\Modules\Student\Models\InvoiceDetail');
    }
}
