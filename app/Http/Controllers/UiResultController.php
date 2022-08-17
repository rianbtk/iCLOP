<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Redirect;
use Session;

class UiResultController extends Controller
{
  private function getListStudent($teacher)
  {
    return \App\User::where('users.uplink', '=', $teacher)
      ->join('user_submits', 'user_submits.userid', '=', 'users.id')
      ->join('class_members', 'class_members.student', '=', 'users.id')
      ->join('classrooms', 'classrooms.id', '=', 'class_members.classid')
      ->join('users as x', 'x.id', '=', 'users.uplink')
      ->select('users.id', 'users.name', 'users.email', 'users.roleid', 'classrooms.name as classname', 'x.name as teacher')
      ->orderBy('users.uplink', 'asc')
      ->orderBy('class_members.classid', 'asc')
      ->orderBy('users.name', 'asc')
      ->get();
  }

  private function getListStudentAll()
  {
    return \App\User::where('users.status', '=', 'active')

      ->join('user_submits', 'user_submits.userid', '=', 'users.id')
      ->join('class_members', 'class_members.student', '=', 'users.id')
      ->join('classrooms', 'classrooms.id', '=', 'class_members.classid')
      ->join('users as x', 'x.id', '=', 'users.uplink')
      ->select('users.id', 'users.name', 'users.email', 'users.roleid', 'classrooms.name as classname', 'x.name as teacher')
      ->orderBy('users.uplink', 'asc')
      ->orderBy('class_members.classid', 'asc')
      ->orderBy('users.name', 'asc')
      ->get();
  }

  public function index(Request $request)
  {

    if (Auth::user()->roleid == 'admin') {
      $student = $this->getListStudentAll();
      $filter = $request->input('stdList', ($student->count() > 0) ? $student[0]['id'] : '');
    } else if (Auth::user()->roleid == 'teacher') {
      $student = $this->getListStudent(Auth::user()->id);
      $filter = $request->input('stdList', ($student->count() > 0) ? $student[0]['id'] : '');
    } else { //student
      $check = \App\User::find(Auth::user()->id);
      if ($check->status != 'active') return view('student/home')->with(['status' => $check->status]);
      $filter = Auth::user()->id;
    }


    $subsubQuery = DB::table('ui_student_submits')
      ->select(DB::raw('uitopic AS uitopicnow,Count(id) as jumlah_submit, max(checkresult) as checkresult, max(checkstat) AS checkstat, max(created_at) AS createdate'))
      ->where('userid', '=', $filter)
      ->groupBy('uitopicnow');

    $entities = DB::table('ui_topics')
      ->leftJoinSub($subsubQuery, 'B', function ($join) {
        $join->on('ui_topics.id', '=', 'B.uitopicnow');
      })
      ->where('ui_topics.status', '=', '1')
      ->select(DB::raw('ui_topics.id, name, jumlah_submit, checkresult AS checkresult, UPPER(checkstat) AS checkstat'))
      ->distinct()
      ->orderBy('ui_topics.id', 'asc')
      ->get();

    if (Auth::user()->roleid == 'admin') {
      $data = ['entities' => $entities, 'items' => $student, 'filter' => $filter];
      return view('admin/uisummaryres/index')->with($data);
    } else if (Auth::user()->roleid == 'teacher') {
      $data = ['entities' => $entities, 'items' => $student, 'filter' => $filter];
      return view('teacher/uisummaryres/index')->with($data);
    } else { //as student
      $data = ['entities' => $entities];
      return view('student/valid/index')->with($data);
    }
  }
}

