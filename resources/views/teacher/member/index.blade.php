@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List of Member Student</h3>
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
				 <th>No</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Topic Submitted</th>
                                <th>Topic Name(s)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $i => $entity)
                            <tr>
				<td class="text-center">{{ $i+1 }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['email'] }}</td>
                                <td>{{ ($entity['count']=='')?0:$entity['count'] }} topic(s)</td>
                                <td>{{ ($entity['topicname']=='')?'-':$entity['topicname'] }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                      @if ($entity['count']=='')
                                        <form method="POST" action="{{ URL::to('/teacher/member/'.$entity['id']) }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE" />
                                            <div class="btn-group">
                                                <button type="submit" class="btn btn-danger"><i class="fa fa-trash">&nbsp;</i>Delete this Student</button>
                                            </div>
                                        </form>
                                      @else
                                        <a class="btn btn-success" href="{{ URL::to('/teacher/member/'.$entity['id'].'/edit') }}"><i class="fa fa-check-circle"></i>&nbsp;Show Student Result</a>
                                      @endif
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
