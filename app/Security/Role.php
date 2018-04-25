<?php
namespace App\Security;

use App\BaseModelTrait;
use Zizaco\Entrust\EntrustRole;
use DB;

class Role extends EntrustRole
{
	use BaseModelTrait;
	
    protected $table = 'roles';
    protected $fillable = ['name','display_name','description'];
    protected $searchable = ['name','display_name','description'];
    
    public $rules = ['name'=> 'required|unique:roles',
          'display_name'=> 'string|nullable',
          'description'=> 'string|nullable'
    ];
    
    public function attachAllPermissions($roleId,$permissions){
        $role = EntrustRole::find($roleId);
        if($role->perms()->sync($permissions)){
            return true;
        }else{
            return false;
        }
    }
    
    public function getAllPermissionsByRole($roleId){
        $query = "SELECT p.id,p.name,p.display_name,p.description,"
                . "(SELECT role_id from permission_role where permission_id = p.id and role_id=".$roleId.") as rid "
                . "FROM permissions as p;";
        return DB::select(DB::raw($query));
    }
}
