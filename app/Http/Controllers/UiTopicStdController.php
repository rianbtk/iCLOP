<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
//use Illuminate\Http\Request;

use Redirect;
use Session;

class UiTopicStdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //
//        $entities = \App\UiTopics::where('status', '=', '1')
//	     ->get();

	$subQuery = DB::table('ui_student_submits')
            ->where('userid', '=', Auth::user()->id)
            ->where('checkstat', '=', 'PASSED');

        $entities = DB::table('ui_topics')
            ->leftJoinSub($subQuery, 'B', function ($join) {
                $join->on('ui_topics.id', '=', 'B.uitopic');
            })
            ->where('ui_topics.status', '=', '1')
            ->select('ui_topics.id', 'name', 'description', 'checkstat')
	    ->distinct()
	    ->orderBy('ui_topics.id', 'asc') 
            ->get();

        $data = ['entities' => $entities];

        return view('student/uitasks/index')
            ->with(compact('entities'));
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
        // SUBMIT TASK PADA student/uitasks/(id)
        $rules = [
            'MainActivity' => 'required'
        ];

        $msg = [
            'MainActivity.required' => 'MainActivity.xml must not empty'
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        //jika data ada yang kosong
        if ($validator->fails()) {

            //refresh halaman
            return Redirect::to('student/uitasks/' . $request->get('id'))
                ->withErrors($validator);
        } else {
            $codebox = array();
            $codebox[0] = $request->get('MainActivity');
            $codebox[1] = $request->get('Color');
            $codebox[2] = $request->get('String');

            //save UiStudentSubmits data to variable
            $this->insertUiStudentSubmits($request->get('id'));
            //insert data to UiStudentValidations
           // $this->insertUiStudentValidations($request, $UiStudentSubmitID);
            //insert data to UiFileResults
            $this->insertUiFileResults($codebox, $request);

            //jika berhasil lempar pesan ini
            Session::flash('message', 'Task were successfully submitted');

            //dialihkan ke .../student/uitasks/(id)
            return Redirect::to('student/uitasks/' . $request->get('id'));
        }
    }

    public function insertUiStudentSubmits($topicid)
    {
        # code...
        $entity = new \App\UiStudentSubmits;
        $maxId = DB::table('ui_student_submits')->max('id');

        $entity->id = $maxId + 1;
        $entity->userid = Auth::user()->id;
        $entity->uitopic = $topicid;
        $entity->checkresult = "-";
        $entity->save();

	return $maxId + 1;
    }

    public function insertUiStudentValidations($request, $UIStudentSubmit)
    {
        # code...
        $testFiles = \App\UiTestFiles::where('uitopicid', '=', $request->get('id'))
            ->orderBy('id')
            ->get();

        foreach ($testFiles as $ts) {
            $entity = new \App\UiStudentValidations();

            $maxId = DB::table('ui_student_validations')->max('id');

            $entity->id = $maxId + 1;
            $entity->userid = Auth::user()->id;
            $entity->testid = $ts['id'];
            $entity->submitid = $UIStudentSubmit;
            $entity->report = "-";
            $entity->save();
        }
    }

    public function insertUiFileResults($codebox, $request)
    {
        $index = 0;

        foreach ($codebox as $textareas) {
            # code...
            $entity = new \App\UiFileResults;

            // filter data for uisubmitid column (based userid & uitopic)
            $uisubmitid_filter = \App\UiStudentSubmits::where('userid', '=', Auth::user()->id)
                ->where('uitopic', '=', $request->get('id'))
                ->orderBy('id', 'desc')
                ->take(1)
                ->get();
	    $maxId = DB::table('ui_file_results')->max('id');
            $uisubmitid_id = $uisubmitid_filter[0]['id'];
            $uitopicfilesid = \App\UiTopicFiles::where('uitopicid', '=', $request->get('id'))
                ->get();

           // create file with random name (using uuid()) for codefile column
            $filename = Str::uuid();
            File::put(storage_path('app/public/uiresource/' . $filename . '.txt'), $textareas);
            $file_path = ('uiresource/' . $filename . '.txt');
            
	    //Insert data to table
            $entity->id = $maxId + 1;
            $entity->userid = Auth::user()->id;
            $entity->uisubmitid = $uisubmitid_id;
            $entity->uicodeid = $uitopicfilesid[$index]['codeno'];;
            $entity->codefile = $file_path;
            $entity->save();
            $index++;
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
	//cek status topic yang dibuka
        $entity = \App\UiTopics::where('id', '=', $id)
            ->select('status')
            ->get();
        if (!((empty($entity[0]) ? 0 : $entity[0]['status']) == 1)) {
            Session::flash('alert', 'Topic name with id=' . $id . ' is not allowed to open!');
            return Redirect::to('student/uitasks/');
        }
        
	$entity = \App\UiTopics::find($id);
        $idUser = Auth::user()->id;
        $numberOfTries = \App\UiStudentSubmits::where('userid', '=', $idUser)
            ->where('uitopic', '=', $id)
            ->count('userid');

        //Data untuk Result
	$submitDataStatus = \App\UiStudentResultView::where('ui_student_submits_view.userid', '=', $idUser)
            ->select(
                'ui_student_submits_view.id',
                'ui_student_submits_view.uitopicid',
                'ui_student_submits_view.topic',
                'ui_student_submits_view.checkstat',
                'ui_student_submits_view.report'
            )
            ->where('ui_student_submits_view.uitopicid', '=', $id)
            ->orderBy('ui_student_submits_view.created_at', 'desc')
            ->get();

        // ambil data student submit untuk Button Submits
        $studentSubmit = \App\UiStudentSubmits::where('userid', '=', $idUser)
            ->where('uitopic', '=', $id)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

	//ambil data untuk tombol next
        $nextTopic = \App\UiTopics::where('id', '>', $id)
            ->select('id')
            ->where('status', '=', '1')
            ->orderBy('id', 'ASC')
            ->take(1)
            ->get();

	//ambil data untuk tombol previous
        $previousTopic = \App\UiTopics::where('id', '<', $id)
            ->select('id')
            ->where('status', '=', '1')
            ->orderBy('id', 'DESC')
            ->take(1)
            ->get();

	$x = ['data' => $entity, 'nextid' => (empty($nextTopic[0]) ? 0 : $nextTopic[0]['id']), 'previousid' => (empty($previousTopic[0]) ? 0 : $previousTopic[0]['id']), 'numberOfTries' => $numberOfTries, 'entities' => $submitDataStatus, 'stdSubmit' => $studentSubmit];

        return view('student/uitasks/show')->with($x);
    }

    public static function getDataSourceFiles($submitid)
    {
        $idUser = Auth::user()->id;
        $entities = \App\UiFileResults::where('ui_file_results.userid', '=', $idUser)
            ->select('ui_file_results.codefile')
            ->where('ui_file_results.uisubmitid', $submitid)
            ->orderBy('ui_file_results.id', 'asc')
            ->get();

        $data = ['fileSources' => $entities];

        return $data;
    }
}

