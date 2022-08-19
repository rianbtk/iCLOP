@extends('student/pythoncourse/home')
@section('content')
<div class="row" style="padding: 32px">

    <div class="col-12">
        <h4>{{$infotopik->nama}}</h4>
        <p>Pilih percobaan dibawah ini untuk memulai pembelajaran</p>
    </div>

    <!-- Button -->
    <div class="button-group">
        <input type="button" value="Kembali" onclick="window.location.href='{{ url('student/pythoncourse/python/task/') }}'"
            class="mx-1 btn btn-outline-primary" style="min-width: 120px; min-height: 45px; margin-bottom: 20px">
    </div>

    <div class="col-12">
        <div class="card card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Percobaan</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $percobaan AS $index => $pc )


                    @php 

                    $status_pengerjaan = false;

                    if ( count( $pengerjaan ) > 0 ) {

                        foreach ( $pengerjaan AS $pngrj ) {

                            if ( $pngrj->id_percobaan == $pc->id_percobaan ) {

                                $status_pengerjaan = true;
                                break;
                            }
                        }
                    }



                    // - - - - - - - - 
                    // warna button
                    $colorBtn = "";
                    $iconBtn = '';

                    if ( $status_pengerjaan == true ) {

                        $colorBtn = "btn btn-warning";
                        $iconBtn = '<i class="fa fa-check"></i>';
                    } else {

                        $colorBtn = "btn btn-success";
                        $iconBtn = '<i class="fa fa-hand-pointer"></i>';
                    }

                    @endphp
                    <tr>
                        <td class="text-center">{{ $index +1 }}</td>
                        <td>{{ $pc->nama_percobaan }}</td>
                        <td>{{ $pc->deskripsi }}</td>
                        <td class="text-center">
                            <a class="{{ $colorBtn }}"
                                href="{{ url('student/pythoncourse/python/pengerjaan/'. $pc->id_percobaan) }}"><?php echo $iconBtn ?></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@endsection
