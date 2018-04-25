<?php

namespace App\Organization;

use App\BaseModel;

class Employee extends BaseModel
{
    protected $table = 'employee';
    protected $fillable = ['firstname','midname','lastname','address','phone','email','reports_to','cnt_start_date','cnt_term_date','orgid'];
    public $searchable = ['firstname','midname','lastname','address','phone','email'];
    
    public $rules = [
          'firstname'=> 'string',
          'midname'=> 'string',
          'lastname'=> 'string',
          'address'=> 'string',
          'phone'=> 'string',
          'email'=> 'string|nullable',
          'active'=> 'integer',
          'reports_to'=> 'integer|nullable',
          'cnt_start_date'=> 'integer|nullable',
          'cnt_term_date'=> 'integer|nullable',
        'orgid'=>'integer'
     ];
}
