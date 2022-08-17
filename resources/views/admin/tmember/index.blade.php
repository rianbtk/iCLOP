@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Teacher Classroom Member</h3>
                <div class="card-tools">
                 <a href="{{ URL::to('/admin/resources/create')}}" class="btn btn-tool">
                     <i class="fa fa-plus"></i>
                     &nbsp; Add
                 </a>
             </div>
         </div>
         <div class="card-body">
            @if (Session::has('message'))
            <div id="alert-msg" class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                {{ Session::get('message') }}
            </div>
            @endif
             {{ Form::open(['method' => 'GET']) }}
            <div class="form-group">
              <label>Select Teacher</label>
              {!! '' !!}
              {!! Form::select('tchList', $items , $filter, ['class' => 'form-control', 'id' => 'topicList', 'onchange' => 'this.form.submit();']) !!}
             {{ Form::close() }}
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Classname</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $index => $entity)
                            <tr>
                                <td class="text-center">{{ $index +1 }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['email'] }}</td>
                                <td>{{ $entity['classname'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>
</div>



@endsection
