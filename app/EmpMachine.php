<?php

namespace App;

class EmpMachine extends BaseModel
{
    public $timestamps = false;
    protected $table = 'emp_machine';
    protected $fillable = ['org_id','machine_id','emp_id','machine_userid','status','remarks'];
    public $searchable = ['oe.org_id','oe.machine_id','oe.machine_userid','o.name','m.name','e.firstname','e.midname','e.lastname'];
    
    public $rules = [
          'org_id'=> 'required',
          'machine_id'=> 'required',
          'emp_id'=>'required',
          'machine_userid'=>'required',
          'status'=> 'required',
          'remarks'=> 'string|nullable'
     ];

    public function canUserGetMachineUserId($orgid,$machineid,$machineUserId,$empId=null){
        $qry = $this::select('id')
        ->where('org_id','=',$orgid)
        ->where('machine_id','=',$machineid)
        ->where('machine_userid','=',$machineUserId);
        if(!empty($empId)){
            $qry->where('emp_id','!=',$empId);
        }
        $data = $qry->where('status','!=',2)->get();
        if(count($data)>0){
            return false;
        }else{
            return true;
        }
    }
}
