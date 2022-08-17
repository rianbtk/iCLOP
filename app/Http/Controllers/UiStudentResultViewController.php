<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UiStudentResultViewController extends Controller
{
    //

    public function index()
    {
        //
	$subsubQuery = DB::table('ui_student_submits')
            ->select(DB::raw('uitopic AS uitopicnow,max(checkstat) AS checkstatnow, max(created_at) AS createdate'))
            ->where('userid', '=', Auth::user()->id)
            ->groupBy('uitopicnow');

        $entitiesresult = DB::table('ui_topics')
            ->leftJoinSub($subsubQuery, 'B', function ($join) {
                $join->on('ui_topics.id', '=', 'B.uitopicnow');
            })
            ->where('ui_topics.status', '=', '1')
            ->select(DB::raw('ui_topics.id, name, description, LOWER(checkstatnow) AS checkstatnow'))
            ->distinct()
            ->orderBy('ui_topics.id', 'asc')
            ->get();

        $entities = \App\UiStudentResultView::where('userid', Auth::user()->id)
            ->orderBy('id', 'desc')->get();

        $data = ['entities' => $entities];

        $topiccount = \App\UiTopics::count('id');

        return view('student/uiresview/index')
    	    ->with(compact('entities'))
            ->with(compact('topiccount'))
            ->with(compact('entitiesresult'));
	}
}

