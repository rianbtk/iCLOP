<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class FlutterController extends Controller
{
    //
    /**
 * Display a listing of the resource.
 *
 * @return Response
 */
public function index(Request $request) {
    if (Auth::user()->roleid=='student/fluttercourse') {
    	$check=\App\User::find(Auth::user()->id);
        if ($check->status!='active') return view('student/fluttercourse/home')->with(['status'=>$check->status]);
    }
    $topiclist=\App\FlutterTopic::where('status','=','1')
    ->orderBy('name','asc')->get();

    $items = \App\FlutterTopic::where('status','=','1')
        ->orderBy('status','desc')
        ->orderBy('name','asc')
        ->pluck('name', 'id');

    $itemslearning = \App\FlutterTopic::where('status','=','1')
        ->orderBy('status','desc')
        ->orderBy('name','asc')
	    ->where('level','=','1')
        ->pluck('name', 'id');

    $filter = $request->input('topicList',$topiclist[0]['id']);

    if ($filter=='0') {
      $entities=\App\FlutterTask::all();
    } else {

      $entities = \App\FlutterTask::where('topic','=',$filter)
            ->select(
                'tasks.id',
                'tasks.taskno',
                'tasks.desc',
                'tasks.topic',
                'topics.name'
            )
            ->join(
                'topics',
                'topics.id','=','tasks.topic'
            )
            ->orderBy('tasks.taskno','asc')
            ->get();
    }

    if (Auth::user()->roleid=='admin') {
      return view('admin/tasks/index')
      ->with(compact('entities'))
      ->with(compact('items'))
      ->with(compact('filter'));
    } else {
      $topic = \App\FlutterTopic::where('topics.id','=',$filter)
              ->select(
                  'topics.id',
                  'topics.name',
                  'topics.desc',
                  'learning_files.guide',
                  'learning_files.testfile',
                  'learning_files.supplement',
                  'learning_files.other'
              )
              ->leftJoin('learning_files', 'learning_files.topic', '=', 'topics.id')
              ->first();
      return view('student/fluttercourse/tasks/index')
            ->with(compact('entities'))
            ->with(compact('items'))
		->with(compact('itemslearning'))
            ->with(compact('filter'))
            ->with(compact('topic'));
    }
}


public function getTopic($id){
    $items = \App\FlutterTopic::find($id);

   return $items['name'];
}


public function filterTask() {
        $filters = \App\FlutterTopic::get();
        $filter = \App\FlutterTopic::findOrFail(Input::get('filter_id'));

        $data= \App\FlutterTask::with('topic')->where('topic', '=' ,  $filter->id )->latest()->get();

        return View::make('admin.tasks.index',compact('filters'))->withProfiles($data)->with('title', 'filter');
}

/**
 * Show the form for creating a new resource.
 *
 * @return Response
 */
public function create()
{
    //
    $items = \App\FlutterTopic::pluck('name', 'id');
//echo "kljasd;lkasdl";
    return view('admin/tasks/create')->with(compact('items'));
}
/**
 * Store a newly created resource in storage.
 *
 * @return Response
 */
public function store(Request $request)
{
//echo "YAAANNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN";
    //
    $rules =[
        'taskno'=>'required',
        'desc'=>'required'
    ];

    $msg=[
        'taskno.required'=>'Task number must not empty',
        'desc.required'=>'Description must not empty'
    ];

    $validator=Validator::make($request->all(),$rules,$msg);

    //jika data ada yang kosong
    if ($validator->fails()) {

        //refresh halaman
        return Redirect::to('admin/tasks/create')
        ->withErrors($validator);

    }else{

        $entity=new \App\FlutterTask;

        $entity->desc=$request->get('desc');
        $entity->taskno=$request->get('taskno');
        $entity->topic=$request->get('topic');
        $entity->save();

        Session::flash('message','A New Task Stored');

        //return "Add new topic is success";
        return Redirect::to('admin/tasks');
    }
}

/**
 * Display the specified resource.
 *
 * @param  int  $id
 * @return Response
 */
public function show(Request $request, $id)
{
    $entity = \App\FlutterTask::find($id);
    $topic = \App\FlutterTopic::find($entity->topic);
    $x=['data'=>$entity, 'topic'=>$topic];

    if ($request->is('admin/*')) {
      return view('admin/tasks/show')->with($x);
    } else {
      return view('student/fluttercourse/tasks/show')->with($x);
    }
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
    $entity = \App\FlutterTask::find($id);
    $x=['data'=>$entity];
    $items = \App\FlutterTopic::pluck('name', 'id');
    return view('admin/tasks/edit')->with($x)->with(compact('items'));
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
      'taskno'=>'required',
      'desc'=>'required'
  ];

  $msg=[
      'taskno.required'=>'Task number must not empty',
      'desc.required'=>'Description must not empty'
  ];


  $validator=Validator::make($request->all(),$rules,$msg);

  if ($validator->fails()) {
      return Redirect::to('admin/topics/'.$id.'/edit')
      ->withErrors($validator);

  }else{
    $entity=\App\FlutterTask::find($id);

    $entity->desc=$request->get('desc');
    $entity->taskno=$request->get('taskno');
    $entity->topic=$request->get('topic');
    $entity->save();

    Session::flash('message','Task with Id='.$id.' is changed');

    return Redirect::to('admin/tasks');
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
  $entity = \App\FlutterTask::find($id);
  $entity->delete();
  Session::flash('message','Task with Id='.$id.' is deleted');
  return Redirect::to('admin/tasks');
}
}
