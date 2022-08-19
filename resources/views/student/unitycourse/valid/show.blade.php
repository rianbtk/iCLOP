@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Validation Result Detail [Student name: {{ $student['name'] }}, Topic: {{ $topic['name'] }} ]</h3>
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
                                <th>Test File</th>
                                <th>Taks Name</th>
                                <th>Result</th>
                                <th>Report</th>
                                <th>Exec Time</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $index => $entity)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $entity['fileName'] }}</td>
                                <td>{{ $entity['taskno'].'. '.$entity['desc'] }}</td>
                                <td>{{ $entity['status'] }}</td>
                                <td>{!! nl2br(e($entity['report'])) !!}</td>
                                <td>{{ $entity['created_at'] }}</td>
                                <td>{{ $entity['duration'] }} seconds</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
        <div class="card-footer">
          <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
          <!--
            <a href="{{ URL::to('admin/topic') }}" class="btn btn-outline-info">Back</a>
          -->
        </div>
    </div>
</div>
</div>

@endsection
