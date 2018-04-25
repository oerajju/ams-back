<?php

namespace App\Http\Controllers\Organization;

use Illuminate\Http\Request;
use App\Organization\Employee;
use App\Http\Controllers\Controller;
use DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new Employee();
        $models = $model->retriveData($input);
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
        $model = new Employee();
        if ($model->validate($request->all())) {
            $req = $request->all();
            $model->fill($req);
            if($model->reports_to == ''){
                $model->reports_to=0;
            }
            if($model->cnt_start_date == ''){
                $model->cnt_start_date=0;
            }
            if($model->cnt_term_date == ''){
                $model->cnt_term_date=0;
            }
            $model->save();
            return response()->json($model);
        } else {
            return response()->json($model->errors, 500);
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
        $model = Employee::find($id);
        return response()->json($model);
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
        $model = new Employee();
        if ($model->validate($request->except(['id']))) {
            unset($model);
            $model = Employee::find($id);
            $req = $request->except(['id']);
            $model->fill($req);
            if($model->reports_to == ''){
                $model->reports_to=0;
            }
            if($model->cnt_start_date == ''){
                $model->cnt_start_date=0;
            }
            if($model->cnt_term_date == ''){
                $model->cnt_term_date=0;
            }
            $model->save();
            return response()->json($model);
        } else {
            return response()->json($model->errors, 500);
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
       if(Employee::find($id)->delete()){
		   return response()->json('Deleted');
	   }else{
		   return response()->json('Internal Error', 500);
	   }
		
    }
    
    public function listEmployeeByOrg($orgid){
        $data = Employee::select(DB::raw("id,concat(firstname,' ',midname,' ',lastname) as name"))
        ->where('orgid','=',$orgid)->get();
        if(!empty($data)){
            return response()->json($data);
        }else{
            return response()->json([]);
        }
    }
    
}

