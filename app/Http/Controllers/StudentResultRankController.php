<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentResultRankController extends Controller
{
    //

public function index()
   { 
        //
        $entities=\App\StudentRank::orderBy('score','desc')
		->orderBy('last_submit','asc')
		->offset(0)->limit(20)->get();
        $data=['entities'=>$entities];
	if (Auth::user()->roleid == "admin") { 
        	return view('admin/rankview/index')->with($data);
	} else if (Auth::user()->roleid == "teacher") {
                return view('teacher/rankview/index')->with($data);
        } else {
		return view('student/rankview/index')->with($data);
	}
    }
}
