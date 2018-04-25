<?php

namespace App;

class OrgMachine extends BaseModel
{
    public $timestamps = false;
    protected $table = 'org_machine';
    protected $fillable = ['org_id','machine_id','remarks','status'];
    public $searchable = ['om.org_id','om.machine_id','om.remarks','o.name','m.name','m.machine_id'];
    
    public $rules = [
          'org_id'=> 'required',
          'machine_id'=> 'required',
          'remarks'=> 'string|nullable',
          'status'=> 'required'
     ];

     public function canBindMachine($machineId,$orgId){
         $data = $this::select('id')
         //->where('org_id','=',$orgId)
         ->where('machine_id','=',$machineId)
         ->where('status','!=','2')->get();
         if(count($data)>0){
             return false;
         }else{
             return true;
         }
     }

}
