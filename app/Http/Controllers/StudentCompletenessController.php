<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCompletenessController extends Controller
{
    //

public function index()
   { 
        //
	if (Auth::user()->roleid == "admin") { 
		$entities=\App\StudentCompleteness::where('student_completeness.userid','>',5 )
                ->orderBy('student_completeness.total','desc')
		->orderBy('student_completeness.teachername','asc')
		->orderBy('student_completeness.name','asc')
                ->get();
        	$data=['entities'=>$entities];

        	return view('admin/completeness/index')->with($data);
	} else if (Auth::user()->roleid == "teacher") {
		$entities=\App\StudentCompleteness::where('teacherid','=',Auth::user()->id )
		->orderBy('student_completeness.total','desc')
                ->orderBy('student_completeness.teachername','asc')
                ->orderBy('student_completeness.name','asc')
                ->get();

        	$data=['entities'=>$entities];

                return view('teacher/completeness/index')->with($data);
        } else {
		return view('student/rankview/index')->with($data);
	}
    }
}
