<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model {

    protected $table = 'role_user';
    
    public function user(){
        return $this->belongsTo('App\Modules\User\Models\User');
    }
    
    public function role(){
        return $this->belongsTo('App\Modules\User\Models\Role');
    }

}
