<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Http\Request;

use Redirect;
use Session;

class TopicController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $entities=\App\Topic::orderBy('name','asc')->orderBy('status', 'asc')->get();
    //::all();

        $data=['entities'=>$entities];

        return view('admin/topics/index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
		return view('admin/topics/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $rules =[
            'name'=>'required',
            'stage'=>'required'
        ];

        $msg=[
            'name.required'=>'Topic name must not empty',
            'stage.required'=>'Learning stage must not empty'
        ];

        $validator=Validator::make($request->all(),$rules,$msg);

        //jika data ada yang kosong
        if ($validator->fails()) {

            //refresh halaman
            return Redirect::to('admin/topics/create')
            ->withErrors($validator);

        }else{

            $entity=new \App\Topic;

	 $name=$request->get('name');

            $entity->name=$name;
            $entity->stage=$request->get('stage');
            $entity->desc=$request->get('desc');
            $entity->packname=$request->get('packname');
            $entity->projectpath=$request->get('projectpath');
	$entity->topiccode=substr($name,0,2);
            $entity->save();

            Session::flash('message','A New Topic Stored');

            //return "Add new topic is success";
            return Redirect::to('admin/topics');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $entity = \App\Topic::find($id);
        $x=['topic'=>$entity];
        return view('admin/topics/show')->with($x);
      //return view('topics.show', array('topic' => $topic));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $entity = \App\Topic::find($id);
        $x=['topic'=>$entity];
        return view('admin/topics/edit')->with($x);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
        //
        $rules =[
            'name'=>'required',
            'stage'=>'required'
        ];

        $msg=[
            'name.required'=>'Topic name must not empty',
            'stage.required'=>'Learning stage must not empty'
        ];


        $validator=Validator::make($request->all(),$rules,$msg);

        if ($validator->fails()) {
            return Redirect::to('admin/topics/'.$id.'/edit')
            ->withErrors($validator);

        }else{
          $entity=\App\Topic::find($id);

          $entity->name=$request->get('name');
          $entity->stage=$request->get('stage');
          $entity->desc=$request->get('desc');
          $entity->packname=$request->get('packname');
          $entity->projectpath=$request->get('projectpath');
          $entity->save();

          Session::flash('message','Topic with Id='.$id.' is changed');
          return Redirect::to('admin/topics');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        $entity = \App\Topic::find($id);
        $entity->delete();
        Session::flash('message','Topic with Id='.$id.' is deleted');
        return Redirect::to('admin/topics');
    }
}
