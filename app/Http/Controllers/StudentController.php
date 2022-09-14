<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
  public function index() {
    return view('student/main');
  }
  public function androidcourse() {
    return view('student/androidcourse/main');
  }
  public function androidcoursetopic() {
    return view('student/androidcourse/topic');
  }
  public function fluttercourse() {
    return view('student/fluttercourse/main');
  }
  public function fluttercoursetopic() {
    return view('student/fluttercourse/topic');
  }
  public function unitycourse() {
    return view('student/unitycourse/main');
  }
  public function unitycoursetopic() {
    return view('student/unitycourse/topic');
  }
  public function nodejscourse() {
    return view('student/nodejscourse/main');
  }
  public function nodejscoursetopic() {
    return view('student/nodejscourse/topic');
  }
  public function pythoncourse() {
    return view('student/pythoncourse/main');
  }

  // public function asynctask() {
  //   return view('student/androidcourse/asynctask/index');
  // }
  // public function firebase() {
  //   return view('student/androidcourse/firebase/index');
  // }
  
  // unity interface
//   public function unitycourse() {
//     return view('student/unitycourse/main');
//   }
//   public function unitycoursetest() {
//     return view('student/unitycourse/course/index');
//   }
//   public function unitycoursepage() {
//     return view('student/unitycourse/page/index');
//   }
//     public function nodejscourseBasicHTML() {
//     return view('student/nodejscourse/basicHTML/index');
//   }
//   public function nodejscourseDynamicContent() {
//     return view('student/nodejscourse/DynamicContent/index');
//   }
}
