<?php

namespace App;

use DB;
use Auth;
class Leave extends BaseModel
{
    public $timestamps = false;
    protected $table = 'leave_request';
    protected $fillable = ['leave_type','reason','status','date_from','date_to'];
    public $searchable = ['leave_type','date_from','date_to','status'];
    public $rules = [
          'reason' =>'required',
          'date_from' =>'required',
          'date_to' =>'required',
          'leave_type'=> 'required',
          'status'=> 'required',
          
     ];
    public function retreiveData($input,$id,$fields=null){

         if(!empty($fields) && is_array($fields)){
            
            $qry = $this::select($fields)->where('emp_id',$id);
        }else{
            $qry = $this::select()->where('emp_id',$id);
        }
        if(property_exists($input,'searchOption') ){
            if($input->searchOption === 'all'){
                foreach($this->searchable as $k=>$s){
                    if($k===0){
                        $qry->where($s,'like','%'.$input->searchTerm.'%')->where('emp_id',$id);
                        continue;
                    }
                    $qry->orWhere($s,'like','%'.$input->searchTerm.'%')->where('emp_id',$id);
                }
            }else{
                $qry->where($input->searchOption,'like','%'.$input->searchTerm.'%');
            }
        }
        if(property_exists($input,'sortKey') && $input->sortKey !='' &&  property_exists($input,'sortDir') && $input->sortDir !=''){
            $qry->orderBy($input->sortKey,$input->sortDir);
        }
         if(property_exists($input,'perPage') ){
            return $qry->paginate($input->perPage);
        }
        return $qry->get();
       // return $qry->paginate($input->perpage)

    
    }
    // public function getUserById()
    // {
    //    $id = Auth::User()->id;
    //   return DB::table('leave_request')->select('emp_id','leave_type','date_from','date_to','day_count','status')->where('emp_id',$id)->get();
    // }
    //  public function getMachineByOrg($orgid){
    //     $data = DB::table($this->getTable()." as m")->select(['m.id','m.name'])
    //     ->join('org_machine as om','om.machine_id','=','m.id')
    //     ->where('om.org_id','=',$orgid)
    //     ->where('om.status','!=',2)->groupBy('m.id','m.name')
    //     ->get();
    //     return $data;
    // }
}
