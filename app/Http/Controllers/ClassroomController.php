<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class ClassroomController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $entities=\App\Classroom::where('classrooms.owner','=',Auth::user()->id)
            ->leftJoin('class_members_group','classrooms.id','=','class_members_group.classid')
            ->select('classrooms.id','classrooms.name','classrooms.status','classrooms.desc','class_members_group.count')
            ->orderBy('classrooms.name','asc')
            ->get();
        $data=['entities'=>$entities];

        return view('teacher/crooms/index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
		return view('teacher/crooms/create');
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
            'name'=>'required'
        ];

        $msg=[
            'name.required'=>'Class name must not empty'
        ];

        $validator=Validator::make($request->all(),$rules,$msg);

        //jika data ada yang kosong
        if ($validator->fails()) {

            //refresh halaman
            return Redirect::to('teacher/crooms/create')
            ->withErrors($validator);

        }else{

            $entity=new \App\Classroom;
		$desc = ($request->get('desc')==null)?'-':$request->get('desc');
            $entity->name=$request->get('name');
            $entity->owner=Auth::user()->id;
            $entity->desc=$desc;
            $entity->save();

            Session::flash('message','A New Classroom Stored');

            //return "Add new topic is success";
            return Redirect::to('teacher/crooms');
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
        $entity = \App\ClassMember::where('class_members.classid','=',$id)
            ->join('users','users.id','=','class_members.student')
            ->select('users.id','users.name','users.email')
            ->orderBy('users.name','asc')->get();
        $x=['entities'=>$entity];
        return view('teacher/crooms/show')->with($x);
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
        $entity = \App\Classroom::find($id);
        $x=['classroom'=>$entity];
        return view('teacher/crooms/edit')->with($x);
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
            'name'=>'required'
        ];

        $msg=[
            'name.required'=>'Class name must not empty'
        ];


        $validator=Validator::make($request->all(),$rules,$msg);

        if ($validator->fails()) {
            return Redirect::to('teacher/crooms/'.$id.'/edit')
            ->withErrors($validator);

        }else{
            $entity = \App\Classroom::find($id);
$desc = ($request->get('desc')==null)?'-':$request->get('desc');

            $entity->name=$request->get('name');
            $entity->desc=$desc;
          $entity->save();

          Session::flash('message','Class with Id='.$id.' is changed');
          return Redirect::to('teacher/crooms');
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
        $entity = \App\Classroom::find($id);
        $entity->delete();
        Session::flash('message','Class with Id='.$id.' is deleted');
        return Redirect::to('teacher/crooms');
    }
}
