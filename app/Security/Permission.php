<?php
namespace App\Security;

use Zizaco\Entrust\EntrustPermission;
use App\BaseModelTrait;

class Permission extends EntrustPermission
{
	use BaseModelTrait;
    //public $timestamps = false;
    protected $table = 'permissions';
    protected $fillable = ['name','display_name','description'];
    protected $searchable = ['name','display_name','description'];
    
    public $rules = ['name'=> 'required|unique:permissions',
          'display_name'=> 'string|nullable',
          'description'=> 'string|nullable'
     ];
}
