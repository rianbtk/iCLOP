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
                                <th>Student / Teacher</th>
                                <th>Topic</th>
				<th>Result</th>
				 <th>View Result Detail</th>
                                <th>View Source Code</th>
				<th>Exec Time</th>
				<th>Date/Time</th>
				<th>Report</th>
                            </tr>
                        </thead>
                        <tbody>
			<?php $x = count($entities); ?>
                            @foreach($entities as $i => $entity)
                            <tr>
                                <td class="text-center">{{ $x }}</td>
                                <td>{{ $entity['student'].' [Teacher: '.$entity['teacher'].']' }}</td>
<?php
$way = (strlen($entity['githublink'])>5)?'by GitHub Link':((strlen($entity['projectfile'])>5)?'by Zipped Project':'by Multi Files');
?>
                                <td>{{ $entity['topic'] }}</td>
				<td>{{ $entity['checkstat'].' - '.$way  }}</td>
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

				<td>{{ $entity['validation_time'] }} sec.</td>
<?php
$date = new DateTime($entity['created_at'], new DateTimeZone('Europe/London'));
$date->setTimezone(new DateTimeZone('Asia/Jakarta'));
?>
				<td>{{ $date->format('Y-m-d H:i:s') }}</td>
				<td style="font-size:8pt">{{ ($entity['checkstat']=='ERROR')?$entity['checkresult']:$entity['validator'] }}</td>

                            </tr>
				<?php $x--; ?>
				
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

