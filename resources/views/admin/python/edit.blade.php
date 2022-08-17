@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::open(['url'=>'admin/python/prosesedittopik/' . $topik->id_topik, 'files'=>true]) }}
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
                            {{ Form::label('bab', 'Bab') }}
                            {{ Form::text('bab', $topik->bab, ['class'=>'form-control', 'placeholder'=>'Masukkan Bab']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('nama', 'Nama Topik') }}
                            {{ Form::text('nama', $topik->nama, ['class'=>'form-control', 'placeholder'=>'Masukkan Nama Topik']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('deskripsi', 'Deskripsi') }}
                            {{ Form::textarea('deskripsi', $topik->deskripsi, ['class'=>'form-control', 'placeholder'=>'Masukkan Deskripsi', 'rows'=>5]) }}
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('panduanpath', 'Panduan Path') }}
                            {{ Form::text('panduanpath', $topik->panduanpath, ['class'=>'form-control', 'placeholder'=>'/example/panduan']) }}
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('status', 'Status') }}
                            {{ Form::text('status', $topik->status, ['class'=>'form-control', 'placeholder'=>'Status (0 or 1)']) }}
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