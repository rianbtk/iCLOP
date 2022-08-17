@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Teacher's Classrooms</h3>
                <div class="card-tools">
                 <a href="{{ URL::to('/teacher/crooms/create')}}" class="btn btn-tool">
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
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Class Name</th>
                                <th>Description</th>
                                <th colspan="2">#Member</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td class="text-center">{{ $entity['id'] }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['desc'] }}</td>
                                <td class="text-center">{{ ($entity['count']=='')?0:$entity['count'] }} student(s)</td>
                                <td class="text-center"><div class="btn-group">
                                    <a class="btn  btn-info " href="{{ URL::to('/teacher/crooms/'.$entity['id']) }}"><i class="fa fa-eye"></i>&nbsp;Show Members</a>
                                </div></td>
                                <td>{{ $entity['status'] }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ URL::to('/teacher/crooms/'.$entity['id']) }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <div class="btn-group">
                                            <a class="btn btn-success" href="{{ URL::to('/teacher/crooms/'.$entity['id'].'/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </form>
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
