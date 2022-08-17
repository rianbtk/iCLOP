@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::open(['route'=>'uitopic.store', 'files'=>true]) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add UI Learning Topic</h3>
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
                            {{ Form::label('level', 'Topic Lavel') }}
                            {{ Form::text('level', '', ['class'=>'form-control', 'placeholder'=>'Enter Topic Lavel']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Topic Name') }}
                            {{ Form::text('name', '', ['class'=>'form-control', 'placeholder'=>'Enter Topic Name']) }}
                        </div>
			<div class="form-group">
                            {{ Form::label('note', 'Topic Note') }}
                            {{ Form::text('note', '', ['class'=>'form-control', 'placeholder'=>'Enter Topic Note']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('projectname', 'Project Name') }}
                            {{ Form::text('projectname', '', ['class'=>'form-control', 'placeholder'=>'Enter Android Project package name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('projectpath', 'Project Folder Path') }}
                            {{ Form::text('projectpath', '', ['class'=>'form-control', 'placeholder'=>'/example/project']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        {{ Form::label('packname', 'Package Name File') }}
                        {{ Form::text('packname', '', ['class'=>'form-control', 'placeholder'=>'Enter package name (ex: org.aplas.example)', 'rows'=>5]) }}
                    </div>
                    <div class="col-12">
                        {{ Form::label('description', 'Description') }}
                        {{ Form::textarea('description', '', ['class'=>'form-control', 'placeholder'=>'Enter description', 'rows'=>5]) }}
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
                            {{ Form::text('status', '', ['class'=>'form-control', 'placeholder'=>'Status (0 or 1)', 'rows'=>5]) }}
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
