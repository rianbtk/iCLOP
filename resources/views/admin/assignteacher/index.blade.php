@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assign User as Teacher</h3>
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
                                <th>No.</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
				<th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $index => $entity)
                            <tr>
                                <td class="text-center">{{ $index+1 }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['email'] }}</td>
                                <td>{{ $entity['roleid'] }}</td>
				<td>{{ $entity['teacher'] }}</td>
                                <td>{{ $entity['status'] }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                      <a class="btn btn-success" href="{{ URL::to('/admin/assignteacher/'.$entity['id'].'/edit') }}"><i class="fa fa-plus"></i>&nbsp;Assign as Teacher</a>
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
