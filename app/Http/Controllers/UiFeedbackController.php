<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class UiFeedbackController extends Controller
{
  public function create($id)
  {
    $userid = Auth::user()->id;
    $taskid = $id;
    $result = \App\UiTaskResult::where('userid', $userid)
      ->select('id', 'duration', 'comment')
      ->where('taskid', $taskid)
      ->get();
    return view('student/uifeedback/create')
      ->with(compact('result'))
      ->with(compact('taskid'));
  }

  private function saveTaskResult(Request $request)
  {
    //
    $rules = [
      'duration' => 'required',
      'comment' => 'required'
    ];

    $msg = [
      'duration.required' => 'Duration time must not empty',
      'comment.required' => 'Comment must not empty'
    ];

    $validator = Validator::make($request->all(), $rules, $msg);
    //jika data ada yang kosong
    if ($validator->fails()) {
      //refresh halaman
      return Redirect::to('student/uifeedback/' . $request->get('topic'))
        ->withErrors($validator);
    } else {
      $entity = new \App\UiTaskResult();

      $comment = ($request->get('comment') == null) ? '-' : $request->get('comment');

      $entity->userid = Auth::user()->id;
      $entity->taskid = $request->get('topic');
      $entity->duration = $request->get('duration');
      $entity->comment = $comment;
      $entity->save();

      Session::flash('message', 'A New Task Result Stored');

      //return "Add new topic is success";
      return Redirect::to('student/uifeedback/' . $request->get('topic'));
    }
  }


  public function store(Request $request)
  {
    $userid = Auth::user()->id;
    $taskid = $request->get('topic');;
    $result = \App\UiTaskResult::where('userid', $userid)
      ->select('id', 'duration', 'comment')
      ->where('taskid', $taskid)
      ->get();

    if (empty($result[0])) {
      return $this->saveTaskResult($request);
    } else {
      return $this->update($request, $result[0]['id']);
    }
  }

  public function update(Request $request, $id)
  {
    //
    $rules = [
      'duration' => 'required',
      'comment' => 'required'
    ];

    $msg = [
      'duration.required' => 'Duration time must not empty',
      'comment.required' => 'Comment must not empty'
    ];


    $validator = Validator::make($request->all(), $rules, $msg);

    if ($validator->fails()) {
      return Redirect::to('student/uifeedback/' . $request->get('topic'))
        ->withErrors($validator);
    } else {
      $entity = \App\UiTaskResult::find($id);

      $comment = ($request->get('comment') == null) ? '-' : $request->get('comment');

      $entity->duration = $request->get('duration');
      $entity->comment = $comment;

      $entity->save();

      Session::flash('message', 'Task Result for UI topic ' . $request->get('topic') . ' is changed');

      return Redirect::to('student/uifeedback/' . $request->get('topic'));
    }
  }
}

