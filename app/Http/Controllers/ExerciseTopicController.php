<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
//use Illuminate\Http\Request;

use Redirect;
use Session;

class ExerciseTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $entities = \App\ExerciseTopic::orderBy('stage', 'asc')->orderBy('name', 'asc')->get();
        //::all();

        $data = ['entities' => $entities];

        return view('admin/exercise/index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin/exercise/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'name' => 'required',
            'stage' => 'required'
        ];

        $msg = [
            'name.required' => 'Exercise name must not empty',
            'stage.required' => 'Learning stage must not empty'
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        //jika data ada yang kosong
        if ($validator->fails()) {

            //refresh halaman
            return Redirect::to('admin/exerciseconf/create')
                ->withErrors($validator);
        } else {

            $file = $request->file('testcode');
            $testFile = $file->store('exercise_test_files', 'public');

            $entity = new \App\ExerciseTopic();

            $entity->name = $request->get('name');
            $entity->stage = $request->get('stage');
            $entity->desc = $request->get('desc');
            $entity->projectpath = $request->get('projectpath');
            $entity->packname = $request->get('packname');
            $entity->androidclass = $request->get('androidclass');
            $entity->testdir = $request->get('testpath');
            $entity->singletestcode = $testFile;

            $entity->save();

            Session::flash('message', 'A New Exercise Stored');

            //return "Add new topic is success";
            return Redirect::to('admin/exerciseconf');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $entity = \App\ExerciseTopic::find($id);
        $x = ['topic' => $entity];
        return view('admin/exercise/show')->with($x);
        //return view('topics.show', array('topic' => $topic));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $entity = \App\ExerciseTopic::find($id);
        $x = ['topic' => $entity];
        return view('admin/exercise/edit')->with($x);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        $rules = [
            'name' => 'required',
            'stage' => 'required'
        ];

        $msg = [
            'name.required' => 'Topic name must not empty',
            'stage.required' => 'Learning stage must not empty'
        ];


        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            return Redirect::to('admin/exerciseconf/' . $id . '/edit')
                ->withErrors($validator);
        } else {
            $file = $request->file('testcode');
            $originalname = $file->getClientOriginalName();
            $testFile = $file->storeAs('exercise_test_files', $originalname, 'public');
	    $entity = \App\ExerciseTopic::find($id);

            $entity->name = $request->get('name');
            $entity->stage = $request->get('stage');
            $entity->desc = $request->get('desc');
            $entity->projectpath = $request->get('projectpath');
            $entity->packname = $request->get('packname');
            $entity->androidclass = $request->get('androidclass');
            $entity->testdir = $request->get('testpath');
            $entity->singletestcode = $originalname;
            $entity->save();

            Session::flash('message', 'Exercise with Id=' . $id . ' is changed');
            return Redirect::to('admin/exerciseconf');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        $entity = \App\ExerciseTopic::find($id);
        $entity->delete();
        Session::flash('message', 'Exercise with Id=' . $id . ' is deleted');
        return Redirect::to('admin/exerciseconf');
    }
}

