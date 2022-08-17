@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($topic,['route'=>['exerciseconf.update',$topic['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of Exercise</h3>
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
                            <label for="name">Exercise Id</label>
                            <input id="topicid" type="text" value="{{ $topic['id'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Exercise Name') }}
                            {{ Form::text('name', $topic['name'], ['class'=>'form-control', 'placeholder'=>'Enter Topic Name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('stage', 'Exercise Stage (ex: A - User Interface)') }}
                            {{ Form::text('stage', $topic['stage'], ['class'=>'form-control', 'placeholder'=>'Enter Learning Stage']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('packname', 'Android Package (ex: org.aplas.basicapp)') }}
                            {{ Form::text('packname', $topic['packname'], ['class'=>'form-control', 'placeholder'=>'Enter Android Project package name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('projectpath', 'Project Folder Path (ex: A1\BasicApp)') }}
                            {{ Form::text('projectpath', $topic['projectpath'], ['class'=>'form-control', 'placeholder'=>'Enter Project Folder Path']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('androidclass', 'Android Class (ex: AndroidX)') }}
                            {{ Form::text('androidclass', $topic['androidclass'], ['class'=>'form-control', 'placeholder'=>'Enter Android test folder path']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('testpath', 'Test Folder Path (ex: app\src\test\java\org\aplas\basicapp)') }}
                            {{ Form::text('testpath', $topic['androidclass'], ['class'=>'form-control', 'placeholder'=>'Enter Android test folder path']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('testcode', 'Test Code File') }}
                            {{ Form::file('testcode', ['class'=>'form-control']) }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ Form::label('desc', 'Description') }}
                        {{ Form::textarea('desc', $topic['desc'], ['class'=>'form-control', 'placeholder'=>'Enter description', 'rows'=>5]) }}
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
