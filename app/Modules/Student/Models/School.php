<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'schools';

    public $timestamps = false;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'address'
    ];
    
    public function student()
    {
        return $this->hasmany('App\Modules\Student\Models\Student');
    }
}
