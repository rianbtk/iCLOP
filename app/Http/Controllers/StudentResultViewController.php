<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentResultViewController extends Controller
{
    //

public function index()
   { 
        //
        $entities=\App\StudentResultView::orderBy('id','desc')->get();
        $data=['entities'=>$entities];
        return view('admin/resview/index')->with($data);
    }
}
