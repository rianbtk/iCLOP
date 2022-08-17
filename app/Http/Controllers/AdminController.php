<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index() {

	$entities=\App\AplasSummary::all();

        $data=['entities'=>$entities];

        return view('admin/main')->with($data);
      //return view('admin/main');
    }
}
