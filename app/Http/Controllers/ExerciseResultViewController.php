<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExerciseResultViewController extends Controller
{
    //

    public function index()
    {
        //
        $entities = \App\ExerciseStudentResultView::orderBy('id', 'desc')->get();
        $data = ['entities' => $entities];
        return view('admin/exerciseresview/index')->with($data);
    }

    public function showhistory($student, $topicid)
    {
        $entities = \App\UiStudentResultView::where('userid', $student)
            ->where('uitopicid', $topicid)
            ->orderBy('id', 'DESC')->get();

        $topic = \App\UiTopics::find($topicid);
        $user = \App\User::find($student);
        $data = ['entities' => $entities, 'topic' => $topic, 'student' => $user];
        return view('admin/uiresview/show')->with($data);
    }
}

