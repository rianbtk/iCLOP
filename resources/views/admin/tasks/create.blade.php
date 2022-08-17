@extends('admin/admin')
@section('content')
    <div class="row">
        <div class="col-12">
            {{ Form::open(['route'=>'tasks.store', 'files'=>true]) }}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Learning Task</h3>
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
                                    {{ Form::label('taskno', 'Task No') }}
                                    {{ Form::number('taskno', '', ['class'=>'form-control', 'placeholder'=>'Task Number']) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('desc', 'Description') }}
                                    {{ Form::text('desc', '', ['class'=>'form-control', 'placeholder'=>'Task Description']) }}
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
