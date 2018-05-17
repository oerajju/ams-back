<?php

namespace App\Attendence;

use App\BaseModel;

class Log extends BaseModel
{
    protected $table = 'att_log';
    protected $fillable = ['myachine_id','machine_user_id','att_time','att_type'];
    public $searchable = ['name','active'];
    public $timestamps = false;
    
    public $rules = [
          'machine_id'=> 'string|required',
          'machine_user_id'=> 'string|required',
          'att_time'=>'string|required',
          'att_type'=>'string|required'
     ];
}
