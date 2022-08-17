<?php

namespace App\Http\Controllers\python;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index() {

        
        $dt_hasil = array();

        // ambil hasil berdasarkan id user
        $hasil = DB::table("python_students_validation")->select('python_students_validation.*', 'nama_percobaan', 'nama', 'bab', 'no_percobaan', 'checkresult')
                ->join('python_percobaan', 'python_percobaan.id_percobaan', '=', 'python_students_validation.id_percobaan')
                ->join('python_students_submit', 'python_students_submit.id_submit', '=', 'python_students_validation.id_submit')
                ->join('python_topics', 'python_topics.id_topik', '=', 'python_students_submit.id_topic')
                ->where('python_students_validation.userid', Auth::id())->get();



        // $test = DB::table("python_students_validation")->select('python_students_validation.*')
        // ->join('python_percobaan', 'python_percobaan.id_percobaan', '=', 'python_students_validation.id_percobaan')
        // ->join('python_students_submit', 'python_students_submit.id_submit', '=', 'python_students_validation.id_submit')
        // ->where('python_students_validation.userid', Auth::id())->get();
        
        
        // print_r( $test );

        foreach ( $hasil AS $isi_kolom ) {

            // id_mahasiswa (id yang mengerjakan)
            $id_user = $isi_kolom->userid;

            // ambil informasi data user berdasarkan id_mahasiswa
            $mhs = DB::table('users')->where('id', $id_user)->first();
            $id_dospem = $mhs->uplink;

            // ambil informasi data user berdasarkan id_dosen pembimbng mhs
            $dosen = DB::table('users')->where('id', $id_dospem)->first();

            // tambahkan informasi isi kolom 
            $isi_kolom->nama_dosen = $dosen->name;

            // masukkan ke dalam array dt_hasil
            array_push( $dt_hasil, $isi_kolom );

        }        

        // view
        return view('student.python_result.result_student', compact('dt_hasil'));
    }



    public function student_submit( ) {

        
        $allData = array();


        // tampil data topik
        $dt_topik = DB::table('python_topics')->get();

        foreach ( $dt_topik AS $topik ) {

            // echo '<h2>'.$topik->nama.'</h2>';

            // tampil data percobaan berdasarkan dt_topik
            $dt_percobaan = DB::table('python_percobaan')->where('id_topik', $topik->id_topik)->get();


            // tampil data validation (yang telah mengumpulkan dan bernilai PASSED) : mhs
            $allPercobaan = array();


            foreach ( $dt_percobaan AS $percobaan ) {

                

                $where = [

                    'id_percobaan'  => $percobaan->id_percobaan,
                    'uplink'            => Auth::id() // id_dosen
                ];

                
                $dt_validation = DB::table('python_students_validation')->select('python_students_validation.*', 'users.id', 'users.name')
                    ->join('users', 'users.id', '=', 'python_students_validation.userid')
                    ->where( $where );


                $totalEnroll = $dt_validation->count();
                $dataSubmit  = $dt_validation->get();


                array_push( $allPercobaan, array(

                    'percobaan' => $percobaan,
                    'validation'    => $dataSubmit,
                    'total'     => $totalEnroll,
                ) );
                // echo $percobaan->nama_percobaan.' : '.$totalEnroll.'<br>';
            }


            array_push( $allData, array(

                'topik' => $topik,
                'materi'=> $allPercobaan
            ) );         
        }


        // hitung mahasiswa berdasarkan dospem 
        $mhs = DB::table('users')->where('uplink', Auth::id())->count();
        $dosen = DB::table('users')->where('id', Auth::id())->first();

        return view('teacher.python.py_student_results', compact( 'allData', 'mhs', 'dosen' ));

    }



    // detail 
    public function detail( $id_topik, $id_percobaan ) {


        $allData = array();


        // tampil data topik
        $topik = DB::table('python_topics')->where('id_topik', $id_topik)->first();

        // tampil data percobaan berdasarkan dt_topik
        $dt_percobaan = DB::table('python_percobaan')->where('id_percobaan', $id_percobaan)->first();

        // tampil data validation (yang telah mengumpulkan dan bernilai PASSED) : mhs
        $allPercobaan = array();

        $where = [
            'python_students_validation.id_percobaan'  => $id_percobaan,
            'uplink'            => Auth::id() // id_dosen
        ];

        
        $dt_validation = DB::table('python_students_validation')->select('python_students_validation.*', 'users.id', 'users.name', 'checkresult')
            ->join('python_students_submit', 'python_students_submit.id_submit', '=', 'python_students_validation.id_submit')
            ->join('users', 'users.id', '=', 'python_students_validation.userid')
            ->where( $where );


        $totalEnroll = $dt_validation->count();
        $dataSubmit  = $dt_validation->get();


        $allPercobaan = array(

            'percobaan'     => $dt_percobaan,
            'validation'    => $dataSubmit,
            'total'         => $totalEnroll,
        );


        // hitung mahasiswa berdasarkan dospem 
        $mhs = DB::table('users')->where('uplink', Auth::id())->count();
        $dosen = DB::table('users')->where('id', Auth::id())->first();

        return view('teacher.python.py_student_result_detail', compact( 'allPercobaan', 'mhs', 'dosen', 'topik', 'dt_percobaan' ));        
    }

}
