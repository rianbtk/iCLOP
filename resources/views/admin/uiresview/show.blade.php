@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">History of student work [Student name: {{ $student['name'] }}, Topic: {{ $topic['name'] }} ]</h3>
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
                                    <th>History No</th>
                                    <th>Student</th>
                                    <th>Topic</th>
                                    <th>Result</th>
                                    <th>Teacher</th>
                                    <th>Duration</th>
                                    <th>Date/Time</th>
                                    <th>Test Detail</th>
                                    <th>Submit Source</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $x = count($entities); ?>
                                @foreach($entities as $i => $entity)
                                <tr>
                                    <td class="text-center">{{ $x }}</td>
                                    <td>{{ $entity['student'] }}</td>
                                    <td>{{ $entity['topic'] }}</td>
                                    <td>{{ $entity['checkstat'] }}</td>
                                    <td>{{ $entity['teacher'] }}</td>
                                    <?php
                                    $date = new DateTime($entity['created_at'], new DateTimeZone('Europe/London'));
                                    $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                    ?>
                                    <td>00:50:22</td>
                                    <td>{{ $date->format('Y-m-d H:i:s') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn  btn-info " href="{{ URL::to('/admin/uistudentres/'.$entity['userid'].'/'.$entity['id']) }}">
                                                <i class="fa fa-eye"></i>&nbsp;Detail</a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn  btn-danger " href="{{ URL::to('/admin/uiuploadsrc/'.$entity['userid'].'/'.$topic['id'].'/'.$entity['id']) }}">
                                                <i class="fa fa-eye"></i>&nbsp; Source</a>
                                        </div>
                                    </td>


                                </tr>
                                <?php $x--; ?>

                                @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="button" value="Back" onclick="javascript:location.href='{{ URL::to('/admin/uiresview/') }}'" class="btn btn-outline-info">
                <!--
            <a href="{{ URL::to('admin/topic') }}" class="btn btn-outline-info">Back</a>
          -->
            </div>
        </div>
    </div>
</div>

@endsection
