<?php

namespace App;

use DB;

class Organization extends BaseModel
{
    //public $timestamps = true;
    protected $table = 'organization';
    protected $fillable = ['name','phone','email','active','parent_id'];
    public $searchable = ['name','phone','email','active','parent_id'];
    
    public $rules = [
		  'id'=> 'string',
          'name'=> 'required|string',
          'phone'=> 'string',
          'email'=> 'email',
          'active'=> 'integer',
          'parent_id'=> 'integer'
     ];
    
    public function getOrganizations($id=null){
        if(!empty($id)){
            $org = self::select('parent_path')->where('id','=',$id)->first();
            $data = DB::select(DB::raw("select id,name from ".$this->getTable()." where id<>$id and parent_path not like '".$org->parent_path.".%'"));
        }else{
            $data = DB::select(DB::raw("select id,name from ".$this->getTable()));
        }
        return $data;
    }
    public function isParentValid($newParent,$selfPath){
        $data = DB::select(DB::raw("select count(id) as counts from ".$this->getTable()." where parent_path like '".$selfPath."%' and id=".$newParent));
        if($data[0]->counts==0){
            return true;
        }
        return false;
    }
    public function updateAllChldsPath($oldPath,$newPath){
       $sql ="update ".$this->getTable()." set parent_path = replace(parent_path,'".$oldPath."','".$newPath."') where parent_path like '".$oldPath.".%'";
       DB::statement($sql);
    }
}
