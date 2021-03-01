<?php

namespace App\Modules\User\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {

    protected $table = 'roles';
    protected $fillable = ['name', 'display_name', 'description'];
    
    public function role_user(){
        return $this->hasOne('App\Modules\User\Models\RoleUser');
    }

}
