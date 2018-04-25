<?php

namespace App\Organization;

use App\BaseModel;

class Post extends BaseModel
{
    protected $table = 'post';
    protected $fillable = ['name','active'];
    public $searchable = ['name','active'];
    
    public $rules = [
          'name'=> 'string',
          'active'=> 'integer'
     ];
}
