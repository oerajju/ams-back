<?php

namespace App;

use Validator;
use DB;

trait BaseModelTrait
{
    
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
    
    public function retriveData($input){
        $qry = $this::select();
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
}
