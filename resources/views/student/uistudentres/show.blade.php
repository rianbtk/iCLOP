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
                                    <th>Test File</th>
                                    <th>Topic Name</th>
                                    <th>Result</th>
                                    <th>Report</th>
                                    <th>Exec Time</th>
                                    <th>Submitted Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($testfile[0]['filename']))
                                <?php $x = count($entities); ?>
                                @foreach($entities as $index => $entity)
                                <tr>
                                    <td>{{ $testfile[0]['filename'] }}</td>
                                    <td>{{ $entity['topic'] }}</td>
                                    <td>{{ $entity['checkstat'] }}</td>
                                    <td>{!! nl2br(e($entity['report'])) !!}</td>
                                    <td>{{ $entity['created_at'] }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn  btn-danger " href="{{ URL::to('/student/uiuploadsrc/'.$entity['uitopicid'].'/'.$entity['id']) }}">
                                                <i class="fa fa-eye"></i>&nbsp; Source</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7" class="text-center">waiting for validation detail..</td>
                                </tr>
                                @endif
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
