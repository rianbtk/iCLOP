<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Redirect;
use Session;

class JplasDownloadController extends Controller
{

public function index(Request $request)
   {
        //
	/*
        $entities=\App\StudentRank::orderBy('score','desc')
                ->orderBy('last_submit','asc')
                ->offset(0)->limit(20)->get();
        $data=['entities'=>$entities];
        if (Auth::user()->roleid == "admin") {
                return view('admin/rankview/index')->with($data);
        } else if (Auth::user()->roleid == "teacher") {
                return view('teacher/rankview/index')->with($data);
        } else {
                return view('student/rankview/index')->with($data);
        }*/

	if (Auth::user()->roleid == "admin") {

	} else if (Auth::user()->roleid == "teacher") {
		$filter = $request->input('stdList',1);

		$entities=\App\JplasResult::where('jplas_results.jplasid','=',$filter)
                ->select('jplas_results.id','jplas_results.filename','jplas_results.comment','jplas_results.filepath',
			'jplas_results.userid as userid','std.name as stdname','classrooms.name as cname','tc.name as tcname',
			'jplas_results.created_at','jplas_results.jplasid')
                ->join('class_members','jplas_results.userid','=','class_members.student')
		->join('classrooms','classrooms.id','=','class_members.classid')
		->join('users as std','std.id','=','jplas_results.userid')
		->join('users as tc','tc.id','=','std.uplink')
		->where('jplas_results.userid','>',5)
		->orderBy('jplas_results.created_at','desc')
                ->get();
		
		$items = \App\JplasTopic::orderBy('id','asc')->get();
	
		$data=['entities'=>$entities, 'items'=>$items, 'filter'=>$filter];

                return view('teacher/jplasdown/index')->with($data);
        } else {

	 $entities=\App\JplasTopic::orderBy('jplas_topics.id','asc')
		->select('jplas_topics.id','jplas_topics.name','jplas_topics.packfile','jplas_topics.docfile',
			'jplas_results.filename','jplas_results.comment')
		->leftJoin('jplas_results',function($join) {
			$join->on('jplas_topics.id','=','jplas_results.jplasid');
			$join->on('jplas_results.userid','=',DB::raw("'".Auth::user()->id."'"));
		})
		->get();
        $data=['entities'=>$entities];

	return view('student/jplasdown/index')->with($data);
	}
    }

public function create() {
      $items = \App\JplasTopic::orderBy('id', 'asc')
        ->get();

      return view('student/jplasdown/create')->with(compact('items'));
}


public function store(Request $request)
  {
      //
      $rules =[
          'comment'=>'required',
          'image'=>'required'
      ];

      $msg=[
          'comment.required'=>'Comment must not empty',
          'image.required'=>'Result file must not empty'
      ];

      $validator=Validator::make($request->all(),$rules,$msg);

      //jika data ada yang kosong
      if ($validator->fails()) {

          //refresh halaman
          return Redirect::to('student/jplasdown/create')
          ->withErrors($validator);

      } else {
        $check = \App\JplasResult::where('userid','=',Auth::user()->id)
                ->where('jplasid','=',$request->get('taskid'))
                ->get();
				
	$task = \App\JplasTopic::find($request->get('taskid'));
        if (sizeof($check)>0) {
		$entity = \App\JplasResult::find($check[0]['id']);
          $message = 'The Result of Lesson '.$task['name'].' was updated!!';

        } else {
		$entity=new \App\JplasResult;
		$message = 'The Result of Lesson '.$task['name'].' was stored!!';

	}
          $file = $request->file('image');
          $imgFile=$file->store('results','public');

          //$entity=new \App\JplasResult;

          $entity->userid=Auth::user()->id;
          $entity->jplasid=$request->get('taskid');
          $entity->filename=$request->file('image')->getClientOriginalName();
          $entity->filepath=$imgFile;
          $entity->comment=$request->get('comment');
          $entity->save();

          Session::flash('message',$message);

          return Redirect::to('student/jplasdown/');
        
      }
  }


}
