<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class AssignStudentController extends Controller
{
  public function index()
  {
      /*
      $entities=\App\User::where('users.roleid','=','student')
        ->select('users.id', 'users.name', 'users.roleid', 'users.email')
          ->leftJoin('student_teachers','users.id','=','student_teachers.student')
          ->whereNull('student_teachers.teacher')
          ->orderBy('users.name','asc')
          ->get();
      */

      $entities = \App\User::where('uplink','=',Auth::user()->id)->where('status','=','pending')->where('roleid','=','student')
            ->select('id', 'name', 'roleid', 'email')
            ->orderBy('name','asc')->get();
      
      $classroom=\App\Classroom::where('owner','=',Auth::user()->id)
          ->pluck('name', 'id');

      $data=['entities'=>$entities, 'classroom'=>$classroom];

      return view('teacher/assignstudent/index')->with($data);
  }

  public function store(Request $request)
  {

      $students = $request->get('students');
      $x = 0;
      if (is_array($students)) {
        //$classid = $request->get('classroom');
        //$x .= $classid;
        foreach ($students as $std) {
          $user=\App\User::find($std);
          $user->status='active';
          $user->save();

          $member = new \App\ClassMember;
          $member->classid = $request->get('classroom');
          $member->student = $std;
          $member->save();
          $x++;
        }
      }


      
      Session::flash('message','Validation for '.$x.' student(s) was succeed');
      return Redirect::to('teacher/assignstudent/index');
  }

  public function show()
  {
    /*
    $entities=\App\User::where('users.roleid','=','student')
      ->select('users.id', 'users.name', 'users.roleid', 'users.email')
        ->leftJoin('student_teachers','users.id','=','student_teachers.student')
        ->whereNull('student_teachers.teacher')
        ->orderBy('users.name','asc')
        ->get();
    */
        $entities = \App\User::where('uplink','=',Auth::user()->id)->where('status','=','pending')->where('roleid','=','student')
            ->select('id', 'name', 'roleid', 'email')
            ->orderBy('name','asc')->get();

            $classroom=\App\Classroom::where('owner','=',Auth::user()->id)
            ->pluck('name', 'id');

            $data=['entities'=>$entities, 'classroom'=>$classroom];

    return view('teacher/assignstudent/index')->with($data);
  }
}
