@extends('student/pythoncourse/home')

@section('content')

<div class="row">

    <div class="col-12">

        <form action="{{ url('pythonfeedback') }}" type="POST">

            @csrf

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">Submit Hasil Percobaan {{$percobaan->no_percobaan.' : '.$percobaan->nama_percobaan}}</h3>

            </div>

            <div class="card-body">

                @if (Session::has('message'))

                <div id="alert-msg" class="alert alert-success alert-dismissible">

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>

                    {{ Session::get('message') }}

                </div>

                @endif



                @if(!empty($errors->all()))

                <div class="alert alert-danger">

                    {{ Html::ul($errors->all())}}

                </div>

                @endif



                <div class="row">

                    <div class="col-md-6">

                        {{-- <div class="form-group">
                            <label for="">Durasi (menit)</label>
                            <input type="number" name="durasi" class="form-control" placeholder="Masukkan estimasi durasi pengerjaan . . ." required="" />
                            <small>Berisi estimasi durasi pengerjaan percobaan {{$percobaan->no_percobaan.' : '.$percobaan->nama_percobaan}}</small>
                        </div> --}}
                        {{-- <div class="form-group">
                            <label>Tingkatan Kesulitan Percobaan</label>
                            <select class="form-control" name="tingkatan">
                                <option value="mudah">Mudah</option>
                                <option value="sulit">Sulit</option>
                            </select>

                        </div> --}}
                        <div cslass="form-group">
                            <label for="">Komentar </label>
                            <textarea name="komentar" class="form-control" placeholder="Berikan feedback dari topik yang sudah disediakan" required=""></textarea>
                            <small>Berisi pendapat mahasiswa mengenai topik pembelajaran yang dikerjakan -> Contoh : Saya bisa menyelesaikan topik ini / Materi kurang jelas </small>
                            <small></small>
                        </div>


                        {{-- <input type="hidden" name="id_topik" value="{{ $id_topik }}" >
                        <input type="hidden" name="id_percobaan" value="{{ $id_percobaan }}" > --}}
                        <input type="hidden" name="id_topik" value="{{ $infotopik->id_topik }}" >
                        <input type="hidden" name="id_percobaan" value="{{ $percobaan->id_percobaan }}" >

                    </div>

                </div>

            </div>

            <div class="card-footer">

                <a href="{{ url('student/pythoncourse/python/pengerjaan/'. $id_percobaan) }}" class="btn btn-outline-info">Back</a>

                {{-- <input type="hidden" name="topic" value="{{$taskid}}" /> --}}

                {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}

            </div>

        </div>

        <!-- </form> -->

        </form>

    </div>

</div>

@endsection
