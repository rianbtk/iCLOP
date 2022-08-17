@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exercise Validation Result</h3>
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
                                    <th>Exercise Name</th>
                                    <th>Duration</th>
                                    <th>Validation Detail</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($entities[0]['name']))

                                @foreach($entities as $entity)
                                <tr>
                                    <td>{{ $entity['name'] }}</td>
                                    <td>Total: {{ $entity['duration'] }} minute(s)</td>
                                    <td>{!! nl2br($entity['report']) !!}</td>
                                    <td>{{ $entity['checkstat'] }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="{{ URL::to('/student/exercisevalid/'.$entity['id']) }}"><i class="fa fa-eye">&nbsp;Show Detail</i></a>
                                        </div>
                                    </td>

                                </tr>
                                @endforeach

                                @else
                                <tr>
                                    <td colspan="6" class="text-center">No validation data yet</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
