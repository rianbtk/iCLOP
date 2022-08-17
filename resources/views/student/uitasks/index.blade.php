@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-header">
            <h3 class="card-title">Start Learning Andoird UI with APLAS</h3>
        </div>
        <div class="card-body">
            @if (Session::has('message'))
            <div id="alert-msg" class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                {{ Session::get('message') }}
            </div>
            @endif

	    @if (Session::has('alert'))
            <div id="alert-msg" class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                {{ Session::get('alert') }}
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Topic Name</th>
                                <th>Description</th>
                                <th>Solve</th>
                            </tr>
                        </thead>
                        <tbody>
			    <?php $i = 1 ?>
                            @foreach($entities as $entity)
                            <tr>
                                <td class="text-center"><?php echo $i ?></td>
                                <td>{{ $entity->{'name'} }}</td>
                                <td>{{ $entity->{'description'} }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
					@if ($entity->{'checkstat'})

                                        <a class="btn btn-outline-warning" href="{{ URL::to('/student/uitasks/'.$entity->{'id'}) }}"><i class="fa fa-check"></i></a>

                                        @else

                                        <a class="btn btn-success" href="{{ URL::to('/student/uitasks/'.$entity->{'id'}) }}"><i class="fa fa-hand-pointer"></i></a>

                                        @endif 

                                    </div>
                                </td>
                            </tr>
			    <?php $i++ ?>
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
