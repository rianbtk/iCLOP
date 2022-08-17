@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Validation Result of Learning Result</h3>
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
                                <th>Topic Name</th>
                                <th>Submission Result</th>
                                <th>Duration</th>
                                <th>Validation Result</th>
                                <th>Validation Detail</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['passed'] }} Task(s) Passed,<br/>{{ $entity['failed'] }} Task(s) Failed</td>
                                <td>Total: {{ $entity['tot_duration'] }} minute(s),<br/>Average: {{ round($entity['avg_duration'],2) }}  minute(s)/task</td>
                                <td>
                                    @if ($entity['vpassed']=='')
                                      WAITING
                                    @else
                                      {{ $entity['vpassed'] }} Task(s) Passed,<br/>{{ $entity['vfailed'] }} Task(s) Failed
                                    @endif
                                </td>
                                <td>{!! nl2br($entity['checkresult']) !!}</td>
                                <td>{{ $entity['checkstat'] }}</td>
                                <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="{{ URL::to('/student/valid/'.$entity['id']) }}"><i class="fa fa-eye">&nbsp;Show Detail</i></a>
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
