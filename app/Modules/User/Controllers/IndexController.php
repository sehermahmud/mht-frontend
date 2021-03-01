<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use App\Modules\User\Models\User;
use App\Modules\User\Models\Role;
use App\Modules\User\Models\RoleUser;
use App\Modules\Directory\Models\MembersDetail;
use Lang;
use Datatables;
use Entrust;

/**
 * IndexController
 *
 * Controller to all the properties uith user module.
 * login, user crud, listing and more
 */
class IndexController extends Controller {

    public function index() {

        if(Auth::check()) {
            redirect("/dashboard");
        }
        else {
            return view('User::login');
        }

    }

    //Login Module
    public function loginUser(\App\Http\Requests\LoginRequest $request) {
        $userdata = array(
            'email' => $request->input('username'),
            'password' => $request->input('password')
        );
        if (Auth::attempt($userdata)) {
            return redirect('dashboard');
        } else {
            return redirect('login')->withErrors([$request->input('username') => $this->getFailedLoginMessage()]);
        }
    }

    protected function getFailedLoginMessage() {
        return Lang::has('auth.failed') ? Lang::get('auth.failed') : 'wrong username / password';
    }

    public function logoutUser() {
        Auth::logout();
        return redirect('login');
    }

    //User Module
    public function allUsers() {
        if (Auth::check()) {
            return view('User::all_users');
        }
        else {
            return view('User::login');
        }
        
    }

    public function getUsers() {
        //$users = User::all();
        if (Auth::check()) {
        $users = RoleUser::join('users', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->select(['users.id as id', 'users.name', 'users.email', 'roles.display_name']);
        return Datatables::of($users)
                        ->addColumn('Link', function ($users) {
                            // if(Entrust::can('user.update') && Entrust::can('user.delete')) {
                            if(true) {
                            return '<a href="' . url('/users') . '/' . $users->id . '/edit' . '"' . 'class="btn bg-blue margin"><i class="glyphicon glyphicon-edit"></i> Edit</a>' . '&nbsp &nbsp &nbsp'.
                                '<a class="btn btn bg-red margin" id="'.$users->id.'" data-toggle="modal" data-target="#confirm_delete">
                                <i class="glyphicon glyphicon-trash"></i> Delete
                                </a>';
                            }else {
                                return 'N/A';
                            }
                        })
                        ->editColumn('id', '{{$id}}')
                        ->setRowId('id')
                        ->make(true);
        }
        else {
            return view('User::login');
        }
    }

    public function createUsers() {
        if (Auth::check()) {
            $getRoles = Role::all();
            return view('User::create_users')->with('getRoles', $getRoles);
        }
        else {
            return view('User::login');
        }
    }

    public function createUsersProcess(\App\Http\Requests\UserRequest $request) {

        if (Auth::check()) {
            $addUsers = new User();

            $addUsers->name = $request->input('fullname');
            $addUsers->email = $request->input('uemail');
            $addUsers->password = bcrypt($request->input('upassword'));

            $addUsers->save();

            $userID = $addUsers->id;
            $roleID = $request->input('uroles');

            $user = User::find($userID);
            $role = Role::where('id', '=', $roleID)->get()->first();
            $user->attachRole($role);

            return redirect('allusers');
        }
        else {
            return view('User::login');
        }
    }

    public function editUsers($id) {
        if (Auth::check()) {
            $editUser = User::where('id', '=', $id)->get();
            
            $getRoles = Role::leftJoin('role_user', function($join) use ($id) {
                        $join->on('role_user.role_id', '=', 'roles.id')->where('role_user.user_id', '=', $id);
                    })->get();

            return view('User::edit_users')->with('getRoles', $getRoles)->with('editUser', $editUser);
        }
        else {
            return view('User::login');
        }
    }

    public function editUsersProcess(\App\Http\Requests\UserUpdateRequest $request) {
        if (Auth::check()) {
            $userID = $request->input('uid');        
            $password = $request->input('upassword');
            
            $addUsers = User::findOrFail($userID);

            $addUsers->name = $request->input('fullname');
            $addUsers->email = $request->input('uemail');

            if (isset($password) && $password != '') {
                $addUsers->password = bcrypt($password);
            }

            $addUsers->save();
            
            $dRoleUser = RoleUser::where('user_id', '=', $userID)->delete();

            $roleID = $request->input('uroles');

            $user = User::find($userID);
            $role = Role::where('id', '=', $roleID)->get()->first();
            $user->attachRole($role);

            return redirect('users/'.$userID.'/edit');
        }
        else {
            return view('User::login');
        }
    }

    /****************
    * Delete a User *
    *****************/

    public function deleteUser($id) {
        if (Auth::check()) {
            $user = User::find($id);
            $role_user = RoleUser::where('user_id',$id)->first();
            if ($role_user->role_id == 2) {
                $user->teacher_detail()->delete();
                $user->delete();
            }
            else {
                $user->delete();
            }
            
            return redirect('allusers');
        }
        else {
            return view('User::login');
        }
    }

}
