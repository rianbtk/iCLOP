<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
  //     if (Auth::user()->roleid == 'student') {
  //       //$check=\App\StudentTeacher::where('student','=',Auth::user()->id);
  //       //return view('/student/main')->with(['count'=>$check->count()]);
	// $check=\App\User::find(Auth::user()->id);
  //       return view('/student/main')->with(['status'=>$check->status]);
  //     } else if (Auth::user()->roleid == 'admin') {
  //       return view('/admin/admin');
  //     } else {
  //       return view('/teacher/home');
  //     }

      if (Auth::user()->roleid == 'student') {
        $check=\App\StudentTeacher::where('student','=',Auth::user()->id);
        return view('/student/landingpage')->with(['count'=>$check->count()]);
      } else if (Auth::user()->roleid == 'admin') {
        return view('/admin/admin');
      } else {
        return view('/teacher/home');
      }

    }
}
