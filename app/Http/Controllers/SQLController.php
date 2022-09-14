<?php

namespace App\Http\Controllers;

use App\SqlExam;
use App\SqlExamResult;
use App\SqlExamUser;
use App\SqlExercise;
use App\SqlExerciseResult;
use App\SqlExerciseUser;
use App\SqlLearning;
use App\SqlLearningUser;
use App\SqlLearningUserLog;
use App\SqlPractice;
use App\SqlPracticeQuestion;
use App\SqlPracticeUser;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDO;

class SQLController extends Controller
{
    public function learning()
    {
        return view(Auth::user()->roleid . '.sql.learning');
    }
    public function learningRead($id = null)
    {
        switch (Auth::user()->roleid) {
            case 'admin':
                if ($id == null) {
                    $data = SqlLearning::all();
                } else {
                    $data = SqlLearning::findOrFail($id);
                }
                break;

            case 'student':
                if ($id == null) {
                    $data = SqlLearning::select('*')
                        ->selectRaw('COALESCE((SELECT sql_learning_users.status FROM sql_learning_users WHERE sql_learning_users.user_id = ' . Auth::user()->id . ' AND sql_learning_users.sql_learning_id = sql_learnings.id LIMIT 1), "Belum Dikerjakan") AS status')
                        ->get();
                } else {
                    $data = SqlLearning::findOrFail($id);
                }
                break;

            default:
                $data = [];
                break;
        }
        return $data;
    }
    public function learningStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'syntax' => 'required',
            'file' => 'required',
        ], [
            'required'  => 'Silahkan isi bagian :attribute.',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $file = $request->file('file');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move('upload', $filename);

        $data = $validator->validated();
        $data['file'] = 'upload/' . $filename;

        SqlLearning::create($data);
        return 'ok';
    }
    public function learningUpdate(Request $request, $id = null)
    {
        if ($id != null) {
            $dataOld = SqlLearning::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'syntax' => 'required',
            ], [
                'required'  => 'Silahkan isi bagian :attribute.',
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }
            $data = $validator->validated();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move('upload', $filename);
                $data['file'] = 'upload/' . $filename;

                if (file_exists(public_path($dataOld->file))) {
                    unlink(public_path($dataOld->file));
                }
            }

            $dataOld->update($data);
            return 'ok';
        }
    }
    public function learningDelete($id = null)
    {
        if ($id != null) {
            $data = SqlLearning::find($id);
            if (file_exists(public_path($data->file))) {
                unlink(public_path($data->file));
            }
            $data->delete();
        }
    }
    public function learningDo($id = null)
    {
        $data = SqlLearning::findOrFail($id);
        $userLearn = SqlLearningUser::where('user_id', Auth::user()->id)->where('sql_learning_id', $id)->first();
        if (empty($userLearn)) {
            $userLearn = SqlLearningUser::create([
                'status'            => 'sedang dikerjakan',
                'sql_learning_id'   => $id,
                'user_id'           => Auth::user()->id,
            ]);
        }
        $data['user'] = SqlLearningUser::where('user_id', Auth::user()->id)->where('sql_learning_id', $data->id)->with(['input' => function ($query) use($userLearn) {
            $query->where('sql_learning_user_id', $userLearn->id)->orderBy('id', 'DESC')->limit(1);
        }])->first();
        $data['previous'] = SqlLearning::where('id', '<', $id)->max('id');
        $data['next'] = SqlLearning::where('id', '>', $id)->min('id');

        // return $data;
        return view('student.sql.learning_do', ['data' => $data]);
    }
    public function learningDoReset()
    {
        $data = SqlLearning::get();
        foreach ($data as $values) {
            $id = $values->id;
            $data['user'] = SqlLearningUser::where('sql_learning_id', $id)->where('user_id', Auth::user()->id);
            $data['log'] = SqlLearningUserLog::whereIn('sql_learning_user_id', $data['user']->pluck('id')->toArray());

            foreach ($data['log']->get() as $values) {
                if ($values->rollback != '' && $values->status == 1) {
                    $this->sqlExec($values->rollback);
                }

                $values->delete();
            }

            $data['user']->delete();
        }
    }
    public function learningDoExec(Request $request, $id = null)
    {
        if ($id != null) {
            $validator = Validator::make($request->all(), [
                'syntax' => 'required',
            ], [
                'required'  => 'Anda belum menuliskan apapun!',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => 'gagal',
                    [
                        'status' => 'gagal',
                        'message' => $validator->messages()->get('syntax')
                    ]
                ];
            }

            $syntax = [];
            $sql = $request->input('syntax');
            // $sqlRaw = $this->syntaxParse($sql);
            // $data = SqlLearning::findOrFail($id);

            // $db = DB::select('SHOW DATABASES LIKE "%mahasiswa' . Auth::user()->id . '_%"');
            // if (!empty($db)) {
            //     foreach ($db[0] as $key => $value) {
            //         DB::getPdo()->query('use ' . $value);
            //     }
            // }

            // $this->sqlExec($sqlRaw);
            // $sql = $this->tapExec($data->syntax);

            // $userLearn = new SqlLearningUser();
            // $userLearn = $userLearn->where('user_id', Auth::user()->id)->where('sql_learning_id', $id)->firstOrFail();
            // $userLearn->update(['status' => $sql['status']]);

            $data = SqlLearning::findOrFail($id);
            $user = SqlLearningUser::where('user_id', Auth::user()->id)->where('sql_learning_id', $id)->firstOrFail();

            $db = DB::select('SHOW DATABASES LIKE "%pembelajaran_mahasiswa_' . Auth::user()->id . '_%"');
            if (!empty($db)) {
                foreach ($db[0] as $key => $value) {
                    $db = $value;
                }
            } else {
                $db = 'sandbox';
            }

            DB::getPdo()->query('use ' . $db);
            $syntax['user']['syntax'] = $this->syntaxParse($request->input('syntax'), 'pembelajaran');
            $syntax['user']['rollback'] = $this->syntaxRollback($syntax['user']['syntax']);
            $syntax['user']['result'] = $this->sqlExec($syntax['user']['syntax']);

            $syntax['tap']['syntax'] = $data->syntax;
            $syntax['tap']['result'] = $this->tapExec($syntax['tap']['syntax'], 'pembelajaran');

            if ($user->status != 'lulus') {
                $user->update(['status' => $syntax['tap']['result']['status']]);
            }

            if ($syntax['tap']['result']['status'] != 'lulus') {
                DB::getPdo()->query('use ' . $db);
                $this->sqlExec($syntax['user']['rollback']);
            }

            SqlLearningUserLog::create([
                'syntax' => $syntax['user']['syntax'],
                'rollback' => $syntax['user']['rollback'],
                'result' => json_encode($syntax['tap']['result']),
                'status' => (($syntax['tap']['result']['status'] == 'lulus') ? 1 : 0),
                'sql_learning_user_id' => $user->id,
            ]);

            return $syntax['tap']['result'];
        }

        return [
            'status' => 0,
            'error' => [
                'syntax' => 'please check your queries'
            ]
        ];
    }
    public function learningLogRead($id = null)
    {
        $data = [];
        if ($id == null) {
            $users = SqlLearningUser::get()->pluck('user_id')->toArray();
            $data = User::whereIn('id', $users)->get();
        } else {
            $data = SqlLearningUser::where('user_id', $id)->with(['input', 'user'])->get();
        }
        return $data;
    }

    public function practice()
    {
        return view(Auth::user()->roleid . '.sql.practice');
    }
    public function practiceRead($id = null)
    {
        switch (Auth::user()->roleid) {
            case 'admin':
                if ($id == null) {
                    $data = SqlPractice::all();
                } else {
                    $data = SqlPractice::with('question')->findOrFail($id);
                }
                break;

            case 'student':
                if ($id == null) {
                    $data = SqlPractice::select('*')
                        ->selectRaw('(SELECT COUNT(*) FROM sql_practice_users WHERE sql_practice_users.user_id = "' . Auth::user()->id . '" AND sql_practice_users.correct IS NULL AND sql_practice_users.sql_practice_question_id IN (SELECT sql_practice_questions.id FROM sql_practice_questions WHERE sql_practice_questions.sql_practice_id = sql_practices.id)) AS ncomplete')
                        ->selectRaw('(SELECT COUNT(*) FROM sql_practice_users WHERE sql_practice_users.user_id = "' . Auth::user()->id . '" AND sql_practice_users.correct IS NOT NULL AND sql_practice_users.sql_practice_question_id IN (SELECT sql_practice_questions.id FROM sql_practice_questions WHERE sql_practice_questions.sql_practice_id = sql_practices.id)) AS complete')
                        ->selectRaw('(SELECT COUNT(*) FROM sql_practice_users WHERE sql_practice_users.user_id = "' . Auth::user()->id . '" AND sql_practice_users.correct = 1 AND sql_practice_users.sql_practice_question_id IN (SELECT sql_practice_questions.id FROM sql_practice_questions WHERE sql_practice_questions.sql_practice_id = sql_practices.id)) AS correct')
                        ->get();
                } else {
                    $data = SqlPractice::findOrFail($id);
                }
                break;

            default:
                $data = [];
                break;
        }
        return $data;
    }
    public function practiceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'question' => 'required',
        ], [
            'required'  => 'Silahkan isi bagian :attribute.',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $data = $validator->validated();

        $sql = SqlPractice::create([
            'name' => $data['name'],
            'question' => sizeof($data['question'])
        ]);
        if ($sql) {
            foreach ($data['question'] as $value) {
                $values = json_decode($value, true);
                $values['sql_practice_id'] = $sql->id;
                SqlPracticeQuestion::create($values);
            }
        }
        return 'ok';
    }
    public function practiceUpdate(Request $request, $id = null)
    {
        if ($id != null) {
            $dataOld = SqlPractice::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'question' => 'required',
            ], [
                'required'  => 'Silahkan isi bagian :attribute.',
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }
            $data = $validator->validated();

            $ids = [];
            foreach ($data['question'] as $index => $value) {
                $question = SqlPracticeQuestion::where('sql_practice_id', $id)->find($index);
                $values = json_decode($value, true);
                $values['sql_practice_id'] = $id;
                if ($values['question'] != '' && $values['syntax'] != '') {
                    if (empty($question)) {
                        SqlPracticeQuestion::create($values);
                    } else {
                        $question->update($values);
                    }
                    $ids[] = $index;
                }
            }

            SqlPracticeQuestion::where('sql_practice_id', $id)->whereNotIn('id', $ids)->delete();

            $dataOld->update([
                'name' => $data['name'],
                'question' => sizeof($ids)
            ]);


            return 'ok';
        }
    }
    public function practiceDelete($id = null)
    {
        if ($id != null) {
            $data = SqlPractice::find($id);
            $data->delete();
        }
    }
    public function practiceDo($id = null, $question = null)
    {
        $data = [];
        if ($question == null) {
            $query = SqlPracticeQuestion::where('sql_practice_id', $id)->get();
        } else {
            $query = SqlPracticeQuestion::where('sql_practice_id', $id)->where('id', $question)->get();
        }

        if (!empty($query)) {
            $ids = $query->pluck('id')->toArray();
            $userPractice = SqlPracticeUser::where('user_id', Auth::user()->id)->whereIn('sql_practice_question_id', $ids)->first();
            $min = SqlPracticeQuestion::where('sql_practice_id', $id)->min('id');
            if (empty($userPractice)) {
                $userPractice = SqlPracticeUser::create([
                    'sql_practice_question_id'  => $ids[0],
                    'user_id'                   => Auth::user()->id,
                ]);
            }

            if ($question == null) {
                return redirect()->route('student sql practice do question', [$id, $ids[0]]);
            }

            $data['user'] = $userPractice;
            $data['question'] = SqlPracticeQuestion::with('practice')->where('id', $data['user']->sql_practice_question_id)->firstOrFail();
            $data['practice'] = $data['question']['id'] - $min + 1;
            $data['previous'] = SqlPracticeQuestion::where('sql_practice_id', $id)->where('id', '<', $question)->max('id');
            $data['next'] = SqlPracticeQuestion::where('sql_practice_id', $id)->where('id', '>', $question)->min('id');

            // return $data;
            return view('student.sql.practice_do', ['data' => $data]);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }
    public function practiceDoExec(Request $request, $id = null)
    {
        if ($id != null) {
            $validator = Validator::make($request->all(), [
                'syntax' => 'required',
            ], [
                'required'  => 'Anda belum menuliskan apapun!',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => 'gagal',
                    [
                        'status' => 'gagal',
                        'message' => $validator->messages()->get('syntax')
                    ]
                ];
            }

            $syntax = [];
            $sql = $request->input('syntax');

            $data = SqlPracticeQuestion::findOrFail($id);
            $user = SqlPracticeUser::where('user_id', Auth::user()->id)->where('sql_practice_question_id', $id)->firstOrFail();

            $db = DB::select('SHOW DATABASES LIKE "%praktek_mahasiswa_' . Auth::user()->id . '_%"');
            if (!empty($db)) {
                foreach ($db[0] as $key => $value) {
                    $db = $value;
                }
            } else {
                $db = 'sandbox';
            }

            DB::getPdo()->query('use ' . $db);
            $syntax['user']['syntax'] = $this->syntaxParse($request->input('syntax'), 'praktek');
            $syntax['user']['rollback'] = $this->syntaxRollback($syntax['user']['syntax']);
            $syntax['user']['result'] = $this->sqlExec($syntax['user']['syntax']);

            $syntax['tap']['syntax'] = $data->syntax;
            $syntax['tap']['result'] = $this->tapExec($syntax['tap']['syntax'], 'praktek');

            if ($user->correct != 1) {
                switch ($syntax['tap']['result']['status']) {
                    case 'lulus':
                        $user->update([
                            'correct' => 1,
                            'syntax' => $syntax['user']['syntax'],
                            'result' => $syntax['tap']['result']
                        ]);
                        break;

                    default:
                        $user->update([
                            'correct' => 0,
                            'syntax' => $syntax['user']['syntax'],
                            'result' => $syntax['tap']['result']
                        ]);
                        break;
                }
            }

            DB::getPdo()->query('use ' . $db);
            // $this->sqlExec($syntax['user']['rollback']);

            return $syntax['tap']['result'];
        }

        return [
            'status' => 0,
            'error' => [
                'syntax' => 'please check your queries'
            ]
        ];
    }
    public function practiceLogRead()
    {
        $data = [];
        $datas = SqlPractice::with('questions')->get()->toArray();
        foreach ($datas as $key => $value) {
            $users = SqlPracticeUser::select('user_id')->distinct()->get()->pluck('user_id')->toArray();
            foreach ($users as $index => $user) {
                $practice = SqlPracticeUser::where('user_id', $user)->whereIn('sql_practice_question_id', array_column($value['questions'], 'id'))->whereNotNull('correct');
                $all = $practice->count();
                $correct = $practice->where('correct', 1)->count();

                if ($all == $value['question']) {
                    if (!isset($data[$index])) {
                        $data[$index] = User::find($user)->toArray();
                    }
                    $data[$index]['practice'][] = [
                        'name' => $value['name'],
                        'nilai' => $correct / $all * 100,
                    ];
                }
            }
        }
        return $data;
    }

    public function exercise()
    {
        return view(Auth::user()->roleid . '.sql.exercise');
    }
    public function exerciseRead($id = null)
    {
        switch (Auth::user()->roleid) {
            case 'admin':
                if ($id == null) {
                    $data = SqlExercise::all();
                } else {
                    $data = SqlExercise::findOrFail($id);
                }
                break;

            case 'student':
                if ($id == null) {
                    $data = SqlExerciseResult::where('user_id', Auth::user()->id)->where('status', 'selesai')->get();
                } else {
                    $data = SqlExercise::findOrFail($id);
                }
                break;

            default:
                $data = [];
                break;
        }
        return $data;
    }
    public function exerciseStart()
    {
        $result = SqlExerciseResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (empty($result)) {
            $result = SqlExerciseResult::create([
                'status' => 'sedang dikerjakan',
                'user_id' => Auth::user()->id
            ]);
        }

        $exercise = SqlExercise::first();
        if (empty($exercise)) {
            return redirect()->route('student sql exercise');
        }

        $input = SqlExerciseUser::where('sql_exercise_result_id', $result->id)->orderBy('id', 'DESC')->first();
        if (empty($input)) {
            $input = SqlExerciseUser::create([
                'answer' => null,
                'sql_exercise_id' => $exercise->id,
                'sql_exercise_result_id' => $result->id,
            ]);
        }

        return redirect()->route('student sql exercise do', $input->sql_exercise_id);
    }
    public function exerciseDo($id = null)
    {
        $data = [];

        $result = SqlExerciseResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (empty($result)) {
            $result = SqlExerciseResult::create([
                'status' => 'sedang dikerjakan',
                'user_id' => Auth::user()->id
            ]);
        }

        $input = SqlExerciseUser::where('sql_exercise_id', $id)->where('sql_exercise_result_id', $result->id)->orderBy('id', 'DESC')->first();
        if (empty($input)) {
            $input = SqlExerciseUser::create([
                'answer' => null,
                'sql_exercise_id' => $id,
                'sql_exercise_result_id' => $result->id,
            ]);
        }

        $exercise = SqlExercise::find($id);
        if (!empty($exercise)) {
            $data['semua'] = SqlExercise::orderBy('id', 'ASC')->get()->pluck('id')->toArray();
            $data['terjawab'] = SqlExerciseUser::whereNotNull('answer')->where('sql_exercise_result_id', $result->id)->orderBy('sql_exercise_id', 'ASC')->get()->pluck('sql_exercise_id')->toArray();
            $data['sekarang'] = $id;
            $data['belum'] = SqlExercise::whereNotIn('id', $data['terjawab'])->orderBy('id', 'ASC')->get()->pluck('id')->toArray();
            $data['next'] = SqlExercise::where('id', '>', $id)->min('id');
            $data['soal'] = $exercise->question;

            $jawaban[0] = $exercise->answer_1;
            $jawaban[1] = $exercise->answer_2;
            $jawaban[2] = $exercise->answer_3;
            $jawaban[3] = $exercise->answer_4;

            $data['input'] = null;
            $userInput = null;
            if (!empty($input) && $input->answer != null) {
                $userInput = $jawaban[$input->answer - 1];
            }

            shuffle($jawaban);

            if ($userInput != null) {
                $data['input'] = array_search($userInput, $jawaban);
            }

            $data['jawaban'] = $jawaban;

            return view('student.sql.exercise_do', ['data' => $data]);
        }

        return redirect()->route('student sql exercise');
    }
    public function exerciseDoDetail($id = null)
    {
        $data = [];

        $exercise = SqlExercise::orderBy('id')->get();
        foreach ($exercise as $key => $value) {
            $user = SqlExerciseUser::where('sql_exercise_result_id', $id)->where('sql_exercise_id', $value->id)->first();
            $jawaban = [];
            $jawaban[0] = $value->answer_1;
            $jawaban[1] = $value->answer_2;
            $jawaban[2] = $value->answer_3;
            $jawaban[3] = $value->answer_4;

            $input = null;

            if ($user != null && $user->answer != null) {
                $input = $jawaban[$user->answer - 1];
            }

            shuffle($jawaban);

            $correct = array_search($value->answer_1, $jawaban) + 1;

            if ($input != null) {
                $input = array_search($input, $jawaban) + 1;
            }

            $data[] = array(
                'soal' => $value->question,
                'jawaban_1' => $jawaban[0],
                'jawaban_2' => $jawaban[1],
                'jawaban_3' => $jawaban[2],
                'jawaban_4' => $jawaban[3],
                'input' => $input,
                'correct' => $correct
            );
        }

        return $data;
    }
    public function exerciseAnswer(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'jawaban' => 'required',
        ], [
            'required'  => 'Silahkan isi bagian :attribute.',
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $result = SqlExerciseResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (empty($result)) {
            $result = SqlExerciseResult::create([
                'status' => 'sedang dikerjakan',
                'user_id' => Auth::user()->id
            ]);
        }

        $input = SqlExerciseUser::where('sql_exercise_id', $id)->where('sql_exercise_result_id', $result->id)->orderBy('id', 'DESC')->first();
        if (empty($input)) {
            $input = SqlExerciseUser::create([
                'answer' => null,
                'sql_exercise_id' => $id,
                'sql_exercise_result_id' => $result->id,
            ]);
        }

        $exercise = SqlExercise::find($id);
        if (!empty($exercise)) {
            $jawaban[0] = $exercise->answer_1;
            $jawaban[1] = $exercise->answer_2;
            $jawaban[2] = $exercise->answer_3;
            $jawaban[3] = $exercise->answer_4;

            $input->answer = array_search($request->input('jawaban'), $jawaban) + 1;
            $input->save();

            $next = SqlExercise::where('id', '>', $id)->min('id');
            if ($next != null) {
                return redirect()->route('student sql exercise do', $next);
            } else {
                $next = SqlExercise::min('id');
                return redirect()->route('student sql exercise do', $next);
            }
        }
    }
    public function exerciseComplete()
    {
        $result = SqlExerciseResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (!empty($result)) {
            $total = SqlExercise::count();
            $benar = SqlExerciseUser::where('answer', 1)->where('sql_exercise_result_id', $result->id)->count();

            $result->nilai = $benar / $total * 100;
            $result->status = 'selesai';
            $result->save();

            return redirect()->route('student sql exercise');
        }

        return redirect()->back();
    }
    public function exerciseStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer_1' => 'required',
            'answer_2' => 'required',
            'answer_3' => 'required',
            'answer_4' => 'required',
        ], [
            'required'  => 'Silahkan isi bagian :attribute.',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $data = $validator->validated();

        SqlExercise::create($data);
        return 'ok';
    }
    public function exerciseUpdate(Request $request, $id = null)
    {
        if ($id != null) {
            $dataOld = SqlExercise::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer_1' => 'required',
                'answer_2' => 'required',
                'answer_3' => 'required',
                'answer_4' => 'required',
            ], [
                'required'  => 'Silahkan isi bagian :attribute.',
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }
            $data = $validator->validated();

            $dataOld->update($data);
            return 'ok';
        }
    }
    public function exerciseDelete($id = null)
    {
        if ($id != null) {
            $data = SqlExercise::find($id);
            $data->delete();
        }
    }
    public function exerciseLogRead()
    {
        $data = [];
        $users = SqlExerciseResult::get()->pluck('user_id')->toArray();
        $data = User::whereIn('id', $users)->with('exercise')->get();
        return $data;
    }

    public function exam()
    {
        return view(Auth::user()->roleid . '.sql.exam');
    }
    public function examRead($id = null)
    {
        switch (Auth::user()->roleid) {
            case 'admin':
                if ($id == null) {
                    $data = SqlExam::all();
                } else {
                    $data = SqlExam::findOrFail($id);
                }
                break;

            case 'student':
                if ($id == null) {
                    $data = SqlExamResult::where('user_id', Auth::user()->id)->where('status', 'selesai')->get();
                } else {
                    $data = SqlExam::findOrFail($id);
                }
                break;

            default:
                $data = [];
                break;
        }
        return $data;
    }
    public function examStart()
    {
        $result = SqlExamResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (empty($result)) {
            $result = SqlExamResult::create([
                'status' => 'sedang dikerjakan',
                'user_id' => Auth::user()->id
            ]);
        }

        $exam = SqlExam::first();
        if (empty($exam)) {
            return redirect()->route('student sql exam');
        }

        $input = SqlExamUser::where('sql_exam_result_id', $result->id)->orderBy('id', 'DESC')->first();
        if (empty($input)) {
            $input = SqlExamUser::create([
                'answer' => null,
                'sql_exam_id' => $exam->id,
                'sql_exam_result_id' => $result->id,
            ]);
        }

        return redirect()->route('student sql exam do', $input->sql_exam_id);
    }
    public function examDo($id = null)
    {
        $data = [];

        $result = SqlExamResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (empty($result)) {
            $result = SqlExamResult::create([
                'status' => 'sedang dikerjakan',
                'user_id' => Auth::user()->id
            ]);
        }

        $input = SqlExamUser::where('sql_exam_id', $id)->where('sql_exam_result_id', $result->id)->orderBy('id', 'DESC')->first();
        if (empty($input)) {
            $input = SqlExamUser::create([
                'answer' => null,
                'sql_exam_id' => $id,
                'sql_exam_result_id' => $result->id,
            ]);
        }

        $exam = SqlExam::find($id);
        if (!empty($exam)) {
            $data['semua'] = SqlExam::orderBy('id', 'ASC')->get()->pluck('id')->toArray();
            $data['terjawab'] = SqlExamUser::whereNotNull('answer')->where('sql_exam_result_id', $result->id)->orderBy('sql_exam_id', 'ASC')->get()->pluck('sql_exam_id')->toArray();
            $data['sekarang'] = $id;
            $data['belum'] = SqlExam::whereNotIn('id', $data['terjawab'])->orderBy('id', 'ASC')->get()->pluck('id')->toArray();
            $data['next'] = SqlExam::where('id', '>', $id)->min('id');
            $data['soal'] = $exam->question;

            $jawaban[0] = $exam->answer_1;
            $jawaban[1] = $exam->answer_2;
            $jawaban[2] = $exam->answer_3;
            $jawaban[3] = $exam->answer_4;

            $data['input'] = null;
            $userInput = null;
            if (!empty($input) && $input->answer != null) {
                $userInput = $jawaban[$input->answer - 1];
            }

            shuffle($jawaban);

            if ($userInput != null) {
                $data['input'] = array_search($userInput, $jawaban);
            }

            $data['jawaban'] = $jawaban;

            return view('student.sql.exam_do', ['data' => $data]);
        }

        return redirect()->route('student sql exam');
    }
    public function examDoDetail($id = null)
    {
        $data = [];

        $exam = SqlExam::orderBy('id')->get();
        foreach ($exam as $key => $value) {
            $user = SqlExamUser::where('sql_exam_result_id', $id)->where('sql_exam_id', $value->id)->first();
            $jawaban = [];
            $jawaban[0] = $value->answer_1;
            $jawaban[1] = $value->answer_2;
            $jawaban[2] = $value->answer_3;
            $jawaban[3] = $value->answer_4;

            $input = null;

            if ($user != null && $user->answer != null) {
                $input = $jawaban[$user->answer - 1];
            }

            shuffle($jawaban);

            $correct = array_search($value->answer_1, $jawaban) + 1;

            if ($input != null) {
                $input = array_search($input, $jawaban) + 1;
            }

            $data[] = array(
                'soal' => $value->question,
                'jawaban_1' => $jawaban[0],
                'jawaban_2' => $jawaban[1],
                'jawaban_3' => $jawaban[2],
                'jawaban_4' => $jawaban[3],
                'input' => $input,
                'correct' => $correct
            );
        }

        return $data;
    }
    public function examAnswer(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'jawaban' => 'required',
        ], [
            'required'  => 'Silahkan isi bagian :attribute.',
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $result = SqlExamResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (empty($result)) {
            $result = SqlExamResult::create([
                'status' => 'sedang dikerjakan',
                'user_id' => Auth::user()->id
            ]);
        }

        $input = SqlExamUser::where('sql_exam_id', $id)->where('sql_exam_result_id', $result->id)->orderBy('id', 'DESC')->first();
        if (empty($input)) {
            $input = SqlExamUser::create([
                'answer' => null,
                'sql_exam_id' => $id,
                'sql_exam_result_id' => $result->id,
            ]);
        }

        $exam = SqlExam::find($id);
        if (!empty($exam)) {
            $jawaban[0] = $exam->answer_1;
            $jawaban[1] = $exam->answer_2;
            $jawaban[2] = $exam->answer_3;
            $jawaban[3] = $exam->answer_4;

            $input->answer = array_search($request->input('jawaban'), $jawaban) + 1;
            $input->save();

            $next = SqlExam::where('id', '>', $id)->min('id');
            if ($next != null) {
                return redirect()->route('student sql exam do', $next);
            } else {
                $next = SqlExam::min('id');
                return redirect()->route('student sql exam do', $next);
            }
        }
    }
    public function examComplete()
    {
        $result = SqlExamResult::where('user_id', Auth::user()->id)->where('status', 'sedang dikerjakan')->first();
        if (!empty($result)) {
            $total = SqlExam::count();
            $benar = SqlExamUser::where('answer', 1)->where('sql_exam_result_id', $result->id)->count();

            $result->nilai = $benar / $total * 100;
            $result->status = 'selesai';
            $result->save();

            return redirect()->route('student sql exam');
        }

        return redirect()->back();
    }
    public function examStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer_1' => 'required',
            'answer_2' => 'required',
            'answer_3' => 'required',
            'answer_4' => 'required',
        ], [
            'required'  => 'Silahkan isi bagian :attribute.',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $data = $validator->validated();

        SqlExam::create($data);
        return 'ok';
    }
    public function examUpdate(Request $request, $id = null)
    {
        if ($id != null) {
            $dataOld = SqlExam::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer_1' => 'required',
                'answer_2' => 'required',
                'answer_3' => 'required',
                'answer_4' => 'required',
            ], [
                'required'  => 'Silahkan isi bagian :attribute.',
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }
            $data = $validator->validated();

            $dataOld->update($data);
            return 'ok';
        }
    }
    public function examDelete($id = null)
    {
        if ($id != null) {
            $data = SqlExam::find($id);
            $data->delete();
        }
    }
    public function examLogRead()
    {
        $data = [];
        $users = SqlExamResult::get()->pluck('user_id')->toArray();
        $data = User::whereIn('id', $users)->with('exam')->get();
        return $data;
    }

    protected function sqlExec($sql = '')
    {
        $result = [];
        $sqlLines = explode(';', $sql);
        foreach ($sqlLines as $key => $value) {
            try {
                DB::getPdo()->query($value);
            } catch (\Throwable $th) {
                continue;
            }
        }
        DB::getPdo()->query('use ' . env('DB_DATABASE'));
        return $result;
    }

    protected function tapExec($sql = '', $prefix = '')
    {
        $result = [];
        $sql = explode(';', $sql);
        $result['status'] = 'lulus';
        foreach ($sql as $key => $value) {
            try {
                $value = $this->tapParse($value, $prefix);
                $res = DB::connection('tap')->getPdo()->query($value)->fetchAll();
                if (!empty($res) && str_contains(strtolower($res[0][0]), "ok") && str_contains(strtolower($res[0][0]), "-")) {
                    $resl = [
                        'message' => rtrim(substr($res[0][0], 0, (strpos($res[0][0], '#') != false) ? strpos($res[0][0], '#') : strlen($res[0][0]))),
                        'status' => 'lulus'
                    ];
                    if (str_contains(strtolower($res[0][0]), "not ok")) {
                        $result['status'] = 'gagal';
                        $resl['status'] = 'gagal';
                    }

                    $result[] = $resl;
                }
            } catch (\Throwable $th) {
                continue;
            }
        }
        DB::getPdo()->query('use ' . env('DB_DATABASE'));
        return $result;
    }

    protected function syntaxParse($sql = '', $prefix = '')
    {
        $needs = ['database', 'use'];
        $lastPos = 0;
        foreach ($needs as $key => $needle) {
            while (($lastPos = stripos($sql, $needle, $lastPos)) !== false) {
                $first = $lastPos + strlen($needle);
                $last = $this->strposa($sql, [' ', ';', PHP_EOL], $first + 1);
                if ($last == false) {
                    $last = strlen($sql);
                }

                $name = preg_replace('/\s+|;+/', '', substr($sql, $first, $last - $first));
                // $sql = str_replace($name, 'user'.Auth::user()->id . '_' . $name, $sql);

                $sql = substr_replace($sql, $prefix . '_mahasiswa_'  . Auth::user()->id . '_' . $name, $first + 1, $last - $first - 1);
                $lastPos = $first;
            }
        }
        return $sql;
    }

    protected function syntaxRollback($sql = '')
    {
        $rollback = '';
        $needs = ['create table', 'create database'];
        $lastPos = 0;
        foreach ($needs as $key => $needle) {
            while (($lastPos = stripos($sql, $needle, $lastPos)) !== false) {
                $first = $lastPos + strlen($needle) + 1;
                $last = $this->strposa($sql, [' ', '(', ';', PHP_EOL], $first + 1);
                if ($last == false) {
                    $last = strlen($sql);
                }

                $name = preg_replace('/\s+|;+/', '', substr($sql, $first, $last - $first));

                $rollback .= str_replace('create', 'drop', $needle) . ' ' . $name . ';';
                $lastPos = $last;
            }
        }

        return $rollback;
    }

    protected function tapParse($sql = '', $prefix = '')
    {
        $needs = [
            'has_schema',
            'has_table',
            '__hasnt_table',
            'table_engine_is',
            'table_collation_is',
            'table_character_set_is',
            'tables_are',
            'table_sha1_is',
            'has_column',
            'hasnt_column',
            'col_is_null',
            'col_not_null',
            'col_has_primary_key',
            'col_hasnt_primary_key',
            'col_has_index_key',
            'col_hasnt_index_key',
            'col_has_unique_index',
            'col_hasnt_unique_index',
            'col_has_named_index',
            'col_has_pos_in_named_index',
            'col_hasnt_pos_in_named_index',
            'col_has_type',
            'col_data_type_is',
            'col_column_type_is',
            'col_has_default',
            'col_hasnt_default',
            'col_default_is',
            'col_extra_is',
            'col_charset_is',
            'col_character_set_is',
            'col_collation_set_is',
            'columns_are',
            'has_routine',
            'has_function',
            'hasnt_function',
            'has_procedure',
            'function_data_type_is',
            'function_is_deterministic',
            'procedure_is_deterministic',
            'function_security_type_is',
            'procedure_security_type_is',
            'function_sql_data_access_is',
            'procedure_sql_data_access_is',
            'routines_are',
            'routine_has_sql_mode',
            'routine_sha1_is',
            'has_view',
            'has_security_invoker',
            'has_security_definer',
            'view_security_type_is',
            'view_check_option_is',
            'view_is_updatable',
            'view_definer_is',
            'views_are',
            'has_trigger',
            'hasnt_trigger',
            'trigger_event_is',
            'trigger_timing_is',
            'trigger_order',
            'trigger_is',
            'triggers_are',
            'schema_collation_is',
            'schema_character_set_is',
            'schemas_are',
            'has_event',
            'hasnt_event',
            'event_type_is',
            'event_interval_value_is',
            'event_interval_field_is',
            'event_status_is',
            'events_are',
            'has_constraint',
            'has_pk',
            'hasnt_pk',
            'has_fk',
            'hasnt_fk',
            'col_is_unique',
            'col_is_pk',
            'has_unique',
            'constraint_type_is',
            'fk_on_delete',
            'fk_on_update',
            'fk_ok',
            'index_is',
            'is_indexed',
            'has_index',
            'hasnt_index',
            'index_is_type',
            'indexes_are',
            'has_partition',
            'hasnt_partition',
            'has_subpartition',
            'hasnt_subpartition',
            'partition_expression_is',
            'subpartition_expression_is',
            'partition_method_is',
            'subpartition_method_is',
            'partition_count_is',
            'partitions_are',
        ];
        $lastPos = 0;
        foreach ($needs as $key => $needle) {
            while (($lastPos = stripos($sql, $needle, $lastPos)) !== false) {
                $first = $this->strposa($sql, ["'"], $lastPos);
                $last = $this->strposa($sql, [','], $first);
                if ($last == false) {
                    $last = strlen($sql);
                }

                $name = preg_replace("/\s+|;+|'+/", '', substr($sql, $first, $last - $first));
                $sql = substr_replace($sql, "'" . $prefix . "_mahasiswa_" . Auth::user()->id . '_' . $name . "'", $first, $last - $first);
                $lastPos = $lastPos + strlen($needle);
            }
        }
        return $sql;
    }

    function strposa($haystack, $needles = array(), $offset = 0)
    {
        $chr = array();
        foreach ($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
        }
        if (empty($chr)) return false;
        return min($chr);
    }
}
