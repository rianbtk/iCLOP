<?php

namespace App\Http\Controllers\python;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExercisePythonController extends Controller
{

    // Menampilkan halaman utama, list topik pembelajaran python
    public function index() {

        // laravel
        $python_topics = [];

        $topik = DB::table('python_topics')->get();
        foreach ( $topik AS $isi_topik ) {

            // data percobaan 
            $percobaan = DB::table("python_percobaan")->where("id_topik", $isi_topik->id_topik);
            
            $totalPercobaan = $percobaan->count();
            $totalPassed = 0;
            $dt_percobaan = $percobaan->get();

            if ( $totalPercobaan > 0 ) {

                foreach ( $dt_percobaan AS $isi_percobaan ) {

                    // ambil data student validation
                    $where = array(

                        'userid'    => Auth::id(),
                        'id_percobaan'  => $isi_percobaan->id_percobaan,
                    );
                    $dt_validation = DB::table("python_students_validation")->where($where)->count();

                    // apakah sudah selesai ? 
                    if ( $dt_validation == 1 ) {

                        $totalPassed++;
                    }
                }
            }


            array_push( $python_topics, array(

                'totalPercobaan'    => $totalPercobaan,
                'totalPassed'       => $totalPassed,
                'isi'               => $isi_topik
            ) );
        }

        return view('student.pythoncourse.python_task.uitask', compact('python_topics'));

    }


    // Menampilkan halaman detail topik pembelajaran (detail percobaan)
    public function detail_percobaan( $id_topik ){

        $infotopik = DB::table('python_topics')->where('id_topik', $id_topik)->first();
        $percobaan = DB::table('python_percobaan')->where('id_topik', $id_topik)->get();

        // auth
        $id = Auth::id();
        $pengerjaan = DB::table('python_students_validation')->where('userid', $id)->get();
        

        return view('student.pythoncourse.python_task.uitask_detail', compact('percobaan', 'infotopik','pengerjaan'));
        
    }


    // Menampilkan halaman pengerjaan (teks editor)
    public function teks_editor( $id_percobaan){

        $percobaan = DB::table('python_percobaan')->where('id_percobaan', $id_percobaan)->first();
        $id_topik = $percobaan->id_topik;

        $infotopik = DB::table('python_topics')->where('id_topik', $id_topik)->first();

        $data['infotopik']  = $infotopik;
        $data['percobaan']  = $percobaan;


        // $paginate = DB::table('python_percobaan');
        
        //Sebelumnya
        $previous = DB::table('python_percobaan')->where('id_percobaan', '<', $id_percobaan)->orderBy('id_percobaan', "desc");
        
        //Selanjutnya
        $next = DB::table('python_percobaan')->where('id_percobaan', '>', $id_percobaan)->orderBy('id_percobaan', "asc");

        $btn_previous = "javascript:;";
        $btn_next = "javascript:;";

        if ( $previous->count() > 0 ) {

            $previous = $previous->first();
            $btn_previous = url('student/pythoncourse/python/pengerjaan/'. $previous->id_percobaan);
        }
        
        if ( $next->count() > 0 ) {

            $next = $next->first();
            $btn_next = url('student/pythoncourse/python/pengerjaan/'. $next->id_percobaan);
        }


        $data['previous'] = $btn_previous;
        $data['next'] = $btn_next;

        return view('student.pythoncourse.python_task.uipengerjaan', $data);
    }



    // Untuk mengcompile code jawaban mahasiswa
    function compiler( Request $request ) {

        // source code dari user
        $textEditor = $request->get('code'); // source code mahasiswa
        $id_topik = $request->get('id_topik');
        $id_percobaan = $request->get('id_percobaan');
        // $time = $request->get('time');


        // ambil data percobaan berdasarkan id_percobaan
        // untuk mengambil filetest dan lokasi package unittest
        $dt_percobaan = DB::table('python_percobaan')->where('id_percobaan', $id_percobaan)->first();


        // file name (nama file)
        $fileName = uniqid().'_'.strtotime("now");

        // // lokasi penyimpanan
        $filePath = "python-resources/unittest/jawaban/". $fileName . '.py';
        

        // file terbuat
        $programFile = fopen($filePath, "w");

        // write code di file
        fwrite($programFile, $textEditor);
        fclose($programFile);
                
        // kondisi apabila file + codding error atau tidak ?
        // memanggil fungsi check error
        $cek_error = $this->check_error( $filePath );
        $statusSintax = false;

        // 0 -> tidak ada error ([1] = status)
        // 0 -> tidak error

        
        if ( $cek_error[1] == 0 ) {

            //ambil direktory
            $packageDirectory = "jawaban.". $fileName;
            $fileUnittest    = $dt_percobaan->filetest;

            $unittest = "C:\\xampp\\htdocs\\iCLOP\\public\\python-resources\\unittest\\". $fileUnittest;

            
            $output = shell_exec("C:\Users\Rania\AppData\Local\Programs\Python\Python310\python.exe ".$unittest." ".$packageDirectory." ".$fileName." --verbose 2>&1");

            $validation_detail = "";
            $status = "";


            
            // split output
            $dataTest = explode("Error", $output);
            // cek string data test memilik nilai OK
            // cek status passed atau failed = Menetukan sttus
            if ( count($dataTest) > 1 ) {
                
                // $validation_detail = $output;
                $status = "FAILED";

            } else {
                
                $status = "PASSED";
            }


            // pembuatan poin error ceklist per unit tetsing
            $validation_detail = "";
            $dt_output = explode("|", $output);
                
            //[0] tidak usah di test
            $title = $dt_output[0];
            $isi = "";
            // urutan itu mengetahui pemisah (cuklekan) 
            foreach ( $dt_output AS $urutan => $isi_error ) {

                if ( $urutan != 0  ) {

                    // pisah string
                    $string = explode(" ", $isi_error);
                    $responseError = explode("-", $isi_error);
                    
                    $fungsiTes = $responseError[0]; // passed
                    $statusTes = $responseError[1]; // failed

                    $pisahStatusTes = explode(" ", $statusTes);
                    
                    // data diolah lagi
                    $cekExeption = explode(" ", $statusTes);
                    // Untuk error nama variabel
                    if ( in_array("no", $cekExeption) ) {

                        $statusTes = $statusTes;
                        $icon = '<i class="fa fa-code" style="color: danger"></i>';
                        $status = "FAILED";

                    } else {

                        // cek error unit testing  
                        $hapusSpasiPesan = rtrim($pisahStatusTes[0]);
                        $hapusSpasiPesan = ltrim($hapusSpasiPesan);
                        if ( $hapusSpasiPesan[1] == "F" ) {

                            $icon = '<i class="fa fa-times" style="color: red"></i>';
                            
                            $statusTes = explode(": ", $statusTes)[1];
                        } else {

                            $icon = '<i class="fa fa-check" style="color: green"></i>';
                            // $statusTes = implode(",", $cekExeption);
                            // $statusTes = var_dump( $cekExeption );
                        }


                    }

                    
                    // echo $fungsiTes;
                    // echo $statusTes.' '.$fungsiTes;
                    // echo '<hr>';
                    $isi .= $icon." ".$fungsiTes ." : ". $statusTes .'<br>';


                    
                    // echo $pisahStatusTes[0].' '. strlen( $pisahStatusTes[0] );
                }
            }

            $validation_detail = $title .'<br>'. $isi;

            // Output tanpa dicleaning
            // $validation_detail = $output

            // mempersiapkan data validation
            $dataSubmit = array(

                'id_topic'          => $id_topik,
                'id_percobaan'      => $id_percobaan,
                'userid'            => Auth::id(),
                'checkstat'         => $status,
                'checkresult'       => $validation_detail,
            );

            //Menambah id berdasarkan yang ada sebelumnya
            $id_submit = DB::table('python_students_submit')->insertGetId( $dataSubmit );

            // Validasi -> Masuk tabel validasi
            // status passed ? 
            $statusPassed = false;
            if ( $status == "PASSED" ) {

                $statusPassed = true;

                $where = array(

                    'userid'            => Auth::id(),
                    'id_percobaan'      => $id_percobaan,
                    'status'            => $status,
                );

                $dataValidation = array(

                    'userid'            => Auth::id(),
                    'id_percobaan'      => $id_percobaan,
                    'status'            => $status,
                    'report'            => $textEditor,
                    'file_submitted'    => $fileName, 
                    // 'time'              => $time
                );

                $cekStatusPassed = DB::table('python_students_validation')->where( $where )->get();
                // 0 tidak ada
                if ( count( $cekStatusPassed ) == 0 )  {

                    // sisipkan id_submit kedalam variabel $dataValidation
                    $dataValidation['id_submit'] = $id_submit;

                    // insert
                    DB::table('python_students_validation')->insert( $dataValidation );
                }
            }

            $statusSintax = true;

            // cek apakah status PASSED ? 
            // $cekValidationPassed = DB::table('python_validation_sub')

            $where = array(

                'id_topic'      => $id_topik,
                'id_percobaan'  => $id_percobaan,
                'userid'        => Auth::id()
            );
            $jmlPercobaanSubmit = DB::table("python_students_submit")->where( $where )->count();

            echo json_encode(['status' => $statusSintax, 'data' => $statusPassed, 'jml' => $jmlPercobaanSubmit]);

        } else {

            echo json_encode(['status' => $statusSintax, 'statusPercobaan' => false, 'data' => $cek_error[0]]);
        }
    }


    // tampil data validation
    public function dataValidation(Request $request) {

        $isi_tabel = "";

        $userid = Auth::id();
        $id_topik = $request->id_topik;
        $id_percobaan = $request->id_percobaan;

        // cari data submit berdasarkan userid + id_topik dan id_percobaan
        $cari = array(

            'id_topic'      => $id_topik,
            'id_percobaan'  => $id_percobaan,
            'userid'        => $userid
        );

        $cekData = DB::table('python_students_submit')->where($cari)->orderBy('created_at', 'desc')->get();

        $status = false;
        $statusPassed = false;

        if ( count($cekData) > 0 ) {

            // uipengerjaan
            $status = true;

            $urutan = count( $cekData );
            foreach ( $cekData AS $isi_kolom ) {

                $arr = explode("\n",$isi_kolom->checkresult);

                // dt_topik 
                $topikBerdasarkanId = DB::table('python_topics')->where('id_topik', $isi_kolom->id_topic)->first();

                $label = "";
                if ( $isi_kolom->checkstat == "PASSED" ) {

                    $label = '<label class="badge badge-success">PASSED</label>';
                    $statusPassed = true;
                } else {

                    $label = '<label class="badge badge-danger">FAILED</label>';
                }

                $isi_tabel .= '<tr>
                    <td>Uji - '.($urutan).'<br><small><b>'.date('d.m.Y H.i', strtotime($isi_kolom->created_at)).'</b></small></td>
                    <td>'.$topikBerdasarkanId->nama.'</td>
                    <td> '.$isi_kolom->checkresult.' </td>
                    <td>'.$label.'</td>
                </tr>';
                $urutan--;
            }
        }


        echo json_encode([

            'status'        => $status,
            'statusPassed'  => $statusPassed,
            'data'      => $isi_tabel
        ]);

    }


    function check_error( $filePath ) {

        // mengeksekusi file tapi nduduhi lek error opo enggak ? 
        // 1 -> error
        // 0 -> gak error
        exec('python '.$filePath.' 2>&1', $output, $status);
        return [$output, $status];
    }


    
    // TAMPIlAN FEEDBACK
    function feedback ( $id_topik, $id_percobaan ){

        $percobaan = DB::table('python_percobaan')->where('id_percobaan', $id_percobaan)->first();
        $id_topik = $percobaan->id_topik;

        $infotopik = DB::table('python_topics')->where('id_topik', $id_topik)->first();

        $data['infotopik']  = $infotopik;
        $data['percobaan']  = $percobaan;
        $data['id_percobaan'] = $id_percobaan;

        return view('student.pythoncourse.python_feedback.feedback', $data);

        // $data['id_topik'] = $id_topik;
        // $data['id_percobaan'] = $id_percobaan;

        // return view('student.python_feedback.feedback', $data);

    }



    // feeback submit
    public function feedback_submit( Request $request ){

        $id_topik       = $request->id_topik;
        $id_percobaan   = $request->id_percobaan;
        $komentar = $request->komentar;
        // $tingkatan = $request->tingkatan;

        $data = array(

            'userid'        => Auth::id(),
            'id_topik'      => $id_topik,
            'id_percobaan'  => $id_percobaan,
            // 'tingkatan'     => $tingkatan,
            'comment'       => $komentar
        );

        // eksekusi insert 
        DB::table('python_feedback')->insert( $data );

        return redirect('student/pythoncourse/python/pengerjaan/'. $id_percobaan);
    }






    // submit history
    public function submit_history( $id_topik, $id_percobaan ) {

        $where = array(

            'id_percobaan'      => $id_percobaan,
            'userid'            => Auth::id()
        );
        $cek = DB::table("python_students_validation")->where( $where )->get();


        $totalPercobaan = DB::table('python_students_submit')->where( $where )->count();

        $statusPassed = false;
        $historyTextEditor = "";
        foreach ( $cek AS $isi ) {

            if ( $isi->status == "PASSED" ) {

                $statusPassed = true;
                $historyTextEditor = $isi->report;
            }
        }

        $data = array(

            'validasi'  => $cek,
            'submit'    => $totalPercobaan,
            'statusPassed'      => $statusPassed,
            'hist_texteditor'   => $historyTextEditor
        );

        echo json_encode( $data );
    }
    
}


