@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Student's Learning Completeness</h3>
                <div class="card-tools">

             </div>
         </div>
         <div class="card-body">

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
				<th>No</th>
                                <th>Student</th>
                                <th>Teacher</th>
				<th>#A1/A1X Passed</th>
				<th>#B1/B1X Passed</th>
				<th>#B2/B2X Passed</th>
				<th>Total</th>
				<th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $i => $entity)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['teachername'] }}</td>
				<td>{{ ($entity['level1']==0)?'-':$entity['level1'] }}</td>
                                <td>{{ ($entity['level2']==0)?'-':$entity['level2']  }}</td>
				<td>{{ ($entity['level3']==0)?'-':$entity['level3']  }}</td>
				<td color="lime"><b>{{ $entity['total'] }}</b></td>

				<td>-</td>

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

