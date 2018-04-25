<?php

namespace App\Http\Controllers\Organization;

use Illuminate\Http\Request;
use App\Organization\Post;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $input = (object) $r->query();
        $model = new Post();
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
        $model = new Post();
        if ($model->validate($request->all())) {
            $model = Post::create($request->all());
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
        $model = Post::find($id);
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
        $model = new Post();
        if ($model->validate($request->except(['id']))) {
            unset($model);
            $model = Post::find($id);
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
       if(Post::find($id)->delete()){
		   return response()->json('Deleted');
	   }else{
		   return response()->json('Internal Error', 500);
	   }
		
    }
}

