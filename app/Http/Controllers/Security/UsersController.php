<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use App\Security\Users;
use Validator;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new Users();
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
        $model = new Users();
        if ($model->validate($request->all())) {
            $model = Users::create($request->all());
            return response()->json($model);
        } else {
            return response()->json($model->errors, 500);
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
        $model = Users::find($id);
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
        $model = new Users();
        if ($model->validate($request->except(['id']))) {
            unset($model);
            $model = Users::find($id);
            $req = $request->except(['id']);
            $model->fill($req);
            $model->save();
            return response()->json($model);
        } else {
            return response()->json($model->errors, 500);
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
       if(Users::find($id)->delete()){
		   return response()->json('Deleted');
	   }else{
		   return response()->json('Internal Error', 500);
	   }
		
    }
    
    public function getAllRolesByUser($userId){
        $user = new Users();
        if(($roles = $user->getAllRolesByUser($userId))!=false){
            return response()->json($roles);
        }else{
            return response()->json($this->internalError(),500);
        }
    }
    public function saveRolesOnUser(Request $request){
        $userId = $request->input('user_id');
        $roles = $request->except('user_id');
        if(!empty($userId)){
            $user = new Users();
            if(count($roles)>0){
                $rls = array_keys($roles);
                if($user->attachAllRoles($userId,$rls)){
                    return response()->json($this->successMessage('Roles changed to the user.'));
                }else{
                    return response()->json($this->internalError(),500);
                }
            }else{
                if($user->attachAllRoles($userId,[])){
                    return response()->json('Roles cleared from the user.');
                }else{
                    return response()->json($this->internalError(),500);
                }
            }
        }else{
            return response()->json($this->errorMessage('User not specified'),500);
        }
    }
    
    function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'password' => 'required|confirmed',
            ]);
        if ($validator->fails()) {
            return response()->json($this->errorMessage($validator->errors()),500);
        }else{
            $user = new \App\User();
            $userId = $request->input('user_id');
            $password = $request->input('password');
            if($user->resetUserPassword($userId,$password)){
                return response()->json($this->successMessage('Password changed..'));
            }else{
                return response()->json($this->errorMessage('Intrnal Error'),500);
            }
        }
    }
}

