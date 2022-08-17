<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class LearningFileController extends Controller {

  protected $filepath = 'public';
  protected $dirname = 'learning';


  public function index(Request $request) {
        $entities = \App\LearningFile::select(
                  'learning_files.id',
                  'learning_files.guide',
                  'learning_files.testfile',
                  'learning_files.supplement',
                  'learning_files.other',
                  'learning_files.desc',
                  'topics.name'
              )
              ->join(
                  'topics',
                  'topics.id','=','learning_files.topic'
              )
              ->orderBy('topics.name','asc')
              ->get();

      //$items = \App\Topic::all();
      if (Auth::user()->roleid=='admin') {
        return view('admin/learning/index')->with(compact('entities'));
      } else {
        return view('student/lfiles/index')->with(compact('entities'));
      }
  }

  public function create()
  {
    //
    $items = \App\Topic::pluck('name', 'id');

    return view('admin/learning/create')->with(compact('items'));
  }

  public function store(Request $request)
  {
    //
    $rules =[
        'guide'=>'required',
        'testfile'=>'required',
        'supplement'=>'required'
    ];

    $msg=[
        'guide.required'=>'Guide file must not empty',
        'testfile.required'=>'Test file must not empty',
        'supplement.required'=>'Supplement file must not empty'
    ];

    $validator=Validator::make($request->all(),$rules,$msg);

    //jika data ada yang kosong
    if ($validator->fails()) {

        //refresh halaman
        return Redirect::to('admin/learning/create')
        ->withErrors($validator);

    } else {
        $check = \App\LearningFile::where('topic','=',$request->get('topic'))->get();

        if (sizeof($check)>0) {
          $topic = \App\Topic::find($request->get('topic'));
          $message = 'Learning File of '.$topic['name'].' is already exist!!';
          //Session::flash('message',);
          return Redirect::to('admin/learning/create')->withErrors($message);

        } else {
          $entity=new \App\LearningFile;

          $entity->topic=$request->get('topic');



          $file1=$request->file('guide')->store($this->dirname,$this->filepath);
          $entity->guide=$file1;

          $file2=$request->file('testfile')->store($this->dirname,$this->filepath);
          $entity->testfile=$file2;

          $file3=$request->file('supplement')->store($this->dirname,$this->filepath);
          $entity->supplement=$file3;

          $file4=$request->file('other');
          if ($file4 != '') {
            $file4=$request->file('other')->store($this->dirname,$this->filepath);
            $entity->other=$file4;
          }
          $entity->save();

          Session::flash('message','A New Learning File Stored');

          return Redirect::to('admin/learning');
        }
    }
  }

  public function getTopic($fileid) {
    $item = \App\LearningFile::find($fileid);
    return $item->topic;
  }

  public function edit($id)
  {
      //
      $topic = \App\Topic::find($this->getTopic($id));
      $x=['data'=>$topic];
      $fileid = $id;
      return view('admin/learning/edit')->with($x)->with(compact('fileid'));
  }

  public function update(Request $request, $id) {
    $entity=\App\LearningFile::find($id);
    $change = false;

    $dirpath = storage_path($this->dirname);
    $file1 = $request->file('guide');
    if ($file1!='') {
     // File::delete(getPath($dirpath.$entity->guide));
      $fname=$file1->store($this->dirname,$this->filepath);
      $entity->guide=$fname;
      $change=true;
    }

    $file2 = $request->file('testfile');
    if ($file2!='') {
      //File::delete(getPath($dirpath.$entity->testfile));
      $fname=$file2->store($this->dirname,$this->filepath);
      $entity->testfile=$fname;
      $change=true;
    }

    $file3 = $request->file('supplement');
    if ($file3!='') {
      //File::delete(getPath($dirpath.$entity->supplement));
      $fname=$file3->store($this->dirname,$this->filepath);
      $entity->supplement=$fname;
      $change=true;
    }

    $file4 = $request->file('other');
    if ($file4!='') {
      //File::delete(getPath($dirpath.$entity->other));
      $fname=$file4->store($this->dirname,$this->filepath);
      $entity->other=$fname;
      $change=true;
    }

    if ($change) {
      $entity->save();
      Session::flash('message','A Learning File is changed');
    } else {
      Session::flash('message','Nothing is changed');
    }
    return Redirect::to('admin/learning');
  }

  public function destroy($id)
  {
    //
    $entity = \App\LearningFile::find($id);
/*
    $dirpath = storage_path('app\public\\');
    File::delete(getPath($dirpath.$entity['guide']));
    File::delete(getPath($dirpath.$entity['supplement']));
    File::delete(getPath($dirpath.$entity['testfiles']));
    if ($entity['other']!='') {
      File::delete(getPath($dirpath.$entity['other']));
    }
*/
    $entity->delete();
    Session::flash('message','Learning Files with Id='.$id.' is deleted');
    return Redirect::to('admin/learning');
  }

  public function getPath($path) {
    $res = str_replace('\\',DIRECTORY_SEPARATOR,$path);
    return str_replace('/',DIRECTORY_SEPARATOR,$res);
  }
}
