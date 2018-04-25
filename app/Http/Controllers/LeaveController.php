<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Leave;
use DB;
use Auth;
use Carbon\Carbon;
class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $id = Auth::user()->id;  
        $input = (object) $r->query();
        //$model = new Leave();
        //$model = $model->where('emp_id',$id);
        $model = new Leave();
        //$model =$model->where('emp_id',$id)->get();
        $models = $model->retreiveData($input,$id);
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
        $empId = Auth::user()->id;
        $model = new Leave();
        if ($model->validate($request->all())) {
            $req = $request->all();
            $end = Carbon::parse($request['date_to']);
            $start = Carbon::parse($request['date_from']);
            $dayCount = $end->diffInDays($start);
            $model->fill($req);
            $model->emp_id = $empId;
            $model->day_count= $dayCount + 1;
            $model->save();
            return response()->json($model);
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
        $model= Leave::find($id);
        return response()->json($model);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Leave::find($id);
        // if(!empty($model)){
            return response()->json($model);
        // }else{
        //     return response()->json($this->errorMessage('Item Not Found.'),500);
        // }
        
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
        $model = new Leave();
        // $model->rules['machine_id'] = 'required|unique:machine,machine_id,'.$id;
         if ($model->validate($request->except(['id']))) {
             unset($model);
             $model = Leave::find($id);
             $req = $request->except(['id']);
             $model->fill($req);
             $model->save();
             return response()->json($model);
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
       if(Leave::find($id)->delete()){
            return response()->json($this->successMessage('Item deleted successfully'));
	}else{
            return response()->json($this->errorMessage('Cannot delete Item'), 500);
	}	
    }
    
    // public function listMachOptions(){
    //     $model = new Machine();
    //     $data = $model->getData(['id','name']);
    //     if(isset($data) && count($data)>0){
    //         return response()->json($data);
    //     }
    // }

    public function leaveApproval(){
       // $model = new Machine();
       // return $model->getMachineByOrg($orgid);
    }
}
