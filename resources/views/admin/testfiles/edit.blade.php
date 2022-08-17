@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($data,['route'=>['testfiles.update',$data['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of Test File</h3>
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
                          <label for="name">File Id</label>
                          <input id="taskid" type="text" value="{{ $data['id'] }}" class="form-control" disabled />
                      </div>
                      <div class="form-group">
                        {!! Form::label('topic', 'Topic:') !!}
                        {!! Form::select('topic', $items, $data['topic'], ['class' => 'form-control']) !!}
                      </div>
                      <div class="form-group">
                          {{ Form::label('testno', 'Test No') }}
                          {{ Form::number('testno', $data['testno'], ['class'=>'form-control', 'placeholder'=>'Test Number']) }}
                      </div>
                      <div class="form-group">
                          {{ Form::label('image', 'Test File (*.java) [Left empty if is not updated]') }}
                          {{ Form::file('testFile', ['class'=>'form-control']) }}
                      </div>
                      <div class="form-group">
                          <label for="name">Original File Name</label>
                          <input id="fn" type="text" value="{{ $data['fileName'] }}" class="form-control" disabled />
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
