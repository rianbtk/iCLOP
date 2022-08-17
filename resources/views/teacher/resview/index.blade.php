@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List of Student Submission</h3>
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
                                <th>Topic</th>
				<th>Result</th>
				<th>Teacher</th>
				<th>Exec Time</th>
				<th>Date/Time</th>
				<th>Test Detail</th>
				<th>Upload Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $i => $entity)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
                                <td>{{ $entity['student'] }}</td>
                                <td>{{ $entity['topic'] }}</td>
				<td>{{ $entity['checkstat'] }}</td>
                                <td>{{ $entity['teacher'] }}</td>
				<td>{{ $entity['validation_time'] }} seconds</td>
<?php
$date = new DateTime($entity['created_at'], new DateTimeZone('Europe/London'));
$date->setTimezone(new DateTimeZone('Asia/Jakarta'));
?>
				<td>{{ $date->format('Y-m-d H:i:s') }}</td>
				<td class="text-center">
                                        <div class="btn-group">
       	<a class="btn  btn-info " href="{{ URL::to('/admin/studentres/'.$entity['userid'].'/'.$entity['topicid']) }}">
	<i class="fa fa-eye"></i>&nbsp;Detail</a>
                                        </div>
                                </td>
				<td class="text-center">
                                        <div class="btn-group">
	<a class="btn  btn-danger " href="{{ URL::to('/admin/uploadsrc/'.$entity['userid'].'/'.$entity['topicid']) }}">
        <i class="fa fa-eye"></i>&nbsp;Source</a>
        
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

