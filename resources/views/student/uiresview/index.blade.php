@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List of UI Learning Result</h3>
                <div class="card-tools">

                </div>
            </div>
            <div class="card-body">
		<div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th></th>
                                    <th colspan={{$topiccount}}>UI Learning Topic</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-bold">Status</td>
                                    <?php $number = 1; ?>
                                    @foreach($entitiesresult as $result)
                                    @if ($result->{'checkstatnow'} == "passed")
                                    <td class="text-center bg-success" title="PASSED"><?= $number ?></td>
                                    @elseif(($result->{'checkstatnow'} == "failed") or ($result->{'checkstatnow'} == "error"))
                                    <td class="text-center bg-danger" title="FAILED"><?= $number ?> </td>
                                    @elseif ((strpos($result->{'checkstatnow'},'in process') !== false) or (strpos($result->{'checkstatnow'},'assigned') !== false) or ($result->{'checkstatnow'}=='waiting'))
                                    <td class="text-center bg-warning" title="VALIDATING"><?= $number ?></td>
                                    @else
                                    <td class="text-center bg-secondary" title="You haven't submitted"><?= $number ?></td>
                                    @endif
                                    <?php $number++ ?>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <p></p>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Topic</th>
                                    <th>Result</th>
                                    <th>Teacher</th>
                                    <th>Date/Time</th>
                                    <th>Test Detail</th>
                                    <th>Submitted Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $x = count($entities); ?>
                                @foreach($entities as $i => $entity)
                                <tr>
                                    <td class="text-center">{{ $x }}</td>
                                    <td>[<b>Topic {{ $entity['uitopicid'] }}</b>] {{ $entity['topic'] }}</td>
                                    <td>{{ $entity['checkstat'] }}</td>
                                    <td>{{ $entity['teacher'] }}</td>
                                    <?php
                                    $date = new DateTime($entity['created_at'], new DateTimeZone('Europe/London'));
                                    $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                    ?>
                                    <td>{{ $date->format('Y-m-d H:i:s') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn  btn-info " href="{{ URL::to('/student/uistudentres/'.$entity['id']) }}">
                                                <i class="fa fa-eye"></i>&nbsp;Detail</a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn  btn-danger " href="{{ URL::to('/student/uiuploadsrc/'.$entity['uitopicid'].'/'.$entity['id']) }}">
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
        </div>
    </div>
</div>

@endsection
