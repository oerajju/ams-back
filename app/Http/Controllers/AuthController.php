<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use Illuminate\Support\Facades\DB;
use App\User;

class AuthController extends Controller {

    public function __construct() {
        $this->middleware('jwt.auth', ['except' => ['authenticate','registerEmployer','registerJobseeker','emailExists']]);
    }

    public function index() {
        // Retrieve all the users in the database and return them
        //$users = \App\User::all();
        //return $users;
    }

    public function authenticate(Request $request) {
        $credentials = $request->only('email', 'password');
        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        
        $id = JWTAuth::getPayload($token)->get('sub');
        $user = User::find($id);
        $details = ['type'=>  $user->user_type,'detail'=>$user->detail_id];
        $token = JWTAuth::attempt($credentials,$details);
        
        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser() {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
    
    public function logout(){
        //invalidate token
        $result = \JWTAuth::parseToken()->invalidate();
        return response()->json($result);
    }
    
    public function getPermissions(){
        $did = \JWTAuth::parseToken()->getPayload()->get('did');
        $data = DB::table('pc_drb as pd')->select(['name'])
                ->join('permissions_custom as pc','pd.pid','=','pc.id')
                ->where('pd.did','=',$did)->get();
        $pArray = [];
        if(count($data)>0){
            foreach($data as $d){
                $pArray[]=$d->name;
            }
        }
        return response()->json(['permissions'=>$pArray]);
    }
    
    public function changePassword(Request $request){
        //return $request->except(['token']);
       // $old_password = $request->oldpassword;

         $userId = JWTAuth::parseToken()->authenticate()->id;
        if($request->password == $request->password_confirmation){
            $user = new User();
            if($user->changePassword($userId,$request->oldpassword,$request->password_confirmation)){
                return response()->json(['message'=>"Password Successfully Changed."]);
            }else{
                return response()->json(['error' => "Wrong Old Password"]);
            }
        }else{
            return response()->json(['message'=>"New and Confirm Password Does not match."],500);
        }

        // $userId = JWTAuth::parseToken()->authenticate()->id;
        // if($request->new_password == $request->cnew_password){
        //     $user = new User();
        //     if($user->changePassword($userId,$request->old_password,$request->new_password)){
        //         return response()->json(['message'=>"Password Successfully Changed."]);
        //     }else{
        //         return response()->json(['message'=>"Wrong Old Password"],500);
        //     }
        // }else{
        //     return response()->json(['message'=>"New and Confirm Password Does not match."],500);
        // }
    }
    public function emailExists(Request $r){
        if($r->email!=''){
            if(User::emailExists($r->email)){
                return json_encode(['email_exists'=>1]);
            }else{
                return json_encode(['email_exists'=>0]);
            }
        }
    }

}
