<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class ExerciseResourcesController extends Controller
{
  //
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $stagelist = \App\ExerciseTopic::orderBy('stage', 'asc')->get();
    $exerciselist = \App\ExerciseTopic::orderBy('name', 'asc')->get();

    if ($exerciselist->count() > 0) {

      // daftar stage untuk dropdown menu
      $itemsstage = \App\ExerciseTopic::orderBy('stage', 'asc')
        ->selectRaw('stage, min(id) as id')
        ->groupBy('stage')
        ->pluck('stage', 'id');

      // id stageList dari url
      $filter = $request->input('stageList', $stagelist[0]['id']);

      // id exerciseList dari url
      $filterexercise = $request->input('exerciseList', $exerciselist[0]['id']);

      // get nama stage dari $filter
      $stagename = \App\ExerciseTopic::orderBy('stage', 'asc')
        ->where('id', $filter)
        ->pluck('stage');

      // daftar exercise untuk dropdown menu
      $itemsexercise = \App\ExerciseTopic::orderBy('id', 'asc')
        ->where('stage', $stagename)
        ->pluck('name', 'id');

      if (!($this->checkstage($filter, $filterexercise))) {
        return Redirect::to('admin/exerciseresources?stageList=' . $filter . '&exerciseList=' . $filter);
      }

      $entities = \App\ExerciseTopicFiles::where('exercise', '=', $filterexercise)
        ->select(
          'exercise_topic_files.id',
          'exercise_topic_files.fileName',
          'exercise_topic_files.path',
          'exercise_topic_files.exercise',
          'exercise_topic_files.desc',
          'exercises.name'
        )
        ->join(
          'exercises',
          'exercises.id',
          '=',
          'exercise_topic_files.exercise'
        )
        ->get();

      return view('admin/exerciseresources/index')
        ->with(compact('itemsstage'))
        ->with(compact('itemsexercise'))
        ->with(compact('filter'))
        ->with(compact('filterexercise'))
        ->with(compact('entities'));
    } else {
      return view('admin/exerciseresources/index');
    }
  }

  private function checkstage($stage, $exercise)
  {
    $stagename = \App\ExerciseTopic::orderBy('stage', 'asc')
      ->where('id', $stage)
      ->pluck('stage');
    $exercisestage = \App\ExerciseTopic::orderBy('stage', 'asc')
      ->where('id', $exercise)
      ->pluck('stage');

    if ($stagename == $exercisestage) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    //
    $items = \App\ExerciseTopic::pluck('name', 'id');

    return view('admin/exerciseresources/create')->with(compact('items'));
  }
  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    //
    $rules = [
      'filename' => 'required',
      'path' => 'required'
    ];

    $msg = [
      'filename.required' => 'File Name must not empty',
      'path.required' => 'Folder Path must not empty'
    ];

    $validator = Validator::make($request->all(), $rules, $msg);

    //jika data ada yang kosong
    if ($validator->fails()) {

      //refresh halaman
      return Redirect::to('admin/exerciseresources/create')
        ->withErrors($validator);
    } else {

      $entity = new \App\ExerciseFiles;

      $entity->fileName = $request->get('filename');
      $entity->path = str_replace('\\', '/', $request->get('path'));
      $entity->exercise = $request->get('exercise');
      $entity->desc = $request->get('desc');
      $entity->save();

      Session::flash('message', 'A New Exercise Resource Stored');

      //return "Add new topic is success";
      return Redirect::to('admin/exerciseresources');
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
    $entity = \App\ExerciseFiles::find($id);
    $exercise = \App\ExerciseTopic::find($entity->exercise);
    $x = ['data' => $entity, 'topic' => $exercise];

    return view('admin/exerciseresources/show')->with($x);
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
    $entity = \App\ExerciseFiles::find($id);
    $x = ['data' => $entity];
    $items = \App\ExerciseTopic::pluck('name', 'id');
    return view('admin/exerciseresources/edit')->with($x)->with(compact('items'));
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
    $rules = [
      'filename' => 'required',
      'path' => 'required'
    ];

    $msg = [
      'filename.required' => 'File Name must not empty',
      'path.required' => 'Folder Path must not empty'
    ];


    $validator = Validator::make($request->all(), $rules, $msg);

    if ($validator->fails()) {
      return Redirect::to('admin/exerciseresources/' . $id . '/edit')
        ->withErrors($validator);
    } else {
      $entity = \App\ExerciseFiles::find($id);

      $entity->fileName = $request->get('filename');
      $entity->path = str_replace('\\', '/', $request->get('path'));
      $entity->exercise = $request->get('exercise');
      $entity->desc = $request->get('desc');
      $entity->save();

      Session::flash('message', 'Resource with Id=' . $id . ' is changed');

      return Redirect::to('admin/exerciseresources');
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
    $entity = \App\ExerciseFiles::find($id);
    $entity->delete();
    Session::flash('message', 'Resource with Id=' . $id . ' is deleted');
    return Redirect::to('admin/exerciseresources');
  }
}

