<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use Illuminate\Http\Request;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view permissions', only: ['index']),
            new Middleware('permission:edit permissions', only: ['edit']),
            new Middleware('permission:create permissions', only: ['create']),
            new Middleware('permission:delete permissions', only: ['delete']),
        ];
    }

    public function index(){

        $permissions = Permission::orderBy('created_at','DESC')->paginate(10);
        return view('permissions.list', compact('permissions'));
    }

    // this method will show create permission page
    public function create(){
        return view('permissions.create');
    }

    // this method will insert permission in db
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {

            Permission::create(['name' => $request->name]);
            return redirect()->route('permissions.index')->with('success','Permission added successfully');

        }else {
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    // this method will edit permission page
    public function edit($id){

        $permission = Permission::findOrfail($id);
        return view('permissions.edit', compact('permission'));
    }

    // this method will update permission page
    public function update(Request $request, $id){

        $permission = Permission::findOrfail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:permissions,name,'.$id.',id'
        ]);

        if ($validator->passes()) {

            $permission->name = $request->name;
            $permission->save();

            return redirect()->route('permissions.index')->with('success','Permission updated successfully');

        }else {
            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }
    }

    // this method will delete permission in db
    public function destroy($id){

        $permission = Permission::find($id);
        $permission->delete();
        return redirect()->back()->with('success','Permission deleted successfully');
    }
}