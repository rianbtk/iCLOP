<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Redirect;
use Session;
use File;

class UiTestFilesController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $entities = \App\UiTestFiles::join('ui_topics', 'ui_test_files.uitopicid', '=', 'ui_topics.id')
      ->select(
        'ui_test_files.id',
        'ui_test_files.uitopicid',
        'ui_test_files.testfile',
        'ui_test_files.filename',
        'ui_topics.name',
        'ui_topics.description'
      )
      ->orderBy('ui_test_files.uitopicid', 'asc')
      ->get();

    return view('admin/uitestfiles/index')->with(compact('entities'));
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    //
    $topics = \App\UiTopics::all();
    return view('admin/uitestfiles/create')
      ->with(compact('topics'));
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
      'testFile' => 'required'
    ];

    $msg = [
      'testFile.required' => 'Test File must not empty'
    ];

    $validator = Validator::make($request->all(), $rules, $msg);

    //jika data ada yang kosong
    if ($validator->fails()) {

      //refresh halaman
      return Redirect::to('admin/uitestfiles/create/')
        ->withErrors($validator);
    } else {
      $file = $request->file('testFile');
      $filename = $file->getClientOriginalName();
      $testFile = $file->storeAs('/public/uitestfiles', Str::uuid().'.html');
      //$testFile = $file->store('uitestfiles', 'public');
      $maxId = DB::table('ui_test_files')->max('id');

      $entity = new \App\UiTestFiles();

      $entity->id = $maxId + 1;
      $entity->uitopicid = $request->get('taskid');
      $entity->filename = $filename;
      $entity->testfile = str_replace('public/', '', $testFile);
      $entity->save();

      Session::flash('message', 'A New Test File Stored');

      //return "Add new topic is success";
      return Redirect::to('admin/uitestfiles/');
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
    $entity = \App\UiTestFiles::find($id);
    $topic = \App\UiTopics::find($entity->uitopicid);

    $x = ['data' => $entity, 'topic' => $topic];
    return view('admin/uitestfiles/show')->with($x);
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
    //$entity = \App\TestFiles::find($id);
    //$x = ['data' => $entity];
    //$items = \App\Topic::pluck('name', 'id');
    //return view('admin/testfiles/edit')->with($x)->with(compact('items'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request, $id)
  {
    //NOT USED (TIDAK DIGUNAKAN)
    $rules = [
      'testno' => 'required'
    ];

    $msg = [
      'testno.required' => 'Test number must not empty'
    ];


    $validator = Validator::make($request->all(), $rules, $msg);

    if ($validator->fails()) {
      return Redirect::to('admin/testfiles/' . $id . '/edit')
        ->withErrors($validator);
    } else {
      $file = $request->file('testFile');

      $entity = \App\TestFiles::find($id);

      $entity->testno = $request->get('testno');
      if ($file != '') {
        $filename = $file->getClientOriginalName();
        $strpath = 'TestFiles/' . $request->get('topic');
        $testFile = $file->store('testfiles', 'public');

        $entity->fileName = $filename;
        $entity->content = $testFile;
      }

      $entity->topic = $request->get('topic');
      $entity->save();

      Session::flash('message', 'Test File with Id=' . $id . ' is changed');

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
    $entity = \App\UiTestFiles::find($id);
    $entity->delete();
    Session::flash('message', 'Test File with Id=' . $id . ' is deleted');
    return Redirect::to('admin/uitestfiles');
  }

  public function getPath($path)
  {
    $res = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    return str_replace('/', DIRECTORY_SEPARATOR, $res);
  }
}

