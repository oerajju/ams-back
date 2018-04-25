<?php

namespace App;

use DB;

class Machine extends BaseModel
{
    protected $table = 'machine';
    protected $fillable = ['name','machine_id','status','remarks'];
    public $searchable = ['name','machine_id','status','remarks'];
    
    public $rules = [
          'name'=> 'required',
          'machine_id'=> 'required|unique:machine,machine_id',
          'status'=> 'required',
          'expired_on'=> 'string|nullable',
          'remarks'=> 'string|nullable'
     ];

     public function getMachineByOrg($orgid){
        $data = DB::table($this->getTable()." as m")->select(['m.id','m.name'])
        ->join('org_machine as om','om.machine_id','=','m.id')
        ->where('om.org_id','=',$orgid)
        ->where('om.status','!=',2)->groupBy('m.id','m.name')
        ->get();
        return $data;
    }
}
