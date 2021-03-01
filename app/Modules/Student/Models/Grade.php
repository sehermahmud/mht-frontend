<?php

namespace App\Modules\Student\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grades';

    public $timestamps = false;


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
    
    public function batch()
    {
        return $this->hasmany('App\Modules\Student\Models\Batch');
    }

}
