<?php

namespace App\Security;

use App\BaseModel;
use DB;
use App\User;

class Users extends BaseModel
{
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['name','email','orgid','empid'];
    public $searchable = ['id','name','email'];
    
    public $rules = [
        'orgid'=>'integer',
        'empid'=>'integer',
          //'name'=> 'required',
          //'email'=> 'required|email|unique:users,email',
          'password'=> 'required|confirmed',
     ];
    
    protected $hidden = [
        'password', 'remember_token','created_at','updated_at'
    ];
    
    public function attachAllRoles($userId,$roles){
        $user = User::find($userId);
        if($user->roles()->sync($roles)){
            return true;
        }else{
            return false;
        }
    }
    
    public function getAllRolesByUser($userId){
        $query = "SELECT r.id,r.name,r.display_name,r.description,"
                . "(SELECT user_id from role_user where role_id = r.id and user_id=".$userId.") as uid "
                . "FROM roles as r;";
        return DB::select(DB::raw($query));
    }
}
