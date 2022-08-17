<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


//use Illuminate\Http\Request;

use Redirect;
use Session;

class UiTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $entities = \App\UiTopics::orderBy('level', 'asc')->get();

        $data = ['entities' => $entities];

        return view('admin/uitopic/index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin/uitopic/create');
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
            'guideFile' => 'required'
        ];

        $msg = [
            'name.required' => 'Topic name must not empty',
            'guideFile.required' => 'Guide file must not empty'
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        //jika data ada yang kosong
        if ($validator->fails()) {

            //refresh halaman
            return Redirect::to('admin/uitopic/create')
                ->withErrors($validator);
        } else {
            //mengisi UI_TOPICS
            $file = $request->file('guideFile');
            $filePath = $file->store('uilearningfile', 'public');

            $entity = new \App\UiTopics;
            $entity->id = \App\UiTopics::max('id') + 1;
            $entity->level = $request->get('level');
            $entity->name = $request->get('name');
            $entity->note = $request->get('note');
            $entity->projectname = $request->get('projectname');
            $entity->projectpath = $request->get('projectpath');
            $entity->packname = $request->get('packname');
            $entity->description = $request->get('description');
            $entity->guidepath = $filePath;
            $entity->status = $request->get('status');

            $entity->save();

            $xmlName = ['activity_main.xml', 'colors.xml', 'strings.xml'];
            $pathfile = ['app/src/main/res/layout/', 'app/src/main/res/values/', 'app/src/main/res/values/'];

            //mengisi UI_TOPIC_FILES
            for ($i = 0; $i < 3; $i++) {
                $entity = new \App\UiTopicFiles;
		$nextid = \App\UiTopicFiles::max('id') + 1;
                
		$entity->id = $nextid;
                $entity->uitopicid = \App\UiTopics::max('id');
                $entity->filename = $xmlName[$i];
                $entity->location = $pathfile[$i];
                $entity->codeno = $nextid;

                $entity->save();
            }

            Session::flash('message', 'A New Topic Stored');

            //return "Add new topic is success";
            return Redirect::to('admin/uitopic');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $entity = \App\UiTopics::find($id);

        $x = ['uitopic' => $entity];

        return view('admin/uitopic/show')->with($x);
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
        $entity = \App\UiTopics::find($id);
        $x = ['uitopic' => $entity];
        return view('admin/uitopic/edit')->with($x);
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
            'name' => 'required'
        ];

        $msg = [
            'name.required' => 'Topic name must not empty'
        ];


        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            return Redirect::to('admin/uitopic/' . $id . '/edit')
                ->withErrors($validator);
        } else {
            $entity = \App\UiTopics::find($id);
            $file = $request->file('guideFile');

            $entity->level = $request->get('level');
            $entity->name = $request->get('name');
	    $entity->note = $request->get('note');
            $entity->projectname = $request->get('projectname');
            $entity->projectpath = $request->get('projectpath');
            $entity->packname = $request->get('packname');
            $entity->description = $request->get('description');
	    if (!is_null($file)) {
            	$filePath = $file->store('uilearningfile', 'public');
		$entity->guidepath = $filePath;
            }
	    $entity->status = $request->get('status');
            $entity->save();

            Session::flash('message', 'UiTopic with Id=' . $id . ' is changed');
            return Redirect::to('admin/uitopic');
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
        $entity = \App\UiTopics::find($id);
        $uitopicfiles = \App\UiTopicFiles::where('uitopicid', '=', $id);
        $entity->delete();
        $uitopicfiles->delete();
        Session::flash('message', 'Topic with Id=' . $id . ' and Ui Codes with UiTopicId=' . $id . ' is deleted');
        return Redirect::to('admin/uitopic');
    }
}

