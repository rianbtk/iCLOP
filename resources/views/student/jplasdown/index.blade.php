@extends('student/main')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Start Learning Java Programming with JPLAS</h3>
                <div class="card-tools">

             </div>
         </div>
         <div class="card-body">

            <div class="row">
                <div class="col-md-12">
		<a class="btn btn-success" href="{{ URL::to('/student/jplasdown/create/')}}">
			<i class="fa fa-plus"></i>&nbsp;Submit/Update a Result</a>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
				<th>No</th>
                                <th>Topic</th>
                                <th>Package File</th>
				<th>Guide File</th>
				<th>Result</th>
				<th>Comment</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $i => $entity)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
				<td>{{ $entity['name'] }}</td>
                                <td>
<div class="btn-group">
<a class="btn btn-info"
href="{{ URL::to('/download/jpack/'.str_replace('learning/','',$entity['packfile']).'/'.str_replace(' ','',$entity['name'])) }}">
<i class="fa fa-download"></i>&nbsp;Download</a>

</div>
				</td>
                                <td>
<div class="btn-group">
<a class="btn btn-danger" 
href="{{ URL::to('/download/jguide/'.str_replace('learning/','',$entity['docfile']).'/'.str_replace(' ','',$entity['name'])) }}">
<i class="fa fa-download"></i>&nbsp;Download</a>

</div>
				</td>
<td>{{ $entity['filename'] }}
</td>
<td>{{ $entity['comment'] }}
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

@endsection

