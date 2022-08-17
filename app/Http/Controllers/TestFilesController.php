<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Redirect;
use Session;
use File;

class TestFilesController extends Controller
{
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
    $entities=\App\TestFiles::all();
  } else {

    //$entities=\App\Task::where('topic','=',$filter)->get();//->join('topics','tasks.topic','=','topics.id')->firstOrFail();


    $entities = \App\TestFiles::where('test_files.topic','=',$filter)
          ->select(
              'test_files.id',
              'test_files.taskid',
              'test_files.fileName',
              'test_files.content',
              'topics.name',
              'tasks.taskno'
          )
          ->join(
              'tasks',
              'tasks.id','=','test_files.taskid'
          )
          ->join(
              'topics',
              'topics.id','=','test_files.topic'
          )
          ->orderBy('test_files.fileName','asc')
          ->get();
  }
  //$items = \App\Topic::all();
  $items = \App\Topic::pluck('name', 'id');

  //$data=['entities'=>$entities, 'items'=>$items];
  return view('admin/testfiles/index')->with(compact('entities'))->with(compact('items'))->with(compact('filter'));


  //return view('admin/tasks/index')->with($data);
}


/**
* Show the form for creating a new resource.
*
* @return Response
*/
public function create($id)
{
  //
  $topic = \App\Topic::find($id);
  $items = \App\Task::where('topic','=',$id)->orderBy('taskno','asc')->get();

  return view('admin/testfiles/create')
      ->with(compact('topic'))
      ->with(compact('items'));
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
      'testFile'=>'required'
  ];

  $msg=[
      'testFile.required'=>'Java File must not empty'
  ];

  $validator=Validator::make($request->all(),$rules,$msg);

  //jika data ada yang kosong
  if ($validator->fails()) {

      //refresh halaman
      return Redirect::to('admin/testfiles/create/'.$request->get('topic'))
      ->withErrors($validator);

  }else{
      $file = $request->file('testFile');
      $filename = $file->getClientOriginalName();
      $strpath = 'TestFiles/'.$request->get('topic');
      $testFile=$file->store('testfiles','public');

      $entity=new \App\TestFiles;

      $entity->taskid=$request->get('taskid');
      $entity->fileName=$filename;
      $entity->topic=$request->get('topic');
      $entity->content=$testFile;
      $entity->save();

      Session::flash('message','A New Test File Stored');

      //return "Add new topic is success";
      return Redirect::to('admin/testfiles?topicList='.$request->get('topic'));
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
  $entity = \App\TestFiles::find($id);
  $task = \App\Task::find($entity->taskid);
  $topic = \App\Topic::find($entity->topic);

  try {
    $path = storage_path('app\\public\\').$entity->content;
    //$path = str_replace('\\',DIRECTORY_SEPARATOR,$path);
    $content = File::get(getPath($path));
    //$content = $entity->content;
  } catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception) {
    //die("The file doesn't exist");
    $content = 'File is not found';
  }

  $x=['data'=>$entity, 'topic'=>$topic, 'task'=>$task, 'content'=>$content];
  return view('admin/testfiles/show')->with($x);
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
  $entity = \App\TestFiles::find($id);
  $x=['data'=>$entity];
  $items = \App\Topic::pluck('name', 'id');
  return view('admin/testfiles/edit')->with($x)->with(compact('items'));
}

/**
* Update the specified resource in storage.
*
* @param  int  $id
* @return Response
*/
public function update(Request $request, $id) {
  //
  $rules =[
      'testno'=>'required'
  ];

  $msg=[
      'testno.required'=>'Test number must not empty'
  ];


  $validator=Validator::make($request->all(),$rules,$msg);

  if ($validator->fails()) {
      return Redirect::to('admin/testfiles/'.$id.'/edit')
      ->withErrors($validator);

  }else{
    $file = $request->file('testFile');

    $entity=\App\TestFiles::find($id);

    $entity->testno=$request->get('testno');
    if ($file!='') {
      $filename = $file->getClientOriginalName();
      $strpath = 'TestFiles/'.$request->get('topic');
      $testFile=$file->store('testfiles','public');

      $entity->fileName=$filename;
      $entity->content=$testFile;
    }

    $entity->topic=$request->get('topic');
    $entity->save();

    Session::flash('message','Test File with Id='.$id.' is changed');

    return Redirect::to('admin/testfiles');
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
    $entity = \App\TestFiles::find($id);
    $entity->delete();
    Session::flash('message','Test File with Id='.$id.' is deleted');
    return Redirect::to('admin/testfiles');
  }

  public function getPath($path) {
    $res = str_replace('\\',DIRECTORY_SEPARATOR,$path);
    return str_replace('/',DIRECTORY_SEPARATOR,$res);
  }
}
