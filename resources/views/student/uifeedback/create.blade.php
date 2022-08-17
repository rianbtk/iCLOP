@extends('student/home')

@section('content')

<div class="row">

    <div class="col-12">

        {{ Form::open(['route'=>'uifeedback.store', 'files'=>true]) }}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">Submit a Task Result</h3>

            </div>

            <div class="card-body">

                @if (Session::has('message'))

                <div id="alert-msg" class="alert alert-success alert-dismissible">

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>

                    {{ Session::get('message') }}

                </div>

                @endif



                @if(!empty($errors->all()))

                <div class="alert alert-danger">

                    {{ Html::ul($errors->all())}}

                </div>

                @endif



                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            {{ Form::label('duration', 'Duration (minutes)') }}

                            {{ Form::number('duration', (empty($result[0]) ? '' : $result[0]['duration']), ['class'=>'form-control', 'placeholder'=>'Duration Time in Minutes']) }}

                        </div>

                        <div class="form-group">

                            {{ Form::label('comment', 'Task Comment') }}

                            {{ Form::textarea('comment', (empty($result[0]) ? '' : $result[0]['comment']), ['class'=>'form-control', 'placeholder'=>'can you finish it?', 'rows'=>5]) }}

                        </div>



                    </div>

                </div>

            </div>

            <div class="card-footer">

                <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">

                <input type="hidden" name="topic" value="{{$taskid}}" />

                {{ Form::submit('Save', ['class' => 'btn btn-primary pull-right']) }}

            </div>

        </div>

        <!-- </form> -->

        {{ Form::close() }}

    </div>

</div>

@endsection
