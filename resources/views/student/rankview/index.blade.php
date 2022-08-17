@extends('student/main')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rank of Top 20 Students (Point: Passed=2, Failed=-1, Error=-2)</h3>
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
				<th>#PASSED</th>
				<th>#FAILED</th>
				<th>#ERROR</th>
				<th>Score</th>
				<th>Last Submit Time</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $i => $entity)
                            <tr>
                                <td class="text-center">{{ $i+1 }}</td>
                                <td>{{ $entity['student_name'] }}</td>
                                <td>{{ $entity['teacher_name'] }}</td>
				<td>{{ $entity['subs_passed'] }}</td>
                                <td>{{ $entity['subs_failed'] }}</td>
				<td>{{ $entity['subs_error'] }}</td>
				<td color="lime"><b>{{ $entity['score'] }}</b></td>

<?php
$date = new DateTime($entity['last_submit'], new DateTimeZone('Europe/London'));
$date->setTimezone(new DateTimeZone('Asia/Jakarta'));
?>
		 <td>{{ $date->format('Y-m-d H:i:s') }}</td>


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

