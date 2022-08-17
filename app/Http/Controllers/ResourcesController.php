<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Redirect;
use Session;

class ResourcesController extends Controller
{
  //
  /**
* Display a listing of the resource.
*
* @return Response
*/
public function index(Request $request)
{
  //
  $filter = $request->input('topicList','1');

  if ($filter=='0') {
    $entities=\App\TopicFiles::all();
  } else {

    //$entities=\App\Task::where('topic','=',$filter)->get();//->join('topics','tasks.topic','=','topics.id')->firstOrFail();


    $entities = \App\TopicFiles::where('topic','=',$filter)
          ->select(
              'topic_files.id',
              'topic_files.fileName',
              'topic_files.path',
              'topic_files.topic',
              'topic_files.desc',
              'topics.name'
          )
          ->join(
              'topics',
              'topics.id','=','topic_files.topic'
          )
          //->where(['tasks.topic' => $filter])
          ->get();
  }
  //$items = \App\Topic::all();
  $items = \App\Topic::pluck('name', 'id');

  //$data=['entities'=>$entities, 'items'=>$items];
  return view('admin/resources/index')->with(compact('entities'))->with(compact('items'))->with(compact('filter'));


  //return view('admin/tasks/index')->with($data);
}

/**
* Show the form for creating a new resource.
*
* @return Response
*/
public function create()
{
  //
  $items = \App\Topic::pluck('name', 'id');

  return view('admin/resources/create')->with(compact('items'));
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
      'filename'=>'required',
      'path'=>'required'
  ];

  $msg=[
      'filename.required'=>'File Name must not empty',
      'path.required'=>'Folder Path must not empty'
  ];

  $validator=Validator::make($request->all(),$rules,$msg);

  //jika data ada yang kosong
  if ($validator->fails()) {

      //refresh halaman
      return Redirect::to('admin/resources/create')
      ->withErrors($validator);

  }else{

      $entity=new \App\TopicFiles;

      $entity->fileName=$request->get('filename');
      $entity->path=str_replace('\\','/',$request->get('path'));
      $entity->topic=$request->get('topic');
      $entity->desc=$request->get('desc');
      $entity->save();

      Session::flash('message','A New Topic Resource Stored');

      //return "Add new topic is success";
      return Redirect::to('admin/resources');
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
    $entity = \App\TopicFiles::find($id);
    $topic = \App\Topic::find($entity->topic);
    $x=['data'=>$entity, 'topic'=>$topic];

    return view('admin/resources/show')->with($x);
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
  $entity = \App\TopicFiles::find($id);
  $x=['data'=>$entity];
  $items = \App\Topic::pluck('name', 'id');
  return view('admin/resources/edit')->with($x)->with(compact('items'));
}

/**
* Update the specified resource in storage.
*
* @param  int  $id
* @return Response
*/
public function update(Request $request, $id)
{
//
$rules =[
    'filename'=>'required',
    'path'=>'required'
];

$msg=[
    'filename.required'=>'File Name must not empty',
    'path.required'=>'Folder Path must not empty'
];


$validator=Validator::make($request->all(),$rules,$msg);

if ($validator->fails()) {
    return Redirect::to('admin/resources/'.$id.'/edit')
    ->withErrors($validator);

}else{
  $entity=\App\TopicFiles::find($id);

  $entity->fileName=$request->get('filename');
  $entity->path=str_replace('\\','/',$request->get('path'));
  $entity->topic=$request->get('topic');
  $entity->desc=$request->get('desc');
  $entity->save();

  Session::flash('message','Resource with Id='.$id.' is changed');

  return Redirect::to('admin/resources');
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
$entity = \App\TopicFiles::find($id);
$entity->delete();
Session::flash('message','Resource with Id='.$id.' is deleted');
return Redirect::to('admin/resources');
}
}
