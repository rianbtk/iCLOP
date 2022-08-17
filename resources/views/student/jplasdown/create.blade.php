@extends('student/home')
@section('content')
    <div class="row">
        <div class="col-12">
            {{ Form::open(['route'=>'jplasdown.store', 'files'=>true]) }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Submit a JPLAS Result</h3>
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
                                <option value="{{ $item->id }}">{{ '['.$item->id.'] '.$item->name }}</option>
                                @endforeach
                                </select>
                                  <div class="form-group">
                                      {{ Form::label('image', 'Result File') }}
                                      {{ Form::file('image', ['class'=>'form-control']) }}
                                  </div>
                                <div class="form-group">
                                    {{ Form::label('comment', 'Comment') }}
                                    {{ Form::textarea('comment', '', ['class'=>'form-control', 'placeholder'=>'Task Comment', 'rows'=>5]) }}
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
