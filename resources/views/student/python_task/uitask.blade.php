@extends('student/home')
@section('content')
<div class="row" style="padding: 32px">
    
    <div class="col-12">
        <h4>Start Learning Basic Python </h4>
        <p>Pilih topik dibawah ini untuk memulai pembelajaran</p>
    </div>

    <div class="col-12">

        <div class="card card-body">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Bab</th>
                        <th>Nama Topik</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $python_topics AS $index => $tp )

                    @if ($tp['isi']->status == "1")
                    <tr>
                        <td class="text-center">{{ $tp['isi']->bab }}</td>
                        <td>{{ $tp['isi']->nama }}</td>
                        <td width="25%">{{ $tp['isi']->deskripsi }}</td>
                        <td>{{ $tp['totalPercobaan'] }} Percobaan
                            <br>
                            <small>Telah menyelesaikan {{ $tp['totalPassed'].'/'.$tp['totalPercobaan'] }}
                        </td>
                        <td class="text-center">

                            @php 

                                $color = "success";
                                $icon = "fa fa-eye";
                                if ( $tp['totalPassed'] == $tp['totalPercobaan'] ) {

                                    $color = "warning";
                                    $icon = "fa fa-check";
                                }
                            @endphp
                            <a class="btn btn-{{ $color }}" href="{{ url('student/python/taskdetail/'. $tp['isi']->id_topik) }}"><i class="{{ $icon }}"></i></a>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection