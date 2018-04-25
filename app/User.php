<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    //use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','address','phone','email','website',
    ];
    public $errors=[];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','user_type','detail_id','created_at','updated_at'
    ];
    
    public static function getUserId($email,$password){
        $id =  DB::table('users')->select(['id','password','address','phone'])->where('email','=',$email)->get();
        echo var_dump($id);exit;
    }
    
    public function changePassword($id,$oldPassword,$newPassword){
        $user = self::find($id);
        if(Hash::check($oldPassword,$user->password)){
            $user->password = Hash::make($newPassword);
            $user->save();
            return true;
        }
        return false;
    }
    
    public function resetUserPassword($userId,$password){
        $user = self::find($userId);
        $user->password = Hash::make($password);
        if ($user->save()) {
            return true;
        }
        return false;
    }
    public static function emailExists($email){
        $row = self::select('id')->where('email','=',$email)->first();
        if(!empty($row) && $row->id!=''){
            return true;
        }
        return false;
    }
    
}
