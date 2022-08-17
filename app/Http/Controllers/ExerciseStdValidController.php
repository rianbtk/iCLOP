<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseStdValidController extends Controller
{
  public function index(Request $request)
  {
    if (Auth::user()->roleid == 'student') {
      $check = \App\User::find(Auth::user()->id);
      if ($check->status != 'active') return view('student/home')->with(['status' => $check->status]);
    }

    $entities = \App\ExerciseSubmit::where('exercise_submits.userid', Auth::user()->id)
      ->select(
        'exercise_submits.id',
        'exercises.name',
        'exercise_submits.checkstat',
        'exercise_validations.report',
        'exercise_submits.duration'
      )
      ->leftJoin('exercise_validations', 'exercise_validations.exercisesubmitid', '=', 'exercise_submits.id')
      ->leftJoin('exercises', 'exercise_submits.exercise', '=', 'exercises.id')
      ->get();

    return view('student/exercisevalid/index')
      ->with(compact('entities'));
  }

  public function show($id)
  {
    $entities = \App\ExerciseSubmit::where('exercise_submits.userid', Auth::user()->id)
      ->where('exercise_submits.id', $id)
      ->select(
        'exercise_submits.id',
        'exercises.name',
        'exercises.singletestcode',
        'exercise_submits.checkstat',
        'exercise_validations.report',
        'exercise_validations.duration',
        'exercise_validations.created_at'
      )
      ->leftJoin('exercise_validations', 'exercise_validations.exercisesubmitid', '=', 'exercise_submits.id')
      ->leftJoin('exercises', 'exercise_submits.exercise', '=', 'exercises.id')
      ->get();

    $student = \App\User::find(Auth::user()->id);

    return view('student/exercisevalid/show')
      ->with(compact('entities'))
      ->with(compact('student'));
  }
}

