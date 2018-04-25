<?php

namespace App\Http\Controllers\Organization;

use Illuminate\Http\Request;
use App\Organization\Organization;
use App\Http\Controllers\Controller;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new Organization();
        $model->searchable =['o.name','o.phone','o.email','p.name'];
        $models = $model->retriveDataCustom($input,
                ['o.id','o.name','o.phone','o.email','p.name as parentname'],
                [
                    ['organization as o','left'],
                    ['organization as p',['o.parent_id','p.id']]
                ],'o.id'
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
        $model = new Organization();
        if ($model->validate($request->all())) {
            $model = Organization::create($request->all());
            $parentId = $request->input('parent_id');
            if('' != $parentId){
                $parent = Organization::select('parent_path')->where('id','=',$parentId)->first();
                $model->parent_path = $parent->parent_path.".".$model->id;
                $model->save();
            }else{
                $model->parent_path = $model->id;
                $model->save();
            }
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
        $model = Organization::find($id);
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
        $model = new Organization();
        if ($model->validate($request->except(['id']))) {
            unset($model);
            $model = Organization::find($id);
            $parentId = $request->input('parent_id');
            $oldPath = $model->parent_path;
            $req = $request->except(['id']);
            if('' != $parentId){
                if($model->isParentValid($parentId, $model->parent_path)){
                    $model->fill($req);
                    $parent = Organization::select('parent_path')->where('id','=',$parentId)->first();
                    $model->parent_path = $parent->parent_path.".".$model->id;
                    $model->save();
                    $model->updateAllChldsPath($oldPath,$model->parent_path);
                }else{
                    return response()->json('Parent is not valid.', 500);
                }                
            }else{
                $model->fill($req);
                $model->parent_path = $model->id;
                $model->save();
                $model->updateAllChldsPath($oldPath,$model->parent_path);
            }
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
       if(Organization::find($id)->delete()){
		   return response()->json('Deleted');
	   }else{
		   return response()->json('Internal Error', 500);
	   }
		
    }
    
    public function getOrganizations($id=null){
        $orgs = new Organization();
        return response()->json($orgs->getOrganizations($id));
    }
}

