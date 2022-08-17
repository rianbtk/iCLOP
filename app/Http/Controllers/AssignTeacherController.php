<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use Session;

class AssignTeacherController extends Controller
{
  public function index()
  {
      //
      //$entities=\App\User::where('roleid','=','student')
      //  ->orderBy('name','asc')
      //  ->get();
	
	$entities=\App\User::where('users.roleid','=','student')
        ->select('users.id','users.name','users.email','users.roleid','users.uplink','users.status','B.name as teacher')
        ->leftJoin('users as B', 'users.uplink', '=', 'B.id')
        ->orderBy('users.name','asc')
        ->get();

      $data=['entities'=>$entities];

      return view('admin/assignteacher/index')->with($data);
  }

  public function edit($id)
  {
      $entity=\App\User::find($id);

      $entity->roleid='teacher';
      $entity->status='active';
	$entity->save();

      $classroom = new \App\Classroom;
      $classroom->name='Default';
      $classroom->owner=$id;
      $classroom->desc='Default class for teacher '.$entity['name'];
      $classroom->save();

	

      Session::flash('message','User with name = '.$entity['name'].' now as a teacher and Default classroom was created');
      return Redirect::to('/admin/assignteacher/index');
  }

  public function show()
  {
    $entities=\App\User::where('roleid','=','student')
      ->orderBy('name','asc')
      ->get();

    $data=['entities'=>$entities];

    return view('admin/assignteacher/index')->with($data);
  }
}
