<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmpMachine;

class EmpMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new EmpMachine();
        $models = $model->retriveDataCustom(
            $input,
            ['oe.id','o.name as oname','m.name as mname','e.firstname','e.midname','e.lastname','oe.status','oe.machine_userid'],
            [
                [$model->getTable().' as oe'],
                ['organization as o',['o.id','oe.org_id'],
                ['machine as m',['m.id','oe.machine_id']],
                ['employee as e',['e.id','oe.emp_id']]
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
        $model = new EmpMachine();
        if ($model->validate($request->all())) {
            $req = $request->all();
            if($model->canUserGetMachineUserId($req['org_id'],$req['machine_id'],$req['machine_userid'])){
                $model->fill($req);
                $model->start_date = time();
                $model->save();
                return response()->json($model);
            }else{
                return response()->json($this->errorMessage("Cannot assign same msachine userid for multiple employee."), 500);
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
        $model = EmpMachine::find($id);
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
        $model = new EmpMachine();
        if ($model->validate($request->except(['id']))) {
            $req = $request->except(['id']);
            $model = EmpMachine::find($id);
            if($model->canUserGetMachineUserId($req['org_id'],$req['machine_id'],$req['machine_userid'],$req['emp_id'])){
                $model->fill($req);
                if($req['status']==2){
                    $model->expired_date = time();
                }else{
                    $model->expired_date = null;
                }
                $model->save();
                return response()->json($model);
            }else{
                return response()->json($this->errorMessage("Cannot assign same msachine userid for multiple employee."), 500);
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
       if(EmpMachine::find($id)->delete()){
            return response()->json($this->successMessage('Item deleted successfully'));
	    }else{
            return response()->json($this->errorMessage('Cannot delete Item'), 500);
	    }	
    }
}
