<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentPassedResultClassController extends Controller
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

    $subsubQuery = DB::table('student_submits_view')
      ->select(DB::raw("userid, student AS name, count(distinct(topic)) AS passednumber, GROUP_CONCAT(concat(topic,' [',checkstat,']') ORDER BY `checkstat` SEPARATOR '\\n') AS topiclist"))
      ->where("checkstat", "PASSED")
      ->groupBy('userid')
      ->orderBy('topiclist', 'asc');

    $entities = DB::table('class_members')
      ->Leftjoin('users', 'class_members.student', '=', 'users.id')
      ->LeftJoin('student_submits_stat', 'class_members.student', '=', 'student_submits_stat.userid')
      ->leftJoinSub($subsubQuery, 'B', function ($join) {
        $join->on('class_members.student', '=', 'B.userid');
      })
      ->select(DB::raw('users.id AS userid, users.name, student_submits_stat.subs_passed AS passed, student_submits_stat.subs_failed AS failed, student_submits_stat.subs_error AS error, B.topiclist'))
      ->where('class_members.classid', '=', $filter)
      ->orderBy('users.name', 'asc')
      ->get();

    // $entities = DB::table(DB::raw('(select * from `student_submits_view` order by checkstat DESC, topic ASC) AS A'))
    //   ->select(DB::raw("student, count(*) AS totalpassed, GROUP_CONCAT(concat(topic,' [',checkstat,']') SEPARATOR '\\n') AS topiclist"))
    //   ->groupBy('student')
    //   ->get();


    if (Auth::user()->roleid == 'admin') {
      $data = ['entities' => $entities, 'items' => $class, 'filter' => $filter];
      return view('admin/studentpassedresult/index')->with($data);
    } else if (Auth::user()->roleid == 'teacher') {
      $data = ['entities' => $entities, 'items' => $class, 'filter' => $filter];
      return view('teacher/studentpassedresult/index')->with($data);
    } else { //as student
      $data = ['entities' => $entities];
      return view('student/valid/index')->with($data);
    }
  }
}

