<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Redirect;
use Session;

class ExerciseSubmissionController extends Controller
{
  public function index(Request $request)
  {
    if (Auth::user()->roleid == 'student') {
      $check = \App\User::find(Auth::user()->id);
      if ($check->status != 'active') return view('student/home')->with(['status' => $check->status]);
    }
    $stagelist = \App\ExerciseTopic::orderBy('stage', 'asc')->get();
    $exerciselist = \App\ExerciseTopic::orderBy('name', 'asc')->get();

    if ($exerciselist->count() > 0) {

      // daftar stage untuk dropdown menu
      $itemsstage = \App\ExerciseTopic::orderBy('stage', 'asc')
        ->selectRaw('stage, min(id) as id')
        ->groupBy('stage')
        ->pluck('stage', 'id');

      // id stageList dari url
      $filter = $request->input('stageList', $stagelist[0]['id']);

      // id exerciseList dari url
      $filterexercise = $request->input('exerciseList', $exerciselist[0]['id']);

      // get nama stage dari $filter
      $stagename = \App\ExerciseTopic::orderBy('stage', 'asc')
        ->where('id', $filter)
        ->pluck('stage');

      // daftar exercise untuk dropdown menu
      $itemsexercise = \App\ExerciseTopic::orderBy('id', 'asc')
        ->where('stage', $stagename)
        ->pluck('name', 'id');

      if (!($this->checkstage($filter, $filterexercise))) {
        return Redirect::to('student/exercisesubmission?stageList=' . $filter . '&exerciseList=' . $filter);
      }

      $completed = \App\ExerciseSubmit::where('userid', '=', Auth::user()->id)
        ->where('exercise', '=', $filterexercise)
        ->get()->count();

      $option = $request->input('option', 'github');

      $stage = \App\ExerciseTopic::where('exercises.id', '=', $filter)
        ->select(
          'exercises.id',
          'exercises.stage'
        )
        ->get();

      $topic = \App\ExerciseTopic::where('exercises.stage', '=', $stage[0]['stage'])
        ->select(
          'exercises.name'
        )
        ->get();

      $result = \App\ExerciseValidation::where('exercise_validations.userid', Auth::user()->id)
        ->where('exercise_submits.exercise', $filter)
        ->select(
          'exercise_submits.checkstat',
          'exercise_submits.checkresult'
        )
        ->leftJoin('exercise_submits', 'exercise_validations.exercisesubmitid', '=', 'exercise_submits.id')
        ->first();

      return view('student/exercisesubmission/index')
        ->with(compact('itemsstage'))
        ->with(compact('itemsexercise'))
        ->with(compact('filter'))
        ->with(compact('filterexercise'))
        ->with(compact('topic'))
        ->with(compact('completed'))
        ->with(compact('result'))
        ->with(compact('option'));
    } else {
      return view('student/exercisesubmission/index');
    }
  }

  private function checkstage($stage, $exercise)
  {
    $stagename = \App\ExerciseTopic::orderBy('stage', 'asc')
      ->where('id', $stage)
      ->pluck('stage');
    $exercisestage = \App\ExerciseTopic::orderBy('stage', 'asc')
      ->where('id', $exercise)
      ->pluck('stage');

    if ($stagename == $exercisestage) {
      return true;
    } else {
      return false;
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
      return Redirect::to('student/exercisesubmission?stageList=' . $request->get('stage') . '&exerciseList=' . $request->get('exercise'))
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
      return Redirect::to('student/exercisesubmission?stageList=' . $request->get('stage') . '&exerciseList=' . $request->get('exercise'));
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
      return Redirect::to('student/exercisesubmission?stageList=' . $request->get('stage') . '&exerciseList=' . $request->get('exercise'))
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
      return Redirect::to('student/exercisesubmission?stageList=' . $request->get('stage') . '&exerciseList=' . $request->get('exercise'));
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
        return Redirect::to('student/exercisesubmission');
      }
    } else { //clicking radio button
      return Redirect::to('student/exercisesubmission?stageList=' . $request->get('stage') . '&exerciseList=' . $request->get('exercise') . '&option=' . $request->get('option'));
      //'&submit='.$request->submitbutton);
    }
  }
}

