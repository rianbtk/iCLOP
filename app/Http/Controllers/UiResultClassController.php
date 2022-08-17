<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UiResultClassController extends Controller
{
  private function getListClass($teacher)
  {
    return \App\Classroom::where('owner', '=', $teacher)
      ->join('users', 'users.id', '=', 'classrooms.owner')
      ->select('users.name',  'classrooms.id', 'classrooms.name as classname')
      ->orderBy('classrooms.name', 'asc')
      ->get();
  }

  private function getListClassAll()
  {
    return \App\Classroom::where('status', '=', 'active')
      ->join('users', 'users.id', '=', 'classrooms.owner')
      ->select('users.name',  'classrooms.id', 'classrooms.name as classname')
      ->orderBy('classrooms.name', 'asc')
      ->get();
  }

  public function index(Request $request)
  {

    if (Auth::user()->roleid == 'admin') {
      $class = $this->getListClassAll();
      $filter = $request->input('stdList', ($class->count() > 0) ? $class[0]['id'] : '');
    } else if (Auth::user()->roleid == 'teacher') {
      $class = $this->getListClass(Auth::user()->id);
      $filter = $request->input('stdList', ($class->count() > 0) ? $class[0]['id'] : '');
    } else { //student
      $check = \App\User::find(Auth::user()->id);
      if ($check->status != 'active') return view('student/home')->with(['status' => $check->status]);
      $filter = Auth::user()->id;
    }

    $subsubQuery = DB::table('ui_student_submits_view')
      ->select(DB::raw('userid, student AS name, count(distinct(topic)) AS passednumber, GROUP_CONCAT(distinct(topic) SEPARATOR "\n") AS topiclist'))
      ->where('checkstat', '=', 'PASSED')
      ->groupBy('userid');

    $entities = DB::table('class_members')
      ->Leftjoin('users', 'class_members.student', '=', 'users.id')
      ->leftJoinSub($subsubQuery, 'B', function ($join) {
        $join->on('class_members.student', '=', 'B.userid');
      })
      ->select(DB::raw('users.id AS userid, users.name, B.passednumber, B.topiclist'))
      ->where('class_members.classid', '=', $filter)
      ->orderBy('users.name', 'asc')
      ->get();

    $topiccount = \App\UiTopics::where('status', '=', '1')
      ->count('id');

    if (Auth::user()->roleid == 'admin') {
      $data = ['entities' => $entities, 'topiccount' => $topiccount, 'items' => $class, 'filter' => $filter];
      return view('admin/uiclasssummary/index')->with($data);
    } else if (Auth::user()->roleid == 'teacher') {
      $data = ['entities' => $entities, 'topiccount' => $topiccount, 'items' => $class, 'filter' => $filter];
      return view('teacher/uiclasssummary/index')->with($data);
    } else { //as student
      $data = ['entities' => $entities];
      return view('student/valid/index')->with($data);
    }
  }
}

