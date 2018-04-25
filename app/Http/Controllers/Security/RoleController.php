<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Security\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new Role();
        $models = $model->retriveData($input);
        return response()->json($models);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new Role();
        if ($model->validate($request->all())) {
            $model = Role::create($request->all());
            return response()->json($model);
        } else {
            return response()->json($this->errorMessage($model->errors),500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Role::find($id);
        return response()->json($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = new Role();
        $model->rules['name'] = 'required|unique:roles,name,'.$id;
        if ($model->validate($request->except(['id']))) {
            unset($model);
            $model = Role::find($id);
            $req = $request->except(['id']);
            $model->fill($req);
            $model->save();
            return response()->json($model);
        } else {
            return response()->json($this->errorMessage($model->errors), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       if(Role::find($id)->delete()){
		   return response()->json('Deleted');
	   }else{
		   return response()->json($this->internalError(), 500);
	   }
		
    }
    
    public function getAllPermissionsByRole($roleId){
        $role = new Role();
        if(($pms = $role->getAllPermissionsByRole($roleId))!=false){
            return response()->json($pms);
        }else{
            return response()->json($this->internalError(),500);
        }
    }
    public function savePermissionsOnRole(Request $request){
        $roleId = $request->input('role_id');
        $permissions = $request->except('role_id');
        if(!empty($roleId)){
            $role = new Role();
            if(count($permissions)>0){
                $perms = array_keys($permissions);
                if($role->attachAllPermissions($roleId,$perms)){
                    return response()->json($this->successMessage('Permissions changed to the role.'));
                }else{
                    return response()->json($this->internalError(),500);
                }
            }else{
                if($role->attachAllPermissions($roleId,[])){
                    return response()->json('Permissions cleared from the role.');
                }else{
                    return response()->json($this->internalError(),500);
                }
            }
        }else{
            return response()->json($this->errorMessage('Role not specified'),500);
        }
    }
}

