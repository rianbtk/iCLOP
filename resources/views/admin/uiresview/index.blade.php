@extends('admin/admin')

@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">List of Student UI Learning Submissions</h3>

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

                                    <th>Date/Time</th>

                                    <th>History</th>

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

                                    <td>{{ $date->format('Y-m-d H:i:s') }}</td>

                                    <td class="text-center">

                                        <div class="btn-group">

                                            <a class="btn  btn-warning " href="{{ URL::to('/admin/uiresview/'.$entity['userid'].'/'.$entity['uitopicid']) }}">

                                                <i class="fa fa-eye"></i>&nbsp;History</a>

                                        </div>

                                    </td>

                                    <td class="text-center">

                                        <div class="btn-group">

                                            <a class="btn  btn-info " href="{{ URL::to('/admin/uistudentdetail/'.$entity['userid'].'/'.$entity['id']) }}">

                                                <i class="fa fa-eye"></i>&nbsp;Detail</a>

                                        </div>

                                    </td>

                                    <td class="text-center">

                                        <div class="btn-group">

                                            <a class="btn  btn-danger " href="{{ URL::to('/admin/uiuploadsrc/'.$entity['userid'].'/'.$entity['uitopicid'].'/'.$entity['id']) }}">

                                                <i class="fa fa-eye"></i>&nbsp;Source</a>



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

        </div>

    </div>

</div>



@endsection
