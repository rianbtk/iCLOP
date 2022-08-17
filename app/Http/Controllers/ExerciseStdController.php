<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class ExerciseStdController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->roleid == 'student') {
            $check = \App\User::find(Auth::user()->id);
            if ($check->status != 'active') return view('student/home')->with(['status' => $check->status]);
        }

        $stagelist = \App\ExerciseTopic::orderBy('stage', 'asc')->get();
        $topiclist = \App\ExerciseTopic::orderBy('name', 'asc')->get();

        if ($topiclist->count() > 0) {
            $itemsstage = \App\ExerciseTopic::orderBy('stage', 'asc')
                ->selectRaw('stage, min(id) as id')
                ->groupBy('stage')
                ->pluck('stage', 'id');

            // $filter = $request->input('topicList', $topiclist[0]['id']);

            $filter = $request->input('stageList', $topiclist[0]['id']);

            $option = $request->input('option', 'github');

            $stage = \App\ExerciseTopic::where('exercises.id', '=', $filter)
                ->select(
                    'exercises.id',
                    'exercises.stage'
                )
                ->get();

            $topic = \App\ExerciseTopic::where('exercises.stage', '=', $stage[0]['stage'])
                ->select(
                    'exercises.id',
                    'exercises.name',
                    'exercises.desc',
                    'exercise_files.guide',
                    'exercise_files.testfile',
                    'exercise_files.supplement',
                    'exercise_files.other'
                )
                ->leftJoin('exercise_files', 'exercise_files.exercise', '=', 'exercises.id')
                ->get();
            // $result = \App\ExerciseValidation::where('exercise_validations.userid', Auth::user()->id)
            //     ->where('exercise_submits.exercise', $filter)
            //     ->select(
            //         'exercise_submits.checkstat',
            //         'exercise_submits.checkresult'
            //     )
            //     ->leftJoin('exercise_submits', 'exercise_validations.exercisesubmitid', '=', 'exercise_submits.id')
            //     ->first();

            return view('student/exercise/index')
                ->with(compact('itemsstage'))
                ->with(compact('filter'))
                ->with(compact('topic'))
                // ->with(compact('result'))
                ->with(compact('option'));
        } else {
            return view('student/exercise/index');
        }
    }

    private function validateUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $result = parse_url($url);
            if (($result['scheme'] == 'https') && ($this->endsWith($result['host'], 'github.com'))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function validateZipFile(Request $request)
    {
        $rules = [
            'zipfile' => 'required',
            'comment' => 'required',
            'duration' => 'required',
        ];

        $msg = [
            'zipfile.required' => 'Zip file must not empty',
            'comment.required' => 'Exercise comment must not empty',
            'duration.required' => 'Duration time must not empty',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            return Redirect::to('student/exercise?topicList=' . $request->get('exercise'))
                ->withErrors($validator);
        } else {
            $userid = Auth::user()->id;
            $exercise = $request->get('exercise');
            $file = $request->file('zipfile');
            $filename = $file->getClientOriginalName();
            //
            //$file = $request->file('zipfile');
            if ($filename != '') {
                //$array = explode('.', $path);
                //$ext = strtolower(end($array));
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if ($ext == "zip") {
                    $zipFile = $file->store('exercise_results', 'public');

                    if ($zipFile != '') {
                        $entity = new \App\ExerciseSubmit();

                        $entity->userid = $userid;
                        $entity->exercise = $exercise;
                        $entity->validstat = "valid";
                        $entity->checkresult  = "waiting";
                        $entity->projectfile = $zipFile;
                        $entity->comment  = $request->get('comment');
                        $entity->duration  = $request->get('duration');

                        $entity->save();

                        $data = \App\ExerciseTopic::find($exercise);
                        Session::flash('message', $data['name'] . ' Validation by Uploading Zip Project is Success');
                    } else {
                        Session::flash('message', 'Storing file ' . $request->file('zipfile') . ' was FAILED');
                    }
                } else {
                    Session::flash('message', 'File extension is not zip -> ' . $filename . ' is wrong .' . $ext);
                }
            } else {
                Session::flash('message', 'Zip File is empty');
            }


            //return "Add new topic is success";
            return Redirect::to('student/exercise?topicList=' . $exercise);
        }
    }

    private function validateGithubLink(Request $request)
    {
        $rules = [
            'githublink' => 'required',
            'comment' => 'required',
            'duration' => 'required',
        ];

        $msg = [
            'githublink.required' => 'Github link must not empty',
            'comment.required' => 'Exercise comment must not empty',
            'duration.required' => 'Duration time must not empty',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            return Redirect::to('student/exercise?topicList=' . $request->get('exercise'))
                ->withErrors($validator);
        } else {
            $userid = Auth::user()->id;
            $exercise = $request->get('exercise');
            $link = $request->get('githublink');
            //
            $trimmedlink = trim($link);
            if ($this->validateUrl($trimmedlink)) {
                $entity = new \App\ExerciseSubmit;

                $entity->userid = $userid;
                $entity->exercise = $exercise;
                $entity->validstat = "valid";
                $entity->checkresult = "waiting";
                $entity->validator  = "";
                $entity->comment  = $request->get('comment');;
                $entity->duration  = $request->get('duration');;
                $entity->githublink = $trimmedlink;

                $entity->save();

                $data = \App\ExerciseTopic::find($exercise);
                Session::flash('message', $data['name'] . ' Validation by submitting GitHub link is Success');

                //Session::flash('message','URL valid '.$link);

            } else {
                Session::flash('message', 'URL is not VALID ' . $link);
            }

            //return "Add new topic is success";
            return Redirect::to('student/exercise?topicList=' . $exercise);
        }
    }

    private function endsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if (($request->get('action') == 'validate') && (strlen($request->submitbutton) > 5)) {
            if ($request->get('option') == 'zipfile') {
                return $this->validateZipFile($request);
            } else if ($request->get('option') == 'github') {
                return $this->validateGithubLink($request);
            } else {
                return Redirect::to('student/exercise?topicList=' . $request->get('exercise') . '&option=' . $request->get('option') .
                    '&submit=' . $request->submitbutton);
            }
        } else { //clicking radio button
            return Redirect::to('student/exercise?topicList=' . $request->get('exercise') . '&option=' . $request->get('option'));
            //'&submit='.$request->submitbutton);
        }
    }
}

