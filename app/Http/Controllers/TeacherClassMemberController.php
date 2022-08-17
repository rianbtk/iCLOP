<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Redirect;
use Session;

class TeacherClassMemberController extends Controller
{
  //
  /**
  * Display a listing of the resource.
  *
  * @return Response
  */
  public function index(Request $request)
  {
    //
    $filter = $request->input('tchList','3');

    if ($filter=='0') {
      $entities=\App\TopicFiles::all();
    } else {
      $entities = \App\User::where('users.uplink','=',$filter)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'C.name as classname'
            )
            ->join(
                'class_members as B', 'users.id','=','B.student'
            )
            ->join(
              'classrooms as C', 'B.classid','=','C.id'
            )
            ->where('users.status','=','active')
            ->orderBy('C.name','asc')
            ->orderBy('users.name','asc')
            ->get();
    }
    $items = \App\User::where('roleid','=','teacher')->pluck('name', 'id');

    //$data=['entities'=>$entities, 'items'=>$items];
    return view('admin/tmember/index')
        ->with(compact('entities'))
        ->with(compact('items'))
        ->with(compact('filter'));
  }

}
