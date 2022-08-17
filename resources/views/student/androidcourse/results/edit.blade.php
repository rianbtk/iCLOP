@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($entity,['route'=>['results.update',$entity['id']], 'files'=>true,'method'=>'PUT']) }}
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
                        <div class="form-group">
                            <label for="data1">Task</label>
                            <input id="data1" type="text" value="{{ $task['taskno'].'. '.$task['desc'] }}" class="form-control" disabled />
                        </div>
                      </div>
                      <div class="form-group">
                          {{ Form::label('status', 'Status: ') }}
                          <div>{{ Form::radio('status', 'Passed' , $entity['status']=='Passed') }}
                          Passed &nbsp;&nbsp;
                          {{ Form::radio('status', 'Failed' , $entity['status']=='Failed') }}
                          Failed</div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('duration', 'Duration') }}
                            {{ Form::number('duration', $entity['duration'], ['class'=>'form-control', 'placeholder'=>'Duration Time in Minutes']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('image', 'Captured Image of Evidence (jpg,png). Left it blank if does not change') }}
                            {{ Form::file('image', ['class'=>'form-control']) }}
                        </div>
                      <div class="form-group">
                          {{ Form::label('comment', 'Task Comment') }}
                          {{ Form::textarea('comment', $entity['comment'], ['class'=>'form-control', 'placeholder'=>'Task Comment', 'rows'=>5]) }}
                      </div>

                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
                <input type="hidden" name="taskid" value="{{ $entity['taskid'] }}" />
                {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}
            </div>
        </div>
        <!-- </form> -->
        {{ Form::close() }}
    </div>
</div>
@endsection
