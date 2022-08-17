@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assign Student as Member</h3>
                <div class="card-tools">

             </div>
         </div>
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
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['email'] }}</td>
                                <td>{{ $entity['roleid'] }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                      <a class="btn btn-success" href="{{ URL::to('/teacher/assignstudent/'.$entity['id'].'/edit') }}"><i class="fa fa-plus"></i>&nbsp;Assign this Student</a>
                                    </div>
                                </td>
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
