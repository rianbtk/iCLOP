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
                                    <th>Validation Detail</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($entities[0]['name']))

                                @foreach($entities as $entity)
                                <tr>
                                    <td><b>{{ $entity['name'] }}</b></td>
                                    <td>{{ $entity['checkstat'] }}</td>
                                    <td><b>{{ $entity['checkresult'] }}</b></td>
                                </tr>
                                @endforeach

                                @else
                                <tr>
                                    <td colspan="3" class="text-center">No validation data yet</td>
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
