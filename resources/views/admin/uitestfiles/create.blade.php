@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::open(['route'=>'uitestfiles.store', 'files'=>true]) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Test File</h3>
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
                            <select class="form-control" id="taskid" name="taskid">
                                @foreach($topics as $topic)
                                <option name="topic" value="{{ $topic['id'] }}">{{ $topic['id'].'. '.$topic['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            {{ Form::label('image', 'Test File (*.java)') }}
                            {{ Form::file('testFile', ['class'=>'form-control']) }}
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