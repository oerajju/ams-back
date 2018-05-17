<?php

namespace App\Attendence;

use DB;
use App\BaseModel;

class LogDetail extends BaseModel
{
    //public $timestamps = true;
    protected $table = 'att_detail';
    protected $fillable = ['machine_id','machine_user_id','checkin_time','checkout_time'];
    public $searchable = ['machine_id','machine_user_id','checkin_time','checkout_time'];
    
    public $rules = [
        'machine_id'=> 'string|required',
        'machine_user_id'=> 'string|required',
        'checkin_time'=> 'string|required',
        'checkout_time'=> 'string|required',
        /*'yearen'=> 'string|required',
        'monthen'=> 'string|required',
        'dayen'=> 'string|required',
        'yearnp'=> 'string|required',
        'monthnp'=> 'string|required',
        'daynp'=> 'string|required',
        'timestamp'=> 'string|required', */
     ];
}
