@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        {{ Form::open(['route'=>'resetpassword.store', 'files'=>true]) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reset User Password</h3>
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
                            {{ Form::label('email', 'Email') }}
                            {{ Form::email('email', '', ['class'=>'form-control', 'placeholder'=>'Enter Email']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('newpassword', 'New Password') }}
                            {{ Form::text('newpassword', '', ['class'=>'form-control', 'placeholder'=>'Enter New Password']) }}
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
