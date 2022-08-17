<?php

namespace App\Http\Controllers\python;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PythonLearningTopicsController extends Controller
{

    //Tampilan Tabel Topik
    public function index() {

        $topik = DB::table('python_topics')->get();

        return view('admin.python.topik', compact('topik'));

    }

    //Tampilan Tambah Topik
    public function tambah() {

        return view('admin.python.tambah');

    }

    //Proses Tambah Topik
    public function proses_tambah( Request $request ) {

        $ambilBab = $request->input('bab');
        $ambilNamaTopik = $request->input('nama');
        $ambilDeskripsi = $request->input('deskripsi');
        $ambilstatus = $request->input('status');

        $dt_python_topics = array(

            'bab'           => $ambilBab,
            'nama'          => $ambilNamaTopik,
            'deskripsi'     => $ambilDeskripsi,
            'status'        => $ambilstatus
        );

        DB::table('python_topics')->insert( $dt_python_topics );
        return redirect('/admin/python/topic');
    }

    //Proses Hapus
    public function proses_hapus( $id_topik ) {

        DB::table('python_topics')->where('id_topik','=', $id_topik)->delete();
        return redirect('/admin/python/topic');
    }

    //Tampilan Edit
    public function edit($id_topik) {

        $topik = DB::table('python_topics')->where('id_topik','=', $id_topik)->first();
        return view('admin.python.edit', compact('topik'));

    }

    //Proses Edit
    public function proses_edit( Request $request, $id_topik ) {

        $ambilBab = $request->input('bab');
        $ambilNamaTopik = $request->input('nama');
        $ambilDeskripsi = $request->input('deskripsi');
        $ambilstatus = $request->input('status');

        $dt_python_topics = array(

            'bab'           => $ambilBab,
            'nama'          => $ambilNamaTopik,
            'deskripsi'     => $ambilDeskripsi,
            'status'        => $ambilstatus
        );

        DB::table('python_topics')->where('id_topik','=', $id_topik)->update( $dt_python_topics );
        return redirect('/admin/python/topic');
    }
}
