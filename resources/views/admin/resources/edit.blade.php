@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($data,['route'=>['resources.update',$data['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of Topic Resource</h3>
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
                          <label for="name">Res. Id</label>
                          <input id="resid" type="text" value="{{ $data['id'] }}" class="form-control" disabled />
                      </div>
                      <div class="form-group">
                        {!! Form::label('topic', 'Topic:') !!}
                        {!! Form::select('topic', $items, $data['topic'], ['class' => 'form-control']) !!}
                      </div>
                        <div class="form-group">
                            {{ Form::label('filename', 'File Name') }}
                            {{ Form::text('filename', $data['fileName'], ['class'=>'form-control', 'placeholder'=>'Enter File Name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('path', 'Folder Path') }}
                            {{ Form::text('path', $data['path'], ['class'=>'form-control', 'placeholder'=>'Enter Folder Path']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('desc', 'Description') }}
                            {{ Form::textarea('desc', $data['desc'], ['class'=>'form-control', 'placeholder'=>'Enter description', 'rows'=>5]) }}
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
