<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class AndroidResultController extends Controller
{
  public function index(Request $request) {
    //$check=\App\StudentTeacher::where('student','=',Auth::user()->id);
    //if ($check->count()==0) return view('student/home')->with(['count'=>$check->count()]);
$check=\App\User::find(Auth::user()->id);
if ($check->status!='active') return view('student/androidcourse/home')->with(['status'=>$check->status]);

      $filter = $request->input('topicList','6');
      if ($filter=='0') {
        $entities=\App\AndroidTaskResult::where('userid','=',Auth::user()->id);
      } else {
        $entities = \App\AndroidTask::where('tasks.topic','=',$filter)
              ->select(
                  'task_results.id',
                  'task_results.taskid',
                  'task_results.userid',
                  'task_results.status',
                  'task_results.duration',
                  'task_results.comment',
                  'task_results.imgFile',
                  'tasks.taskno',
                  'tasks.desc',
                  'tasks.topic'
              )
              ->leftJoin('task_results', function($join)
                    {
                      $join->on('tasks.id','=','task_results.taskid')
                      ->where('task_results.userid', '=', Auth::user()->id);
                    }
                  )
              ->orderBy('tasks.taskno', 'asc')
              ->get();
      }

      $lfiles = \App\AndroidTopicFiles::where('topic_files.topic','=',$filter)
            ->select(
                'file_results.id',
                'file_results.userid',
                'file_results.rscfile',
                'file_results.fileid',
                'topic_files.fileName',
                'topic_files.path',
                'topic_files.desc'
            )
            ->leftJoin('file_results', function($join)
                  {
                    $join->on('topic_files.id','=','file_results.fileid')
                    ->where('file_results.userid', '=', Auth::user()->id);
                  }
                )
            ->orderBy('topic_files.fileName', 'asc')
            ->get();

    $items = \App\AndroidTopic::
    where('status','>=','0')
	->where('androidclass','=','AndroidX')
        ->orderBy('name','asc')
        ->orderBy('level','asc')
        ->pluck('name', 'id');

      $valid = \App\AndroidStudentSubmit::where('userid','=',Auth::user()->id)
              ->where('topic','=',$filter)
              ->get()->count();

	$option = $request->input('option','github');

	$currtopic = \App\AndroidTopic::find($filter);
 
      return view('student/androidcourse/results/index')
        ->with(compact('entities'))
        ->with(compact('lfiles'))
        ->with(compact('items'))
        ->with(compact('filter'))
	->with(compact('option'))
	->with(compact('currtopic'))
        ->with(compact('valid'));

  }



  public function getTaskData($topic) {
    $items = \App\AndroidTask::where('tasks.topic','=',$topic)
          ->select(
              'tasks.id',
              'tasks.taskno',
              'tasks.desc',
              'topics.name'
          )
          ->join(
              'topics',
              'topics.id','=','tasks.topic'
          )
          ->orderBy('topics.name', 'asc')
          ->orderBy('tasks.taskno', 'asc')
          ->get();

    return $items;
  }
  public function create($id)
  {
      $items = \App\AndroidTask::where('topic','=',$id)
        ->orderBy('taskno', 'asc')
        ->get();
      $topic = \App\AndroidTopic::find($id);
      return view('student/androidcourse/results/create')
        ->with(compact('topic'))
        ->with(compact('items'));
  }

private function validateByFiles($userid, $topic) {
    //
    $entity=new \App\AndroidStudentSubmit;

    $entity->userid=$userid;
    $entity->topic=$topic;
    $entity->validstat="valid";
    $entity->save();

    $data = \App\AndroidTopic::find($topic);
    Session::flash('message','Topic '.$data['name'].' Validation is Success');

    //return "Add new topic is success";
    return Redirect::to('student/androidcourse/results?topicList='.$topic.'&option=files');
}

private function validateZipFile($userid, $topic, $file, $path) {
    //
    //$file = $request->file('zipfile');
    if ($path!='' ) {
	//$array = explode('.', $path);
	//$ext = strtolower(end($array));
      $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      if ($ext=="zip") {
        $zipFile=$file->store('results','public');

	if ($zipFile!='') {
	   $entity=new \App\AndroidStudentSubmit;

    	   $entity->userid=$userid;
    	   $entity->topic=$topic;
    	   $entity->validstat="valid";
	   $entity->projectfile=$zipFile;

    	   $entity->save();

    	   $data = \App\AndroidTopic::find($topic);
    	   Session::flash('message','Topic '.$data['name'].' Validation by Uploading Zip Project is Success');
	} else {
 	   Session::flash('message','Storing file '.$request->file('zipfile').' was FAILED');
	}
      } else {
	Session::flash('message','File extension is not zip -> '.$path.' is wrong .'.$ext);
      }
    } else {
	Session::flash('message','Zip File is empty');
    } 


    //return "Add new topic is success";
    return Redirect::to('student/androidcourse/results?topicList='.$topic.'&option=zipfile');
}


private function validateGithubLink($userid, $topic, $link, $projname) {
    //
    $trimmedlink = trim($link);
    if ($this->validateUrl($trimmedlink,$projname)) {
      /*
        $zipFile=$file->store('results','public');

        if ($zipFile!='') {
           $entity=new \App\AndroidStudentSubmit;

           $entity->userid=$userid;
           $entity->topic=$topic;
           $entity->validstat="valid";
           $entity->projectfile=$zipFile;

           $entity->save();

           $data = \App\AndroidTopic::find($topic);
           Session::flash('message','Topic '.$data['name'].' Validation is Success');
        } else {
           Session::flash('message','Storing file '.$request->file('zipfile').' was FAILED');
        }
	*/
	$entity=new \App\AndroidStudentSubmit;

        $entity->userid=$userid;
        $entity->topic=$topic;
        $entity->validstat="valid";
        $entity->githublink=$trimmedlink;

        $entity->save();

        $data = \App\AndroidTopic::find($topic);
        Session::flash('message','Topic '.$data['name'].' Validation by submitting GitHub link is Success');

	//Session::flash('message','URL valid '.$link);

    } else {
        Session::flash('message','URL is not VALID '.$link);
    }


    //return "Add new topic is success";
    return Redirect::to('student/androidcourse/results?topicList='.$topic.'&option=github');
}

private function validateUrl($url,$projname) {
    $path = parse_url($url, PHP_URL_PATH);
    $encoded_path = array_map('urlencode', explode('/', $path));
    $url = str_replace($path, implode('/', $encoded_path), $url);

    if (filter_var($url, FILTER_VALIDATE_URL)) {
	$result = parse_url($url);
	if ( ($result['scheme']=='https') && ($this->endsWith($result['host'],'github.com')) 	
	&& (strpos($result['path'],$projname)) ) {
	  return true;
	} else {
	  return false;
	}
   } else {
	return false;
   }
}

private function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}


  private function saveTaskResult(Request $request)
  {
      //
      $rules =[
          'duration'=>'required',
          'image'=>'required',
	'comment'=>'required'
      ];

      $msg=[
          'duration.required'=>'Duration time must not empty',
          'image.required'=>'Evidence image file must not empty',
	  'comment.required'=>'Comment must not empty'
      ];

      $validator=Validator::make($request->all(),$rules,$msg);

      //jika data ada yang kosong
      if ($validator->fails()) {

          //refresh halaman
          return Redirect::to('student/androidcourse/results/create/'.$request->get('topic'))
          ->withErrors($validator);

      } else {
        $check = \App\AndroidTaskResult::where('userid','=',Auth::user()->id)
                ->where('taskid','=',$request->get('taskid'))
                ->get();

        if (sizeof($check)>0) {
          $task = \App\AndroidTask::find($request->get('taskid'));
          $message = 'Result of Task '.$task['desc'].' is already submitted!!';
          //Session::flash('message',);
          return Redirect::to('student/androidcourse/results/create'.$request->get('topic'))->withErrors($message);

        } else {
          $file = $request->file('image');
          $imgFile=$file->store('results','public');

          $entity=new \App\AndroidTaskResult;
	
	$comment = ($request->get('comment')==null)?'-':$request->get('comment');

          $entity->userid=Auth::user()->id;
          $entity->taskid=$request->get('taskid');
          $entity->status=$request->get('status');
          $entity->duration=$request->get('duration');
          $entity->comment=$comment;
          $entity->imgFile=$imgFile;
          $entity->save();

          Session::flash('message','A New Task Result Stored');

          //return "Add new topic is success";
          return Redirect::to('student/androidcourse/results?topicList='.$request->get('topic'))->with( [ 'topic' => $request->get('topic') ] );
        }
      }
  }


  public function store(Request $request)
  {
        if (strlen($request->get('option'))>3) {
          if (($request->get('action')=='validate') && (strlen($request->submitbutton)>5)) {
                if ($request->get('option')=='files') {
                  return $this->validateByFiles(Auth::user()->id, $request->get('topic'));
                } else if ($request->get('option')=='zipfile') {
		  $file = $request->file('zipfile');
		  $filename = $file->getClientOriginalName();
                  return $this->validateZipFile(Auth::user()->id, $request->get('topic'), $file, $filename);
		} else if ($request->get('option')=='github') {
		  return $this->validateGithubLink(Auth::user()->id, $request->get('topic'), $request->get('githublink'), 
			$request->get('projname'));
                } else {
                  return Redirect::to('student/androidcourse/results?topicList='.$request->get('topic').'&option='.$request->get('option').
                  '&submit='.$request->submitbutton);
                }
          } else { //clicking radio button
                return Redirect::to('student/androidcourse/results?topicList='.$request->get('topic').'&option='.$request->get('option'));
                //'&submit='.$request->submitbutton);
          }

        } else {   //echo $request;
                return $this->saveTaskResult($request);
        }
  }


  public function destroy(Request $request, $id)
  {
      //
      $entity = \App\AndroidTaskResult::find($id);
      $entity->delete();
      Session::flash('message','Task Result with Id='.$id.' is deleted');
      return Redirect::to('student/androidcourse/results?topicList='.$request->get('topic'));
  }

  public function edit($id)
  {
    //
    $entity = \App\AndroidTaskResult::where('id','=',$id)->first();
    $task = \App\AndroidTask::where('id','=',$entity['taskid'])->first();
    return view('student/androidcourse/results/edit')->with(compact('entity'))
      ->with(compact('task'));
  }

  public function valsub(Request $request)
  {
      $items = \App\AndroidTask::where('topic','=',$id)
        ->orderBy('taskno', 'asc')
        ->get();
      $topic = \App\AndroidTopic::find($id);
      return view('student/androidcourse/results/create')
        ->with(compact('topic'))
        ->with(compact('items'));
  }


  public function update(Request $request, $id) {
    //
    $rules =[
        'duration'=>'required',
    ];

    $msg=[
        'duration.required'=>'Duration time must not empty',
    ];


    $validator=Validator::make($request->all(),$rules,$msg);

    if ($validator->fails()) {
        return Redirect::to('student/androidcourse/results/'.$id.'/edit')
        ->withErrors($validator);

    }else{
      $file = $request->file('image');

      $entity=\App\AndroidTaskResult::find($id);

      $entity->taskid=$request->get('taskid');
      $entity->status=$request->get('status');
      $entity->duration=$request->get('duration');
      $entity->comment=$request->get('comment');

      if ($file!='') {
        $imgFile=$file->store('results','public');
        $entity->imgFile=$imgFile;
      }
      $entity->save();

      Session::flash('message','Task Result with Id='.$id.' is changed');

      $task = \App\AndroidTask::find($request->get('taskid'));
      return Redirect::to('student/androidcourse/results?topicList='.$task['topic']);
    }
  }
}

