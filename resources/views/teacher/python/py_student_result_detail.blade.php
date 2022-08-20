@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Python Student Learning Result - Detail</h3>
                <div class="card-tools">

                </div>
            </div>

            <div class="card-body">
               
                {{-- <div class="row" style="margin-bottom: 30px">
                    <div class="col-md-4" style="border-right: 2px solid #e0e0e0">
                        <b>Dosen Pengajar Oleh</b>
                        <h3>{{ $dosen->name }}</h3>
                    </div>
                    <div class="col-md-4">
                        <b>Hasil Dari Pembelajaran Mahasiswa :  </b>
                        <h3>{{ $mhs }} Mahasiswa</h3>
                    </div>
                </div> --}}
                

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2">
                                        <b><small>Topik</small></b><br>
                                        <b>{{ $topik->nama }}</b>
                                    </th>
                                    <th colspan="4">Pengumpulan Detail</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Nama Mahasiswa</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Waktu</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="{{ count( $allPercobaan['validation'] ) + 1 }}">
                                        <small>Nama Percobaan</small>
                                        <h4>{{ $dt_percobaan->nama_percobaan }}</h4>
                                    </td>
                                </tr>

                                @foreach ( $allPercobaan['validation'] AS $nomor => $isi )
                                <tr>
                                    <td>{{ $isi->name }}</td>
                                    <td>{{ $isi->status }}</td>
                                    <td>{{ date('d M Y H.i A', strtotime($isi->create_at)) }}</td>
                                    <td>

                                        <a href="javascript:;" data-toggle="modal" data-target="#modal-{{ $nomor }}" class="btn btn-sm btn-primary">Submitted</a>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modal-{{ $nomor }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    
                                                    <b>Materi {{ $topik->nama }}</b>
                                                    <h5 style="margin: 0px"><code>{{ $dt_percobaan->nama_percobaan }}</code></h5>
                                                    <small>Pengerjaan pada {{ date('d F Y H.i A', strtotime($isi->create_at)) }}</small>
                                                    <div class="card card-body" style="font-family: 'Courier New', Courier, monospace">
                                                        {{ $isi->report }}

                                                        <hr>
                                                        <b>Hasil Unit Test : </b><br>
                                                        <?php echo $isi->checkresult ?>
                                                    </div>
                                                    <a href="{{ asset('python-resources/unittest/jawaban/'. $isi->file_submitted) }}.py" download>Unduh Source Code</a><br>
                                                    <small>Klik untuk mengunduh file jawaban</small>

                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
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
