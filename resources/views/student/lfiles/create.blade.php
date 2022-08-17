@extends('student/home')
@section('content')
    <div class="row">
        <div class="col-12">
            {{ Form::open(['route'=>'lfiles.store', 'files'=>true]) }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Submit a Learning Result File</h3>
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
                                  <label for="data1">Topic</label>
                                  <input id="data1" type="text" value="{{ $topic['name'] }}" class="form-control" disabled />
                              </div>
                              <div class="form-group">
                                {!! Form::label('fileid', 'File Name:') !!}
                                <select class="form-control" id="fileid" name="fileid">
                                @foreach($files as $file)
                                <option value="{{ $file->id }}" {{ $file->id == 0 ? 'selected' : '' }}>{{ $file->fileName.' >>> '.$file->path.'' }}</option>
                                @endforeach
                                </select>
                              </div>
                              <div class="form-group">
                                      {{ Form::label('rscfile', 'File:') }}
                                      {{ Form::file('rscfile', ['class'=>'form-control']) }}
                                  </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="topic" value="{{ $topic['id'] }}" />
                        <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
                        {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}
                    </div>
                </div>
            <!-- </form> -->
            {{ Form::close() }}
        </div>
    </div>
@endsection
