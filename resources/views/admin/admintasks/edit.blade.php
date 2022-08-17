@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($data,['route'=>['tasks.update',$data['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of Learning Task</h3>
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
                          <label for="name">Task Id</label>
                          <input id="taskid" type="text" value="{{ $data['id'] }}" class="form-control" disabled />
                      </div>
                      <div class="form-group">
                        {!! Form::label('topic', 'Topic:') !!}
                        {!! Form::select('topic', $items, $data['topic'], ['class' => 'form-control']) !!}
                      </div>
                        <div class="form-group">
                            {{ Form::label('taskno', 'Task Number') }}
                            {{ Form::text('taskno', $data['taskno'], ['class'=>'form-control', 'placeholder'=>'Enter Topic Name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('desc', 'Description') }}
                            {{ Form::text('desc', $data['desc'], ['class'=>'form-control', 'placeholder'=>'Enter Learning Stage']) }}
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
