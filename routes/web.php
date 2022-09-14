<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Python
use App\Http\Controllers\python\ExercisePythonController;
use App\Http\Controllers\python\PythonLearningTopicsController;
use App\Http\Controllers\python\PythonPercobaanController;
use App\Http\Controllers\python\ResultController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
  return view('welcome');
});

Route::group(['middleware' => ['auth', 'admin']], function () {
  Route::get('/admin', 'AdminController@index');
  Route::resource('/admin/topics', 'TopicController');
  Route::resource('/admin/admintasks', 'TaskController');
  Route::resource('/admin/learning', 'LearningFileController');
  Route::resource('/admin/resources', 'ResourcesController');
  Route::resource('/admin/testfiles', 'TestFilesController');
  Route::get('/admin/testfiles/create/{topic}', 'TestFilesController@create');
  Route::resource('/admin/assignteacher', 'AssignTeacherController');
  Route::resource('/admin/assignteacher/index', 'AssignTeacherController@index');
  Route::resource('/admin/tmember', 'TeacherClassMemberController');
  Route::resource('/admin/studentres', 'StudentValidController');
  Route::get('/admin/studentres/{student}/{id}', 'StudentValidController@showteacher');
  Route::get('/admin/uploadsrc/{student}/{id}', 'StudentValidController@showsource');

  Route::resource('/admin/resview', 'StudentResultViewController');
  Route::resource('/admin/rankview', 'StudentResultRankController');
  Route::resource('/admin/completeness', 'StudentCompletenessController');

  Route::get('/admin/uistudentdetail/{student}/{id}', 'UiDetailController@showadmin');
  Route::get('/admin/uiuploadsrc/{student}/{topicid}/{id}', 'UiDetailController@showsource');
  Route::get('/admin/uiresview/{student}/{topicid}', 'UiResultViewController@showhistory');

  Route::resource('/admin/uitopic', 'UiTopicController');
  Route::resource('/admin/uitestfiles', 'UiTestFilesController');
  Route::resource('/admin/uiresview', 'UiResultViewController');
  Route::resource('/admin/uisummaryres', 'UiResultController');
  Route::resource('/admin/exerciseconf', 'ExerciseTopicController');
  Route::resource('/admin/exercisefiles', 'ExerciseFilesController');
  Route::resource('/admin/exerciseresources', 'ExerciseResourcesController');
  Route::resource('/admin/exerciseresview', 'ExerciseResultViewController');
  Route::get('/admin/exercisestudentres/{student}/{id}', 'ExerciseValidController@showadmin');
  Route::get('/admin/exercisestudentres/{student}/{topicid}/{id}', 'ExerciseValidController@showsource');
  Route::resource('/admin/resetpassword', 'ResetPasswordController');

  // Python Topik Tampilan
  Route::get('/admin/python/topic', [PythonLearningTopicsController::class, 'index']);
  Route::get('/admin/python/tambahtopik', [PythonLearningTopicsController::class, 'tambah']);
  Route::get('/admin/python/edittopik/{id_topik}', [PythonLearningTopicsController::class, 'edit']);

  // Python Percobaan Tampilan
  Route::get('/admin/python/percobaan', [PythonPercobaanController::class, 'index']);
  Route::get('/admin/python/tambahpercobaan', [PythonPercobaanController::class, 'tambah']);
  Route::get('/admin/python/editpercobaan/{id_percobaan}', [PythonPercobaanController::class, 'edit']);

  // proses tambah + update
  Route::post('/admin/python/prosestambahtopik', [PythonLearningTopicsController::class, 'proses_tambah']);
  Route::post('/admin/python/prosesedittopik/{id_topik}', [PythonLearningTopicsController::class, 'proses_edit']);
  Route::post('/admin/python/prosestambahpercobaan', [PythonPercobaanController::class, 'proses_tambah']);
  Route::post('/admin/python/proseseditpercobaan/{id_percobaan}', [PythonPercobaanController::class, 'proses_edit']);

  // proses hapus
  Route::get('/admin/python/proseshapustopik/{id_topik}', [PythonLearningTopicsController::class, 'proses_hapus']);
  Route::get('/admin/python/proseshapuspercobaan/{id_percobaan}', [PythonPercobaanController::class, 'proses_hapus']);

  /* ----------------------------------- SQL ---------------------------------- */
  Route::group(['prefix' => 'admin/sql'], function () {
    Route::group(['prefix' => 'pembelajaran'], function () {
      Route::get('', 'SQLController@learning')->name('admin sql learning');
      Route::get('/read', 'SQLController@learningRead')->name('admin sql learning read');
      Route::get('/detail/{id}', 'SQLController@learningRead')->name('admin sql learning detail');
      Route::post('/create', 'SQLController@learningStore')->name('admin sql learning create');
      Route::post('/update/{id}', 'SQLController@learningUpdate')->name('admin sql learning update');
      Route::get('/delete/{id}', 'SQLController@learningDelete')->name('admin sql learning delete');

      Route::group(['prefix' => 'log'], function () {
        Route::get('/read', 'SQLController@learningLogRead')->name('admin sql learning log read');
        Route::get('/detail/{id}', 'SQLController@learningLogRead')->name('admin sql learning log detail');
      });
    });

    Route::group(['prefix' => 'praktek'], function () {
      Route::get('', 'SQLController@practice')->name('admin sql practice');
      Route::get('/read', 'SQLController@practiceRead')->name('admin sql practice read');
      Route::get('/detail/{id}', 'SQLController@practiceRead')->name('admin sql practice detail');
      Route::post('/create', 'SQLController@practiceStore')->name('admin sql practice create');
      Route::post('/update/{id}', 'SQLController@practiceUpdate')->name('admin sql practice update');
      Route::get('/delete/{id}', 'SQLController@practiceDelete')->name('admin sql practice delete');

      Route::get('/log/read', 'SQLController@practiceLogRead')->name('admin sql practice log read');
    });

    Route::group(['prefix' => 'latihan'], function () {
      Route::get('', 'SQLController@exercise')->name('admin sql exercise');
      Route::get('/read', 'SQLController@exerciseRead')->name('admin sql exercise read');
      Route::get('/detail/{id}', 'SQLController@exerciseRead')->name('admin sql exercise detail');
      Route::post('/create', 'SQLController@exerciseStore')->name('admin sql exercise create');
      Route::post('/update/{id}', 'SQLController@exerciseUpdate')->name('admin sql exercise update');
      Route::get('/delete/{id}', 'SQLController@exerciseDelete')->name('admin sql exercise delete');

      Route::get('/log/read', 'SQLController@exerciseLogRead')->name('admin sql exercise log read');
    });

    Route::group(['prefix' => 'ujian'], function () {
      Route::get('', 'SQLController@exam')->name('admin sql exam');
      Route::get('/read', 'SQLController@examRead')->name('admin sql exam read');
      Route::get('/detail/{id}', 'SQLController@examRead')->name('admin sql exam detail');
      Route::post('/create', 'SQLController@examStore')->name('admin sql exam create');
      Route::post('/update/{id}', 'SQLController@examUpdate')->name('admin sql exam update');
      Route::get('/delete/{id}', 'SQLController@examDelete')->name('admin sql exam delete');

      Route::get('/log/read', 'SQLController@examLogRead')->name('admin sql exam log read');
    });
  });
  /* ----------------------------------- SQL ---------------------------------- */
});

Route::group(['middleware' => ['auth', 'teacher']], function () {
  Route::get('/teacher', 'TeacherController@index');
  Route::resource('/teacher/assignstudent', 'AssignStudentController');
  Route::resource('/teacher/member', 'StudentMemberController');
  Route::resource('/teacher/studentclasssummary', 'StudentResultClassController');
  Route::resource('/teacher/studentpassedresult', 'StudentPassedResultClassController');
  Route::resource('/teacher/studentres', 'StudentValidController');
  Route::resource('/teacher/crooms', 'ClassroomController');
  Route::get('/teacher/studentres/{student}/{id}', 'StudentValidController@showteacher');
  Route::get('/teacher/uploadsrc/{student}/{id}', 'StudentValidController@showsource');
  Route::resource('/teacher/rankview', 'StudentResultRankController');
  Route::resource('/teacher/jplasdown', 'JplasDownloadController');

  // Python
  //tampilan result mahasiswa dari dosen
  Route::get('teacher/python/resultstudent', [ResultController::class, 'student_submit']);
  Route::get('teacher/python/resultstudentdetail/{id_topik}/{id_percobaan}', [ResultController::class, 'detail']);


  // UI (BARU)
  Route::resource('/teacher/uiclasssummary', 'UiResultClassController');
  Route::resource('/teacher/uiresview', 'UiResultViewController');
  Route::get('/teacher/uiresview/{student}/{topicid}', 'UiResultViewController@showhistory');
  Route::resource('/teacher/uisummaryres', 'UiResultController');
  Route::get('/teacher/uistudentres/{student}/{id}', 'UiValidController@showadmin');
  Route::get('/teacher/uiuploadsrc/{student}/{topicid}/{id}', 'UiValidController@showsource');

  Route::resource('/teacher/completeness', 'StudentCompletenessController');
});

Route::group(['middleware' => ['auth', 'student']], function () {
  //Android//
  Route::patch('/student/androidcourse/results/valsub', ['as' => 'results.valsub', 'uses' => 'AndroidTaskResultController@valsub']);
  Route::get('student/androidcourse/results/create/{topic}', 'AndroidTaskResultController@create');
  Route::get('/student/androidcourse', 'StudentController@androidcourse');
  Route::get('/student/androidcourse/topic', 'StudentController@androidcoursetopic');
  Route::resource('/student/androidcourse/tasks', 'AndroidController');
  Route::resource('/student/androidcourse/results', 'AndroidResultController');
  Route::resource('/student/androidcourse/lfiles', 'AndroidFileResultController');
  Route::get('student/lfiles/androidcourse/create/{topic}', 'AndroidFileResultController@create');
  Route::get('student/lfiles/androidcourse/valid/{topic}', 'AndroidFileResultController@submit');
  Route::get('student/lfiles/androidcourse/delete/{id}/{topic}', 'AndroidFileResultController@delete');
  // Flutter //
  Route::patch('/student/fluttercourse/results/valsub', ['as' => 'results.valsub', 'uses' => 'FlutterTaskResultController@valsub']);
  Route::get('/student/fluttercourse/results/create/{topic}', 'FlutterTaskResultController@create');
  Route::get('/student/fluttercourse', 'StudentController@fluttercourse');
  Route::get('/student/fluttercourse/topic', 'StudentController@fluttercoursetopic');
  Route::resource('/student/fluttercourse/tasks', 'FlutterController');
  Route::resource('/student/fluttercourse/results', 'FlutterResultController');
  Route::resource('/student/fluttercourse/lfiles', 'FlutterFileResultController');
  Route::get('/student/lfiles/fluttercourse/create/{topic}', 'FlutterFileResultController@create');
  Route::get('/student/lfiles/fluttercourse/valid/{topic}', 'FlutterFileResultController@submit');
  Route::get('/student/lfiles/fluttercourse/delete/{id}/{topic}', 'FlutterFileResultController@delete');
  Route::resource('/student/flutterexercise', 'FlutterExerciseStdController');
  Route::resource('/student/flutterexercisesubmission', 'FlutterExerciseSubmissionController');
  Route::resource('/student/flutterexercisevalid', 'FlutterExerciseStdValidController');
  //NodeJs//
  Route::patch('/student/nodejscourse/results/valsub', ['as' => 'results.valsub', 'uses' => 'NodejsTaskResultController@valsub']);
  Route::get('/student/nodejscourse/results/create/{topic}', 'NodejsTaskResultController@create');
  Route::get('/student/nodejscourse', 'StudentController@Nodejscourse');
  Route::get('/student/nodejscourse/topic', 'StudentController@Nodejscoursetopic');
  Route::resource('/student/nodejscourse/tasks', 'NodejsController');
  Route::resource('/student/nodejscourse/results', 'NodejsResultController');
  Route::resource('/student/nodejscourse/lfiles', 'NodejsFileResultController');
  Route::get('/student/lfiles/nodejscourse/create/{topic}', 'NodejsFileResultController@create');
  Route::get('/student/lfiles/nodejscourse/valid/{topic}', 'NodejsFileResultController@submit');
  Route::get('/student/lfiles/nodejscourse/delete/{id}/{topic}', 'NodejsFileResultController@delete');
  //Unity//
  Route::patch('/student/unitycourse/results/valsub', ['as' => 'results.valsub', 'uses' => 'UnityTaskResultController@valsub']);
  Route::get('/student/unitycourse/results/create/{topic}', 'UnityTaskResultController@create');
  Route::get('/student/unitycourse', 'StudentController@unitycourse');
  Route::get('/student/unitycourse/topic', 'StudentController@unitycoursetopic');
  Route::resource('/student/unitycourse/tasks', 'UnityController');
  Route::resource('/student/unitycourse/results', 'UnityResultController');
  Route::resource('/student/unitycourse/lfiles', 'UnityFileResultController');
  Route::get('/student/lfiles/unitycourse/create/{topic}', 'UnityFileResultController@create');
  Route::get('/student/lfiles/unitycourse/valid/{topic}', 'UnityFileResultController@submit');
  Route::get('/student/lfiles/unitycourse/delete/{id}/{topic}', 'UnityFileResultController@delete');


  /** Python */
  //Tampilan topik
  Route::get('/student/pythoncourse', 'StudentController@pythoncourse');
  Route::get('/student/pythoncourse/python/task', [ExercisePythonController::class, 'index']);
  //Tampilan detail percobaan
  Route::get('/student/pythoncourse/python/taskdetail/{id_topik}', [ExercisePythonController::class, 'detail_percobaan']);
  //Tampilan pengerjaan (Teks Editor)
  Route::get('/student/pythoncourse/python/pengerjaan/{id_percobaan}', [ExercisePythonController::class, 'teks_editor']);
  // tampilan feedback
  Route::get('/student/pythoncourse/python/feedback/{id_topik}/{id_percobaan}', [ExercisePythonController::class, 'feedback']);
  //Compile Program
  Route::get('/student/pythoncourse/python-compile', [ExercisePythonController::class, 'compiler']);
  //tampilan data validation
  Route::get('student/pythoncourse/python/tampil-data-validation', [ExercisePythonController::class, 'dataValidation']);
  //tampilan result mahasiswa
  Route::get('student/pythoncourse/python/result', [ResultController::class, 'index']);
  Route::get('pythonfeedback', [ExercisePythonController::class, 'feedback_submit']);


  Route::get("/student/pythoncourse/python-history/{id_topik}/{id_percobaan}", [ExercisePythonController::class, 'submit_history']);

  Route::get('/student/androidcourse/asynctask', 'StudentController@asynctask');
  Route::get('/student/androidcourse/firebase', 'StudentController@firebase');

  Route::get('/student', 'StudentController@index');
  Route::resource('/student/tasks', 'TaskStdController');
  Route::resource('/student/results', 'TaskResultController');

  Route::patch('/student/androidcourse/results/valsub', ['as' => 'results.valsub', 'uses' => 'TaskResultController@valsub']);
  Route::get('/student/androidcourse/results/create/{topic}', 'TaskResultController@create');
  Route::resource('/student/androidcourse/lfiles', 'FileResultController');
  Route::get('/student/lfiles/androidcourse/create/{topic}', 'FileResultController@create');
  Route::get('/student/lfiles/androidcourse/valid/{topic}', 'FileResultController@submit');
  Route::get('/student/lfiles/androidcourse/delete/{id}/{topic}', 'FileResultController@delete');
  Route::resource('/student/androidcourse/rankview', 'StudentResultRankController');
  Route::resource('/student/androidcourse/valid', 'StudentValidController');
  Route::resource('/student/androidcourse/rankview', 'StudentResultRankController');
  Route::patch('/student/results/valsub', ['as' => 'results.valsub', 'uses' => 'TaskResultController@valsub']);
  Route::get('student/results/create/{topic}', 'TaskResultController@create');
  Route::resource('/student/lfiles', 'FileResultController');
  Route::get('/student/lfiles/create/{topic}', 'FileResultController@create');
  Route::get('/student/lfiles/valid/{topic}', 'FileResultController@submit');
  Route::get('/student/lfiles/delete/{id}/{topic}', 'FileResultController@delete');
  Route::resource('/student/rankview', 'StudentResultRankController');
  Route::resource('/student/valid', 'StudentValidController');
  Route::resource('/student/rankview', 'StudentResultRankController');
  Route::resource('/student/jplasdown', 'JplasDownloadController');

  Route::resource('/student/uitasks', 'UiTopicStdController');
  Route::get('/student/uifeedback/{topic}', 'UiFeedbackController@create');
  Route::resource('/student/uifeedback', 'UiFeedbackController');
  Route::resource('/student/uiresview', 'UiStudentResultViewController');
  Route::get('/student/uistudentres/{id}', 'UiStudentValidController@show');
  Route::get('/student/uiuploadsrc/{topicid}/{id}', 'UiStudentValidController@showsource');

  Route::resource('/student/exercise', 'ExerciseStdController');
  Route::resource('/student/exercisesubmission', 'ExerciseSubmissionController');
  Route::resource('/student/exercisevalid', 'ExerciseStdValidController');
  
  /* ----------------------------------- SQL ---------------------------------- */
  Route::group(['prefix' => 'student/sqlcourse'], function () {
    Route::group(['prefix' => 'pembelajaran'], function () {
      Route::get('/student/sql', 'StudentController@sqlcourse');
      Route::get('', 'SQLController@learning')->name('student sqlcourse learning');
      Route::get('/read', 'SQLController@learningRead')->name('student sqlcourse learning read');
      Route::get('/detail/{id}', 'SQLController@learningRead')->name('student sqlcourse learning detail');
      Route::post('/create', 'SQLController@learningStore')->name('student sqlcourse learning create');
      Route::post('/update/{id}', 'SQLController@learningUpdate')->name('student sqlcourse learning update');
      Route::get('/delete/{id}', 'SQLController@learningDelete')->name('student sqlcourse learning delete');
      Route::get('/kerjakan/{id}', 'SQLController@learningDo')->name('student sqlcourse learning do');
      Route::post('/kerjakan/{id}', 'SQLController@learningDoExec')->name('student sqlcourse learning do exec');
      Route::get('/reset', 'SQLController@learningDoReset')->name('student sqlcourse learning do reset');
    });

    Route::group(['prefix' => 'praktek'], function () {
      Route::get('', 'SQLController@practice')->name('student sqlcourse practice');
      Route::get('/read', 'SQLController@practiceRead')->name('student sqlcourse practice read');
      Route::get('/detail/{id}', 'SQLController@practiceRead')->name('student sqlcourse practice detail');
      Route::post('/create', 'SQLController@practiceStore')->name('student sqlcourse practice create');
      Route::post('/update/{id}', 'SQLController@practiceUpdate')->name('student sqlcourse practice update');
      Route::get('/delete/{id}', 'SQLController@practiceDelete')->name('student sqlcourse practice delete');
      Route::get('/kerjakan/{id}', 'SQLController@practiceDo')->name('student sqlcourse practice do');
      Route::get('/kerjakan/{id}/{question}', 'SQLController@practiceDo')->name('student sqlcourse practice do question');
      Route::post('/kerjakan/{id}', 'SQLController@practiceDoExec')->name('student sqlcourse practice do exec');
      Route::get('/reset', 'SQLController@practiceDoReset')->name('student sqlcourse practice do reset');
    });

    Route::group(['prefix' => 'latihan'], function () {
      Route::get('', 'SQLController@exercise')->name('student sql exercise');
      Route::get('/read', 'SQLController@exerciseRead')->name('student sqlcourse exercise read');
      Route::get('/detail/{id}', 'SQLController@exerciseRead')->name('student sqlcourse exercise detail');
      Route::post('/create', 'SQLController@exerciseStore')->name('student sqlcourse exercise create');
      Route::post('/update/{id}', 'SQLController@exerciseUpdate')->name('student sqlcourse exercise update');
      Route::get('/delete/{id}', 'SQLController@exerciseDelete')->name('student sqlcourse exercise delete');

      Route::get('/start', 'SQLController@exerciseStart')->name('student sqlcourse exercise start');
      Route::get('/kerjakan/detail/{id}', 'SQLController@exerciseDoDetail')->name('student sqlcourse exercise do detail');
      Route::get('/kerjakan/{id}', 'SQLController@exerciseDo')->name('student sqlcourse exercise do');
      Route::post('/kerjakan/{id}', 'SQLController@exerciseAnswer')->name('student sqlcourse exercise answer');
      Route::get('/complete', 'SQLController@exerciseComplete')->name('student sqlcourse exercise complete');
    });

    Route::group(['prefix' => 'ujian'], function () {
      Route::get('', 'SQLController@exam')->name('student sqlcourse exam');
      Route::get('/read', 'SQLController@examRead')->name('student sqlcourse exam read');
      Route::get('/detail/{id}', 'SQLController@examRead')->name('student sqlcourse exam detail');
      Route::post('/create', 'SQLController@examStore')->name('student sqlcourse exam create');
      Route::post('/update/{id}', 'SQLController@examUpdate')->name('student sqlcourse exam update');
      Route::get('/delete/{id}', 'SQLController@examDelete')->name('student sqlcourse exam delete');

      Route::get('/start', 'SQLController@examStart')->name('student sqlcourse exam start');
      Route::get('/kerjakan/detail/{id}', 'SQLController@examDoDetail')->name('student sqlcourse exam do detail');
      Route::get('/kerjakan/{id}', 'SQLController@examDo')->name('student sqlcourse exam do');
      Route::post('/kerjakan/{id}', 'SQLController@examAnswer')->name('student sqlcourse exam answer');
      Route::get('/complete', 'SQLController@examComplete')->name('student sqlcourse exam complete');
    });
  });
  /* ----------------------------------- SQL ---------------------------------- */
});

Route::middleware(['auth'])->group(function () {
  Route::get('download/guide/{file}/{topic}', 'DownloadController@downGuide')->name('file-download');
  Route::get('download/test/{file}/{topic}', 'DownloadController@downTest')->name('file-download');
  Route::get('download/supp/{file}/{topic}', 'DownloadController@downSupplement')->name('file-download');
  Route::get('download/other/{file}/{topic}', 'DownloadController@downOther')->name('file-download');
  // exercise
  Route::get('download/exerciseguide/{file}/{topic}', 'DownloadController@downExerciseGuide')->name('file-download');
  Route::get('download/exercisetest/{file}/{topic}', 'DownloadController@downExerciseTest')->name('file-download');
  Route::get('download/exercisesupp/{file}/{topic}', 'DownloadController@downExerciseSupplement')->name('file-download');
  Route::get('download/exerciseother/{file}/{topic}', 'DownloadController@downExerciseOther')->name('file-download');
  // jplas
  Route::get('download/jpack/{file}/{topic}', 'DownloadController@downJplasPackage')->name('file-download');
  Route::get('download/jguide/{file}/{topic}', 'DownloadController@downJplasGuide')->name('file-download');
  Route::get('download/jresult/{file}/{topic}', 'DownloadController@downJplasResult')->name('file-download');
});

Auth::routes();
//Route::get('register', 'Auth\RegisterController@index')->name('register');
//Route::get('register', 'Auth\RegisterController@register');

Route::get('/home', 'HomeController@index')->name('home');
