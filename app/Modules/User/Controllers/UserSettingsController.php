<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\Role;
Use App\Modules\User\Models\Permission;
use App\Modules\User\Models\PermissionRole;

use Datatables;




use App\Modules\Student\Models\InvoiceMaster;
use Carbon\Carbon;




class UserSettingsController extends Controller {

    //Roles
    public function allRoles() {
        $getPermissions = Permission::all();
        return view('User::roles')->with("getPermissions", $getPermissions);
    }

    public function getRoles() {
        $roles = Role::all();
        return Datatables::of($roles)
                        ->addColumn('Link', function($roles) {
                            return '<a href="' . url('/roles') . '/' . $roles->id . '/edit' . '"' . 'class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        })
                        ->editColumn('id', '{{$id}}')
                        ->setRowId('id')
                        ->make(true);
    }

    public function addRolesProcess(\App\Http\Requests\RulesRequest $request) {
        $roles = new Role();
        $roles->name = $request->input('rname');
        $roles->display_name = $request->input('dname'); // optional
        $roles->description = $request->input('description'); // optional
        $roles->save();

        $permissions = $request->get('permi');
        foreach ($permissions as $perm) {
            $permissionSearch = Permission::find($perm);
            $roles->attachPermission($permissionSearch->id);
        }

        return redirect('roles');
    }

    public function editRoles($id) {
        $editRole = Role::where('id', '=', $id)->get();
        $getPermissions = Permission::leftJoin('permission_role', function($join) use($id) {
                    $join->on('permission_role.permission_id', '=', 'permissions.id')->where('permission_role.role_id', '=', $id);
                })->get();

        return view('User::roles')->with('getPermissions', $getPermissions)->with('editRole', $editRole);
    }

    public function editRolesProcess(\App\Http\Requests\RulesRequest $request) {
        $roleID = $request->input('rid');
        $roles = Role::findOrFail($roleID);

        $roles->name = $request->input('rname');
        $roles->display_name = $request->input('dname'); // optional
        $roles->description = $request->input('description'); // optional

        $roles->save();

        $permission = $request->get('permi');
        $getPermissionRole = PermissionRole::where('role_id', '=', $roleID)->delete();
        
        foreach ($permission as $perm) {
            $permissionSearch = Permission::find($perm);
            $roles->attachPermission($permissionSearch->id);
        }

        return redirect('roles/' . $roleID . '/edit');
    }

    //Permissions
    public function allPermissions() {
        return view('User::permission');
    }

    public function getPermissions() {
        $permissions = Permission::all();
        return Datatables::of($permissions)
                        ->addColumn('Link', function($permissions) {
                            return '<a href="' . url('/permission') . '/' . $permissions->id . '/edit' . '"' . 'class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        })
                        ->editColumn('id', '{{$id}}')
                        ->setRowId('id')
                        ->make(true);
    }

    public function addPermissionsProcess(\App\Http\Requests\PermissionRequest $request) {
        $owner = new Permission();
        $owner->name = $request->input('pname');
        $owner->display_name = $request->input('dname'); // optional
        $owner->description = $request->input('description'); // optional
        $owner->save();

        return redirect('permissions');
    }
    
    public function editPermission($id) {
        $editPermission = Permission::where('id', '=', $id)->get();
        return view('User::permission')->with('editPermission', $editPermission);
    }
    
    public function editPermissionProcess(\App\Http\Requests\PermissionRequest $request){
        $permissionId = $request->input('pid');
        $permission = Permission::findOrFail($permissionId);
        
        $permission->name = $request->input('pname');
        $permission->display_name = $request->input('dname'); // optional
        $permission->description = $request->input('description'); // optional

        $permission->save();
        
        return redirect('permission/' . $permissionId . '/edit');
    }

    // public function invoice_correction() {

    //     $invoice_data = InvoiceMaster::all();
        
    //     for ($i=0; $i < count($invoice_data); $i++) {
    //         $date = Carbon::createFromFormat('d/m/Y', $invoice_data[$i]->payment_date)->format('Y-m-d ');
    //         $date = Carbon::parse($date);
    //         $formated_serial_number = $date->year . "" . sprintf('%02d', $date->month) . "" . sprintf('%02d', $date->day) . "" . 
    //                                     substr($invoice_data[$i]->serial_number, -4);
    //         $invoice_data[$i]->serial_number = $formated_serial_number;
    //         $invoice_data[$i]->save();
    //         // return $invoice_data[$i];
    //     }
    //     return 'invoice_correction Works'; 

    // }

    

}
