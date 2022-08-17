@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exercise Submission</h3>

            </div>
            <div class="card-body">
                @if (Session::has('message'))
                <div id="alert-msg" class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                    {{ Session::get('message') }}
                </div>
                @endif

                @if (!empty($itemsstage))
                {{ Form::open(['method' => 'GET']) }}
                <div class="form-group">
                    {!! Form::label('stage', 'Select Exercise Stage:') !!}
                    {!! Form::select('stageList', $itemsstage , $filter, ['class' => 'form-control', 'id' => 'stageList', 'onchange' => 'this.form.submit();']) !!}
                    <p></p>

                    {!! Form::label('exercise', 'Select Exercise:') !!}
                    {!! Form::select('exerciseList', $itemsexercise , $filterexercise, ['class' => 'form-control', 'id' => 'exerciseList', 'onchange' => 'this.form.submit();']) !!}
                    {{ Form::close() }}
                </div>

                {{ Form::open(['method' => 'GET']) }}
                <div class="form-group">

                    {{ Form::close() }}
                </div>

                @php ($complete = true)
                {{ Form::open(['route'=>'exercisesubmission.store', 'files'=>true]) }}
                <input type="hidden" name="action" value="validate" />
                <input type="hidden" name="stage" value="{{ $filter }}" />
                <input type="hidden" name="exercise" value="{{ $filterexercise }}" />

                <p></p>

                <div class="row">
                    {!! Form::label('titw2', 'Assignment Answer Submission') !!}
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {{ Form::radio('option', 'github' ,  $option=='github',  ['onchange' => 'this.form.submit();']) }}
                        {!! Form::label('tit55', "by GitHub Link: ") !!}
                        <br />
                        @if (($completed=='0') && ($option=='github'))
                        <p><b>The link must be <font color='RED'>PUBLIC</font> access. Format : https://github.com/{{ '<username>' }}/project-name</b></p>
                        {{ Form::text('githublink','https://github.com/', ['class'=>'form-control']) }}

                        <p></p>

                        <div class="form-group">
                            {{ Form::label('duration', 'Duration (Minutes)') }}
                            {{ Form::number('duration', '0', ['class'=>'form-control', 'placeholder'=>'Duration Time in Minutes']) }}
                        </div>

                        <p></p>

                        <div class="form-group">
                            {{ Form::label('comment', 'Exercise Comment') }}
                            {{ Form::textarea('comment', '', ['class'=>'form-control', 'placeholder'=>'Task Comment', 'rows'=>5]) }}
                        </div>
                        @else
                        @endif
                    </div>
                </div>

                <p></p>

                <div class="row">
                    <div class="col-md-12">
                        {{ Form::radio('option', 'zipfile' ,  $option=='zipfile',  ['onchange' => 'this.form.submit();']) }}
                        {!! Form::label('tit55', "by Zip File of Android Project Folder: ") !!}
                        <br />
                        @if (($completed=='0') && ($option=='zipfile'))
                        <p></p>
                        <font color='RED'>File must have ZIP extension (*.zip), don't submit RAR file.</font>
                        <p></p>
                        {{ Form::file('zipfile', ['class'=>'form-control']) }}

                        <p></p>

                        <div class="form-group">
                            {{ Form::label('duration', 'Duration (Minutes)') }}
                            {{ Form::number('duration', '0', ['class'=>'form-control', 'placeholder'=>'Duration Time in Minutes']) }}
                        </div>

                        <p></p>

                        <div class="form-group">
                            {{ Form::label('comment', 'Exercise Comment') }}
                            {{ Form::textarea('comment', '', ['class'=>'form-control', 'placeholder'=>'Task Comment', 'rows'=>5]) }}
                        </div>
                        @else
                        @endif
                    </div>
                </div>

                <p></p>

                <div class="col-md-12">
                    @if (!@$completed)
                    {{ Form::submit('Submit this Exercise', ['class' => 'btn btn-danger', 'name' => 'submitbutton']) }}
                    <span class="btn btn-block"><i class="fa fa-frown"></i>&nbsp;You haven't submitted an answer for <b>{{$topic[0]['name']}}</b></a>
                        @else
                        <span class="btn btn-block"><i class="fa fa-smile"></i>&nbsp;You have submitted an answer for <b>{{$topic[0]['name']}}</b></a>
                            @endif
                </div>
                {{ Form::close() }}
                @else
                <td><b>Exercise is not available at this time</b></td>
                @endif
            </div>

            <div class="row"><br /></div>


        </div>
    </div>
</div>
</div>



@endsection
