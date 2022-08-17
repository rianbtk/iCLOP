@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::open(['url'=>'/admin/python/proseseditpercobaan/'. $percobaan->id_percobaan, 'files'=>true]) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Percobaan</h3>
            </div>
            <div class="card-body">
                @if(!empty($errors->all()))
                <div class="alert alert-danger">
                    {{ Html::ul($errors->all())}}
                </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for=""> Nama Topik</label>
                            <select name="id_topik" class="form-control" id="" required="">
                                <option value="">-- Pilih salah satu --</option>
                                
                                @foreach ( $dt_topik AS $isi )

                                @php 

                                    $pilihan = "";
                                    if ( $percobaan->id_topik == $isi->id_topik ){

                                        $pilihan = 'selected="selected"';
                                    }
                                @endphp
                                
                                <option value="{{ $isi->id_topik }}" {{ $pilihan }}>{{ $isi->nama }}</option>

                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            {{ Form::label('no_percobaan', 'Nomor Percobaan') }}
                            {{ Form::text('no_percobaan', $percobaan->no_percobaan, ['class'=>'form-control', 'placeholder'=>'Masukkan Nomor Percobaan']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('nama_percobaan', 'Nama Percobaan') }}
                            {{ Form::text('nama_percobaan', $percobaan->nama_percobaan, ['class'=>'form-control', 'placeholder'=>'Masukkan Nama Percobaan']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('catatan', 'Catatan') }}
                            {{ Form::text('catatan', $percobaan->catatan, ['class'=>'form-control', 'placeholder'=>'Masukkan Catatan']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('panduanpath', 'Panduan Path') }}
                            {{ Form::text('panduanpath', $percobaan->panduanpath, ['class'=>'form-control', 'placeholder'=>'/example/panduan']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">File Test</label>
                            <input type="file" name="filetest" class="form-control" placeholder=".py">
                        </div>
                        <div class="form-group">
                            {{ Form::label('deskripsi', 'Deskripsi') }}
                            {{ Form::textarea('deskripsi', $percobaan->deskripsi, ['class'=>'form-control', 'placeholder'=>'Masukkan Deskripsi']) }}
                        </div>
                        <div class="form-group">
                            <label for="">Text Editor</label>
                            <textarea name="texteditor" class="form-control" placeholder="Masukkan sintaks editor . . ." id="" cols="30" rows="10">{{ $percobaan->texteditor }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
                {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}
            </div>
        </div>
        <!-- </form> -->
        {{ Form::close() }}
    </div>
</div>
@endsection
