<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct() {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth');
    }
    private function GeneralizeError($message,$status,$action='',$data=null){
        $msgData = [
            'status'=>$status,
            'message'=>$message,
            'action'=>$action,
        ];
        if(!empty($data)){
            $msgData['data'] = $data;
        }
        return $msgData;
    }
    
    public function errorMessage($message,$action='',$data=null){
        $status = "error";
        return $this->GeneralizeError($message,$status,$action,$data);
    }
    public function successMessage($message,$action='',$data=null){
        $status = "success";
        return $this->GeneralizeError($message,$status,$action,$data);
    }
    public function internalError($action='',$data=''){
        $message ="Internal server Error.";
        return $this->errorMessage($message,$action,$data);
    }
    public function basicError($action='',$data=''){
        $message ="Operation Failed.";
        return $this->errorMessage($message,$action,$data);
    }
    public function infoMessage($message,$action='',$data=null){
        $message ="";
        $status ="info";
        return $this->GeneralizeError($message, $status, $action, $data);
    }
    public function waitMessage($message,$action='',$data=null){
        $message ="";
        $status ="wait";
        return $this->GeneralizeError($message, $status, $action, $data);
    }
}
