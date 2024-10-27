<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:edit roles', only: ['edit']),
            new Middleware('permission:create roles', only: ['create']),
            new Middleware('permission:delete roles', only: ['delete']),
        ];
    }

    // This method will show roles page
    public function index(){

        $roles = Role::orderBy('created_at','ASC')->paginate(10);
        return view('roles.list', compact('roles'));
    }

    // This method will create role page
    public function create(){

        $permissions = Permission::orderBy('name','ASC')->get();
        return view('roles.create', compact('permissions'));
    }

    // This method will insert role in DB
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',
            // Add any validation for permissions if necessary
        ]);

        if ($validator->passes()) {
            $role = Role::create(['name' => $request->name]);

            if (!empty($request->permission)) {
                foreach ($request->permission as $name) {
                    $role->givePermissionTo($name);
                }
            }

            return redirect()->route('roles.index')->with('success', 'Role added successfully');
        } else {
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){

        $role = Role::findOrfail($id);
        $hasPermissions = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name','ASC')->get();

        return view('roles.edit', compact('role','hasPermissions','permissions'));
    }

    public function update(Request $request, $id){
        // dd($request);
        $role = Role::findOrfail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,'.$id.',id',
        ]);

        if ($validator->passes()) {

            $role->name = $request->name;
            $role->save();

            if (!empty($request->permission)) {
                $role->syncPermissions($request->permission);
            } else{
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.index')->with('success', 'Role updated successfully');
        } else {
            return redirect()->route('roles.edit', $id)->withInput()->withErrors($validator);
        }
    }

    public function destroy($id){

        $role = Role::find($id);
        $role->delete();

        return back()->with('success','Role deleted successfully');
    }
}