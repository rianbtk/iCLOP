@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($uitopic,['route'=>['uitopic.update',$uitopic['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of Learning Topic</h3>
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
                            <label for="name">UiTopic Id</label>
                            <input id="topicid" type="text" value="{{ $uitopic['id'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            {{ Form::label('level', 'Topic Lavel') }}
                            {{ Form::text('level', $uitopic['level'], ['class'=>'form-control', 'placeholder'=> $uitopic['level'] ]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Topic Name') }}
                            {{ Form::text('name', $uitopic['name'], ['class'=>'form-control', 'placeholder'=> $uitopic['name'] ]) }}
                        </div>
			<div class="form-group">
                            {{ Form::label('note', 'Topic Note') }}
                            {{ Form::textarea('note', $uitopic['note'], ['class'=>'form-control', 'rows'=>5]) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('projectname', 'Project Name') }}
                            {{ Form::text('projectname', $uitopic['projectname'], ['class'=>'form-control', 'placeholder'=> $uitopic['projectname'] ]) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('projectpath', 'Project Folder Path') }}
                            {{ Form::text('projectpath', $uitopic['projectpath'], ['class'=>'form-control', 'placeholder'=> $uitopic['projectpath'] ]) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        {{ Form::label('packname', 'Package Name File') }}
                        {{ Form::text('packname', $uitopic['packname'], ['class'=>'form-control', 'placeholder'=>$uitopic['packname'] ]) }}
                    </div>
                    <div class="col-12">
                        {{ Form::label('description', 'Description') }}
                        {{ Form::textarea('description', $uitopic['description'], ['class'=>'form-control', 'placeholder'=>$uitopic['description'], 'rows'=>5]) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('guideFile', 'Guide File (*.pdf)') }}
                            {{ Form::file('guideFile', ['class'=>'form-control']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('status', 'Status') }}
                            {{ Form::text('status', $uitopic['status'], ['class'=>'form-control', 'placeholder'=> $uitopic['status'] ]) }}
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
