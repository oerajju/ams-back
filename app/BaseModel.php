<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;
use DB;

class BaseModel extends Model
{
    public $rules =[];
    public $errors=[];
    public $searchable=[];
    
    public function validate($data)
    {
        // make a new validator object
        $v = Validator::make($data, $this->rules);
        // check for failure
        if ($v->fails())
        {
            // set errors and return false
            $this->errors = $v->errors();
            return false;
        }

        // validation pass
        return true;
    }
    
    public function getData($fields=null,$where=null,$order=null){
        if(!empty($fields)){
            $qry = $this::select($fields);
        }else{
            $qry = $this::select();
        }
        if(!empty($where)){
            $qry->where($where);
        }
        if(!empty($where)){
            $qry->orderBy($order);
        }
        return $qry->get();
    }
    
    public function retriveData($input,$fields=null){
        
        if(!empty($fields) && is_array($fields)){
            
            $qry = $this::select($fields);
        }else{
            $qry = $this::select();
        }
        
        if(property_exists($input,'searchOption') ){
            if($input->searchOption === 'all'){
                foreach($this->searchable as $k=>$s){
                    if($k===0){
                        $qry->where($s,'like','%'.$input->searchTerm.'%');
                        continue;
                    }
                    $qry->orWhere($s,'like','%'.$input->searchTerm.'%');
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
    }
    
    public function retriveDataCustom($input,$fields,$tables,$order=null){ 
        $qry = DB::table($tables[0][0])->select($fields);
        if(isset($tables[0][1])){
            $join=$tables[0][1].'Join';
        }else{
            $join ='join';
        }
        if(count($tables)>0){
            foreach($tables as $k=>$tb){
                if($k==0){
                    continue;
                }
                if(count($tb)>2){
                    $qry->$join($tb[0],function($query){
                        $query->on($tb[1][0],'=',$tb[1][1])->where($tb[2][0],$tb[2][1],$tb[2][2]);
                    });
                }else{
                    $qry->$join($tb[0],$tb[1][0],'=',$tb[1][1]);
                }
                
            }
        }
        if(property_exists($input,'sortKey') && $input->sortKey !='' &&  property_exists($input,'sortDir') && $input->sortDir !=''){
            $qry->orderBy($input->sortKey,$input->sortDir);
        }else{
            if(!empty($order)){
            $qry->orderBy($order);
        }
        }
        if(property_exists($input,'searchOption') ){
            if($input->searchOption === 'all'){
                foreach($this->searchable as $k=>$s){
                    if($k===0){
                        $qry->where($s,'like','%'.$input->searchTerm.'%');
                        continue;
                    }
                    $qry->orWhere($s,'like','%'.$input->searchTerm.'%');
                }
            }else{
                $qry->where($input->searchOption,'like','%'.$input->searchTerm.'%');
            }
        }
        
        if(property_exists($input,'perPage') ){
            return $qry->paginate($input->perPage);
        }
        return $qry->get();
    }
    
    public function change_2dot($string){
        return preg_replace('/_/', '.', $string,1);
    }
    
    public static function mMaxDay($year,$month){
        $qry = DB::table('date_meta')->select(['maxday'])
                ->where('years','=',$year)
                ->where('months','=',$month)->first();
        return $qry->maxday;
    }
    
    public static function nepDate($date){
        $q = DB::select(DB::raw("select nepdate('".$date."') as nepdate"));
        return $q[0]->nepdate;
    }
    public static function engDate($date){
        $q = DB::select(DB::raw("select engdate('".$date."') as engdate"));
        return $q[0]->engdate;
    }
    
    public static function splitDate($date,$eng=true){
        if($eng==true){
            $d = explode('/',$date);
            $e[0] =$d[2];
            $e[1]= $d[0];
            $e[2]= $d[1];
            return $e;
        }
        else{
            return explode('/',$date);
        }
        
    }
    
    public static function getFiscalYear($nepDate = null){
        if($nepDate == null){
            $nepDate = self::nepDate(date('Y-m-d'));
        }
        $dateParts = self::splitDate($nepDate,false);
        if($dateParts[1] >= 4){
            return $dateParts[0];
        }else{
            return ($dateParts[0]-1);
        }
    }
    
    //this function changes nepali number to english and english no to nepali
    public static function toggleNumber($n) {
        $langMap = ["०", "१", "२", "३", "४", "५", "६", "७", "८", "९"];
        $num = '';
        $nArray = preg_split('/(.{0})/us', $n, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        foreach ($nArray as $sn) {
            if (isset($langMap[$sn])) {
                $num .=$langMap[$sn];
            } elseif (in_array($sn, $langMap)) {
                $num .= array_search($sn, $langMap);
            }
            else{
                $num .=$sn;
            }
        }
        return $num;
    }
    public static function getMonth($id,$en=true){
        if($en){
            $q = DB::table('month_list')->select(['monthen'])->where('monthid','=',$id)->first();
            return $q->monthen;
        }else{
            $q = DB::table('month_list')->select(['monthnp'])->where('monthid','=',$id)->first();
            return $q->monthnp;
        }
        
    }

}
