<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
  public function saveHistory($file_path, $topic_name, $doctype, $fname) {
    $item = new \App\DownloadHistory;

    $item->userid=Auth::user()->id;
    $item->topicname=$topic_name;
    $item->doctype=$doctype;
    $item->filepath=$file_path;
    $item->downfile=$fname;
    $item->host=$_SERVER['REMOTE_ADDR'];
    $item->save();
  }

  public function downFile($file_name, $topic_name, $doctype, $folder) {
    $dirname = str_replace('\\',DIRECTORY_SEPARATOR,'app\public\\'.$folder.'\\');
    $file_path = storage_path($dirname.$file_name);
    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
    $headers = array('Content-Type' => 'application/zip','Content-Type' => 'application/pdf',
		'Content-Type' => 'application/rar','Content-Type' => 'application/txt');

    $fname=$topic_name.'-'.$doctype.'.'.$ext;
    $this->saveHistory($file_path,$topic_name,$doctype,$fname);
    return response()->download($file_path, $fname, $headers);
  }


  public function downLearning($file_name, $topic_name, $doctype) {
/*
    $dirname = str_replace('\\',DIRECTORY_SEPARATOR,'app\public\learning\\');
    $file_path = storage_path($dirname.$file_name);
    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
    $headers = array('Content-Type' => 'application/zip','Content-Type' => 'application/pdf','Content-Type' => 'application/rar');

    $fname=$topic_name.'-'.$doctype.'.'.$ext;
    $this->saveHistory($file_path,$topic_name,$doctype,$fname);
    return response()->download($file_path, $fname, $headers);
*/
	return $this->downFile($file_name, $topic_name, $doctype,'learning');
  }

  public function downGuide($file_name, $topic_name) {
    return $this->downLearning($file_name,$topic_name,'GuideDocuments');
  }

  public function downTest($file_name, $topic_name) {
    return $this->downLearning($file_name,$topic_name,'TestFiles');
  }

  public function downSupplement($file_name, $topic_name) {
    return $this->downLearning($file_name,$topic_name,'SupplementFiles');
  }

  public function downOther($file_name, $topic_name) {
    return $this->downLearning($file_name,$topic_name,'OtherFiles');
  }

// Exercise download
  public function downExercise($file_name, $topic_name, $doctype)
  {
    return $this->downFile($file_name, $topic_name, $doctype, 'exercise_files');
  }

  public function downExerciseGuide($file_name, $topic_name)
  {
    return $this->downExercise($file_name, $topic_name, 'GuideDocuments');
  }

  public function downExerciseTest($file_name, $topic_name)
  {
    return $this->downExercise($file_name, $topic_name, 'TestFiles');
  }

  public function downExerciseSupplement($file_name, $topic_name)
  {
    return $this->downExercise($file_name, $topic_name, 'SupplementFiles');
  }

  public function downExerciseOther($file_name, $topic_name)
  {
    return $this->downExercise($file_name, $topic_name, 'OtherFiles');
  }

  // Jplas
  public function downJplasPackage($file_name, $topic_name) {
    return $this->downLearning($file_name,$topic_name,'JplasPackage');
  }

  public function downJplasGuide($file_name, $topic_name) {
    return $this->downLearning($file_name,$topic_name,'JplasGuide');
  }

  public function downJplasResult($file_name, $topic_name) {
    return $this->downFile($file_name,$topic_name,'JplasResult','results');
  }


  /*
  public function download(Request $request){
        //PDF file is stored under project/public/download/info.pdf
        $file="./".$request->get('fileid');
        return Response::download($file);
    }
    */
    /*
    public function download($file_name) {
      $file_path = public_path('files/'.$file_name);
      return response()->download($file_path);
    }
    */
}
