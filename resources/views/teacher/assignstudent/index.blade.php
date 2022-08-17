@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Validation of Student
                    
                </div>
                
                <div class="card-tools">
                    
             </div>
         </div>
         {{ Form::open(['route'=>'assignstudent.store', 'files'=>true]) }}
         <div class="card-body">
            @if (Session::has('message'))
            <div id="alert-msg" class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                {{ Session::get('message') }}
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th></th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td>{{ Form::checkbox('students[]',$entity['id'],null, array('id'=>'asap', 'class'=>'f')) }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['email'] }}</td>
                                <td>{{ $entity['roleid'] }}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>

            <div class="form-row"> 
                <div class="input-group">
                <div class="form-group">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-check"></i>Validate as Member of </button> 
                </div>
                <div class="form-group">
                    <div class="input-group-text">Class</div>
                  </div>
                <div class="form-group">
                    {!! Form::select('classroom', $classroom, null, ['class' => 'form-control']) !!}
                </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>

@endsection
