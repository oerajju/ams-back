<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrgMachine;

class OrgMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new OrgMachine();
        $models = $model->retriveDataCustom(
            $input,
            ['om.id','o.name as oname','m.name as mname','om.status','m.machine_id'],
            [
                [$model->getTable().' as om'],
                ['organization as o',['o.id','om.org_id']],
                ['machine as m',['m.id','om.machine_id']]
            ]
        );
        return response()->json($models);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new OrgMachine();
        if ($model->validate($request->all())) {
            $req = $request->all();
            if($model->canBindMachine($req['machine_id'],$req['org_id'])){
                $model->fill($req);
                $model->from_date_int = time();
                $model->save();
                return response()->json($model);
            }else{
                return response()->json($this->errorMessage("Cannot Bind Same Machine for two Organization"), 500);
            }
            
        } else {
            return response()->json($this->errorMessage($model->errors), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = OrgMachine::find($id);
        if(!empty($model)){
            return response()->json($model);
        }else{
            return response()->json($this->errorMessage('Item Not Found.'),500);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = new OrgMachine();
        if ($model->validate($request->except(['id']))) {
            $req = $request->except(['id']);
            $model = OrgMachine::find($id);
            if($model->machine_id == $req['machine_id'] && $model->status==2){ 
                if($model->canBindMachine($req['machine_id'],$req['org_id'])){
                    $model->fill($req);
                    if($req['status']==2){
                        $model->to_date_int = time();
                    }else{
                        $model->to_date_int = null;
                    }
                    $model->save();
                    return response()->json($model);
                }else{
                    return response()->json($this->errorMessage("Cannot Bind Same Machine for two Organization"), 500);
                }
        }
            elseif($model->machine_id == $req['machine_id']){ 
                    $model->fill($req);
                    if($req['status']==2){
                        $model->to_date_int = time();
                    }else{
                        $model->to_date_int = null;
                    }
                    $model->save();
                    return response()->json($model);
            }else{
                if($model->canBindMachine($req['machine_id'],$req['org_id'])){
                    $model->fill($req);
                    if($req['status']==2){
                        $model->to_date_int = time();
                    }else{
                        $model->to_date_int = null;
                    }
                    $model->save();
                    return response()->json($model);
                }else{
                    return response()->json($this->errorMessage("Cannot Bind Same Machine for two Organization"), 500);
                }
            }
            
            
        } else {
            return response()->json($this->errorMessage($model->errors), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       if(OrgMachine::find($id)->delete()){
            return response()->json($this->successMessage('Item deleted successfully'));
	}else{
            return response()->json($this->errorMessage('Cannot delete Item'), 500);
	}	
    }
}
