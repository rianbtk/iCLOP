<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class NodejsFileResultController extends Controller
{

  public function create($id) {
    //
    $topic = \App\NodejsTopic::find($id);
    $files = \App\NodejsTopicFiles::where('topic','=',$id)->get();

    return view('student/nodejscourse/lfiles/create')
      ->with(compact('files'))
      ->with(compact('topic'));
  }

  public function store(Request $request)
  {
    //
    $rules =[
        'rscfile'=>'required'
    ];

    $msg=[
        'rscfile.required'=>'Resource File must not empty'
    ];

    $validator=Validator::make($request->all(),$rules,$msg);

	//jika data ada yang kosong
    if ($validator->fails()) {
        return Redirect::to('student/nodejscourse/lfiles/create/'.$request->get('topic'))
        ->withErrors($validator);
    } else {
        $file = $request->file('rscfile');
        $filename = $file->getClientOriginalName();

        $fileinfo = \App\NodejsTopicFiles::find($request->get('fileid'));
        if ($fileinfo['fileName']!=$filename) {
          return Redirect::to('student/nodejscourse/lfiles/create/'.$request->get('topic'))
          ->withErrors("File name should be ".$fileinfo['fileName']);
        } else {
          $result = \App\NodejsFileResult::where('userid','=',Auth::user()->id)
                ->where('fileid','=',$request->get('fileid'))
                ->get();
          if (count($result)>0) {
            return Redirect::to('student/nodejscourse/lfiles/create/'.$request->get('topic'))
            ->withErrors('File '.$fileinfo['fileName'].' was already submitted');
          } else {
            $rsc=$file->store('resource','public');
            $entity=new \App\NodejsFileResult;

            $entity->userid=Auth::user()->id;
            $entity->fileid=$request->get('fileid');
            $entity->rscfile=$rsc;
            $entity->save();

            Session::flash('message','A New File Result Stored');

            //return "Add new topic is success";
            return Redirect::to('student/nodejscourse/results?topicList='.$fileinfo['topic'])->with( [ 'topic' => $request->get('topic') ] );
          }
        }
    }
  }

  public function destroy(Request $request,$id)
  {
    //
    $entity = \App\NodejsFileResult::find($id);

    $path = storage_path('app\\public\\').$entity['rscfile'];
    //$path = str_replace('\\',DIRECTORY_SEPARATOR,$path);

    //$dirpath = storage_path('app\\public\\\');
    File::delete(getPath($path));

    $entity->delete();
    Session::flash('File Result with Id='.$id.' is deleted');
    return Redirect::to('student/nodejscourse/results?topicList='.$request->get('topic'));
  }


  public function delete($id,$topic)
  {
    //
    $entity = \App\NodejsFileResult::find($id);

    $path = storage_path('app\\public\\').$entity['rscfile'];
    //$path = str_replace('\\',DIRECTORY_SEPARATOR,$path);

    //$dirpath = storage_path('app\\public\\\');
    File::delete($path);

    $entity->delete();
    Session::flash('File Result with Id='.$id.' is deleted');
    return Redirect::to('student/nodejscourse/results?topicList='.$topic.'&option=files');
  }


  public function submit($id) {
    //
    $entity=new \App\NodejsStudentSubmit;

    $entity->userid=Auth::user()->id;
    $entity->topic=$id;
    $entity->validstat="valid";
    $entity->save();

    $topic = \App\NodejsTopic::find($id);
    Session::flash('message','Topic '.$topic['name'].' Validation is Success');

    //return "Add new topic is success";
    return Redirect::to('student/nodejscourse/results?topicList='.$id);

  }

  public function getPath($path) {
    $res = str_replace('\\',DIRECTORY_SEPARATOR,$path);
    return str_replace('/',DIRECTORY_SEPARATOR,$res);
  }
}
