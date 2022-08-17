@extends('admin/admin')
@section('content')
    <div class="row">
        <div class="col-12">
            {{ Form::open(['route'=>'resources.store', 'files'=>true]) }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Topic Resource</h3>
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
                                {!! Form::label('topic', 'Topic:') !!}
                                {!! Form::select('topic', $items, null, ['class' => 'form-control']) !!}
                              </div>
                                <div class="form-group">
                                    {{ Form::label('filename', 'File Name') }}
                                    {{ Form::text('filename', '', ['class'=>'form-control', 'placeholder'=>'File Name']) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('path', 'Folder Path') }}
                                    {{ Form::text('path', '', ['class'=>'form-control', 'placeholder'=>'Folder Path']) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('desc', 'Description') }}
                                    {{ Form::textarea('desc', '', ['class'=>'form-control', 'placeholder'=>'Enter description', 'rows'=>5]) }}
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
