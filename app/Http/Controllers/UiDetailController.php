<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class UiDetailController extends Controller
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

    $entities = \App\StudentSubmit::where('student_submits.userid', '=', $filter)
      ->select(
        'topics.id',
        'topics.name',
        'task_results_group_student.passed',
        'task_results_group_student.failed',
        'task_results_group_student.avg_duration',
        'task_results_group_student.tot_duration',
        'student_submits.checkstat',
        'student_submits.checkresult',
        'student_validations_pertopic.failed as vfailed',
        'student_validations_pertopic.passed as vpassed'
      )
      ->leftJoin(
        'task_results_group_student',
        function ($join) {
          $join->on('task_results_group_student.userid', '=', 'student_submits.userid');
          $join->on('task_results_group_student.topic', '=', 'student_submits.topic');
        }
      )
      ->leftJoin(
        'student_validations_pertopic',
        function ($join2) {
          $join2->on('student_validations_pertopic.userid', '=', 'student_submits.userid');
          $join2->on('student_validations_pertopic.topic', '=', 'student_submits.topic');
        }
      )
      ->join('topics', 'topics.id', '=', 'student_submits.topic')
      ->orderBy('topics.name', 'asc')
      ->get();



    if (Auth::user()->roleid == 'admin') {
      $data = ['entities' => $entities, 'items' => $student, 'filter' => $filter];
      return view('admin/studentres/index')->with($data);
    } else if (Auth::user()->roleid == 'teacher') {
      $data = ['entities' => $entities, 'items' => $student, 'filter' => $filter];
      return view('teacher/studentres/index')->with($data);
    } else { //as student
      $data = ['entities' => $entities];
      return view('student/valid/index')->with($data);
    }
  }

  private function getDataShow($student, $id)
  {
    $entities = \App\UiStudentResultView::where('userid', $student)
      ->where('id', $id)
      ->get();
    $uitestfile = \App\UiTestFiles::where('uitopicid', $entities[0]['uitopicid'])->get();
    $topic = \App\UiTopics::find($entities[0]['uitopicid']);
    $user = \App\User::find($student);
    $data = ['entities' => $entities, 'testfile' => $uitestfile, 'topic' => $topic, 'student' => $user];

    // dd($uitestfile);
    return $data;
  }

  public function show($id)
  {
    $data = $this->getDataShow(Auth::user()->id, $id);

    return view('student/valid/show')->with($data);
  }

  public function showadmin($student, $id)
  {
    //
    $data = $this->getDataShow($student, $id);
    if (Auth::user()->roleid == 'admin') {
      //$data=['entities'=>$entities, 'items'=>$student, 'filter'=>$filter];
      return view('admin/uistudentres/show')->with($data);
    } else if (Auth::user()->roleid == 'teacher') {
      //$data=['entities'=>$entities, 'items'=>$student, 'filter'=>$filter];
      return view('teacher/uistudentres/show')->with($data);
    } else { //as student
      //$data=['entities'=>$entities];
      return view('student/valid/show')->with($data);
    }
    //return view('teacher/studentres/show')->with($data);
  }



  private function getFileSource($student, $topicid, $id)
  {
    $entities = \App\UiFileResults::where('ui_file_results.userid', '=', $student)
      ->select('ui_file_results.codefile', 'ui_topic_files.filename')
      ->join(
        'ui_topic_files',
        function ($join) {
          $join->on('ui_file_results.uicodeid', '=', 'ui_topic_files.id');
        }
      )
      ->where('ui_topic_files.uitopicid', $topicid)
      ->where('ui_file_results.uisubmitid', $id)
      ->orderBy('ui_topic_files.id', 'asc')
      ->get();

    $topic = \App\UiTopics::find($topicid);
    $user = \App\User::find($student);
    $data = ['entities' => $entities, 'topic' => $topic, 'student' => $user];

    return $data;
  }


  public function showsource($student, $topicid, $id)
  {
    //
    $data = $this->getFileSource($student, $topicid, $id);

    if (Auth::user()->roleid == 'admin') {
      //$data=['entities'=>$entities, 'items'=>$student, 'filter'=>$filter];
      return view('admin/uiuploadsrc/index')->with($data);
    } else if (Auth::user()->roleid == 'teacher') {
      //$data=['entities'=>$entities, 'items'=>$student, 'filter'=>$filter];
      return view('teacher/uiuploadsrc/index')->with($data);
    } else { //as student
      //$data=['entities'=>$entities];
      return view('student/uistudentres/show')->with($data);
    }
    //return view('teacher/studentres/show')->with($data);
  }
}

