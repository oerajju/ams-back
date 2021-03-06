<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\Request;
use App\Security\Permission;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new Permission();
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
        $model = new Permission();
        if ($model->validate($request->all())) {
            $model = Permission::create($request->all());
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
        $model = Permission::find($id);
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
        $model = new Permission();
        $model->rules['name'] = 'required|unique:roles,name,'.$id;
        if ($model->validate($request->except(['id']))) {
            unset($model);
            $model = Permission::find($id);
            $req = $request->except(['id']);
            $model->fill($req);
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
       if(Permission::find($id)->delete()){
		   return response()->json('Deleted');
	   }else{
		   return response()->json('Internal Error', 500);
	   }
		
    }
}

