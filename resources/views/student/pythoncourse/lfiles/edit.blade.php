@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($data,['route'=>['results.update',$data['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of Task Result</h3>
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
                        {!! Form::label('taskid', 'Learning Task:') !!}
                        <select disabled class="form-control" id="taskid" name="taskid">
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $data['taskid'] ? 'selected' : '' }}>{{ '['.$item->name.'] '.$item->taskno.'. '.$item->desc }}</option>
                        @endforeach
                        </select>
                      </div>
                      <div class="form-group">
                          {{ Form::label('status', 'Status: ') }}
                          <div>{{ Form::radio('status', 'Passed' , $data['status']=='Passed') }}
                          Passed &nbsp;&nbsp;
                          {{ Form::radio('status', 'Failed' , $data['status']=='Failed') }}
                          Failed</div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('duration', 'Duration') }}
                            {{ Form::number('duration', $data['duration'], ['class'=>'form-control', 'placeholder'=>'Duration Time in Minutes']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('image', 'Captured Image of Evidence (jpg,png). Left it blank if does not change') }}
                            {{ Form::file('image', ['class'=>'form-control']) }}
                        </div>
                      <div class="form-group">
                          {{ Form::label('comment', 'Task Comment') }}
                          {{ Form::textarea('comment', $data['comment'], ['class'=>'form-control', 'placeholder'=>'Task Comment', 'rows'=>5]) }}
                      </div>

                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
                <input type="hidden" name="taskid" value="{{ $data['id'] }}" />
                {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}
            </div>
        </div>
        <!-- </form> -->
        {{ Form::close() }}
    </div>
</div>
@endsection
