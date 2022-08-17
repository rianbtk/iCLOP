@extends('student/home')
@section('content')
    <div class="row">
        <div class="col-12">
            {{ Form::open(['route'=>'results.store', 'files'=>true]) }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Submit a Task Result</h3>
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
                                <select class="form-control" id="taskid" name="taskid">
                                @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ $item->id == 0 ? 'selected' : '' }}>{{ '['.$item->name.'] '.$item->taskno.'. '.$item->desc }}</option>
                                @endforeach
                                </select>
                              </div>
                                <div class="form-group">
                                    {{ Form::label('status', 'Status: ') }}
                                    <div>{{ Form::radio('status', 'Passed' , true) }}
                                    Passed &nbsp;&nbsp;
                                    {{ Form::radio('status', 'Failed' , false) }}
                                    Failed</div>
                                  </div>
                                  <div class="form-group">
                                      {{ Form::label('duration', 'Duration') }}
                                      {{ Form::number('duration', '0', ['class'=>'form-control', 'placeholder'=>'Duration Time in Minutes']) }}
                                  </div>
                                  <div class="form-group">
                                      {{ Form::label('image', 'Captured Image of Evidence (jpg,png)') }}
                                      {{ Form::file('image', ['class'=>'form-control']) }}
                                  </div>
                                <div class="form-group">
                                    {{ Form::label('comment', 'Task Comment') }}
                                    {{ Form::textarea('comment', '', ['class'=>'form-control', 'placeholder'=>'Task Comment', 'rows'=>5]) }}
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
                        <input type="hidden" name="topic" value="{{ $topic['id'] }}" />
                        {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}
                    </div>
                </div>
            <!-- </form> -->
            {{ Form::close() }}
        </div>
    </div>
@endsection
