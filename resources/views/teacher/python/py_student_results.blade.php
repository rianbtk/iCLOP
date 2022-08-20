@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Python Student Learning Result</h3>
                <div class="card-tools">

                </div>
            </div>

            <div class="card-body">
                @if (Session::has('message'))
                <div id="alert-msg" class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                    {{ Session::get('message') }}
                </div>
                @endif

                <div class="row" style="margin-bottom: 30px">
                    <div class="col-md-4" style="border-right: 2px solid #e0e0e0">
                        <b>Dosen Pengajar Oleh</b>
                        <h3>{{ $dosen->name }}</h3>
                    </div>
                    <div class="col-md-4">
                        <b>Total mahasiswa yang mengikuti kelas</b>
                        <h3>{{ $mhs }} Mahasiswa</h3>
                    </div>
                </div>
                

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Topik</th>
                                    <th>Percobaan</th>
                                    <th>Terkumpul</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ( $allData AS $index => $isi )

                                @php

                                $rowspan = count( $isi['materi'] ) + 1;

                                @endphp
                                <tr style="border-bottom: 2px solid #e0e0e0">
                                    <td rowspan="{{ $rowspan }}">
                                        <small>Nama Topik</small><br>
                                        <b>{{ $isi['topik']->nama }}</b>
                                    </td>
                                </tr>

                                @foreach ( $isi['materi'] AS $materi )
                                <tr style="border-bottom: 2px solid #e0e0e0">
                                    <td>{{ $materi['percobaan']->nama_percobaan }}</td>
                                    <td>
                                        @php

                                        if ( $materi['total'] > 0 ) {

                                        echo $materi['total'].' mhs';
                                        } else {

                                        echo '-';
                                        }

                                        @endphp
                                    </td>
                                    <td>
                                        @php 

                                            $label = "";
                                            if ( count( $materi['validation'] ) == $mhs ) {

                                                $label = '<label class="badge badge-info">Materi Selesai</label>';
                                            
                                            } else if ( count( $materi['validation'] ) == 0 ) {

                                                $label = '<label class="badge badge-secondary">Kosong</label>';
                                            } else if ( count( $materi['validation'] ) != $mhs ) {

                                                $label = '<label class="badge badge-warning">Sebagian</label>';
                                            }


                                            // echo count( $materi['validation'] );
                                            echo $label;

                                        @endphp 
                                    </td>
                                    <td><a href="{{ url('teacher/python/resultstudentdetail/'. $isi['topik']->id_topik.'/'.$materi['percobaan']->id_percobaan) }}" class="btn btn-primary btn-sm"><i class="fa fa-book"></i> Detail</a></td>
                                </tr>
                                @endforeach
                                @endforeach

                            </tbody>
                        </table>


                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
