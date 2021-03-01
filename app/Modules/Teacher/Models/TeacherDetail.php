<?php

namespace App\Modules\Teacher\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherDetail extends Model
{
    protected $table = 'teacher_details';

    public $timestamps = false;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'users_id',
        'teacher_percentage'
    ];

    public function user()
    {
        return $this->belongsTo('App\Modules\User\Models\User', 'users_id');
    }

    // public function subject()
    // {
    //     return $this->belongsTo('App\Modules\Student\Models\Subject', 'subjects_id');
    // }

    public function batch()
    {
        return $this->hasmany('App\Modules\Student\Models\Batch', 'teacher_details_id');
    }

    public function invoiceDetail()
    {
        return $this->hasManyThrough('App\Modules\Student\Models\InvoiceDetail', 'App\Modules\Student\Models\Batch', 'teacher_details_id', 'batch_id');
    }

}
