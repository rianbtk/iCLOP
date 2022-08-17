<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UiResultViewController extends Controller
{
    //

    public function index()
    {
        //
        if (Auth::user()->roleid == 'admin') {
            $entities = \App\UiStudentResultView::orderBy('id', 'desc')->get();
            $data = ['entities' => $entities];
            return view('admin/uiresview/index')->with($data);
        } else if (Auth::user()->roleid == 'teacher') {
            $entities = \App\UiStudentResultView::orderBy('id', 'desc')
                ->where('teacher', Auth::user()->name)
                ->get();
            $data = ['entities' => $entities];
            return view('teacher/uiresview/index')->with($data);
        }
    }

    public function showhistory($student, $topicid)
    {
        $entities = \App\UiStudentResultView::where('userid', $student)
            ->where('uitopicid', $topicid)
            ->orderBy('id', 'DESC')->get();

        $topic = \App\UiTopics::find($topicid);
        $user = \App\User::find($student);
        $data = ['entities' => $entities, 'topic' => $topic, 'student' => $user];

        if (Auth::user()->roleid == 'admin') {
            return view('admin/uiresview/show')->with($data);
        } else if (Auth::user()->roleid == 'teacher') {
            return view('teacher/uiresview/show')->with($data);
        }
    }
}

