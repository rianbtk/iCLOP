@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::model($classroom,['route'=>['crooms.update',$classroom['id']], 'files'=>true,'method'=>'PUT']) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Data of A Classroom</h3>
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
                          <label for="name">Class Id</label>
                          <input id="classid" type="text" value="{{ $classroom['id'] }}" class="form-control" disabled />
                      </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Name') }}
                            {{ Form::text('name', $classroom['name'], ['class'=>'form-control', 'placeholder'=>'Enter Topic Name']) }}
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ Form::label('desc', 'Description') }}
                        {{ Form::textarea('desc', $classroom['desc'], ['class'=>'form-control', 'placeholder'=>'Enter description', 'rows'=>5]) }}
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
