<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Http\Request;

use Redirect;
use Session;

class UiTopicController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $entities=\App\UiTopic::orderBy('level','asc')->get();
    //::all();

        $data=['entities'=>$entities];

        return view('admin/uitopic/index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
		return view('admin/uitopics/create');
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
            'level'=>'required'
        ];

        $msg=[
            'name.required'=>'Topic name must not empty',
            'level.required'=>'Learning level must not empty'
        ];

        $validator=Validator::make($request->all(),$rules,$msg);

        //jika data ada yang kosong
        if ($validator->fails()) {

            //refresh halaman
            return Redirect::to('admin/topics/create')
            ->withErrors($validator);

        }else{

            $entity=new \App\UiTopic;

            //$entity->name=$request->get('name');
            
	    $entity->level = $request->get('level');
            $entity->name = $request->get('name');
            $entity->projectname = $request->get('projectname');
            $entity->projectpath = $request->get('projectpath');
            $entity->packname = $request->get('packname');
            $entity->description = $request->get('description');
            $entity->guidepath = $request->get('guidepath');
            $entity->status = $request->get('status');
            $entity->save();

            Session::flash('message','A New Topic Stored');

            //return "Add new topic is success";
            return Redirect::to('admin/uitopics');
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
        $entity = \App\UiTopic::find($id);
        $x=['topic'=>$entity];
        return view('admin/uitopics/show')->with($x);
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
        $entity = \App\UiTopic::find($id);
        $x=['topic'=>$entity];
        return view('admin/uitopics/edit')->with($x);
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
            'level'=>'required'
        ];

        $msg=[
            'name.required'=>'Topic name must not empty',
            'level.required'=>'Learning level must not empty'
        ];


        $validator=Validator::make($request->all(),$rules,$msg);

        if ($validator->fails()) {
            return Redirect::to('admin/uitopics/'.$id.'/edit')
            ->withErrors($validator);

        }else{
          $entity=\App\UiTopic::find($id);

          $entity->level = $request->get('level');
          $entity->name = $request->get('name');
          $entity->projectname = $request->get('projectname');
          $entity->projectpath = $request->get('projectpath');
          $entity->packname = $request->get('packname');
          $entity->description = $request->get('description');
          $entity->guidepath = $request->get('guidepath');
          $entity->status = $request->get('status');
          $entity->save();

          Session::flash('message','Topic with Id='.$id.' is changed');
          return Redirect::to('admin/uitopics');
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
