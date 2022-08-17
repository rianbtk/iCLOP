@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Android UI Classroom Result</h3>
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
                {{ Form::open(['method' => 'GET']) }}
                <div class="form-group">
                    {!! Form::label('student', 'Classroom:') !!}
                    <select class="form-control" id="stdList" name="stdList" onChange='this.form.submit();'>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $filter ? 'selected' : '' }}>{{ '[ '.$item->classname.' / '.$item->name.' ] ' }}</option>
                        @endforeach
                    </select>
                    {{ Form::close() }}
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>Student Name</th>
                                        <th>Passed</th>
                                        <th>Topics Total</th>
                                        <th>Passed Topic List</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($number=1)
                                    @foreach($entities as $entity)
                                    <tr>
                                        <td>{{ $number }} . {{ $entity->{'name'} }}</td>
                                        <td>{{ ($entity->{'passednumber'} == '') ? 0 : $entity->{'passednumber'} }} Topic(s) </td>
                                        <td>{{ $topiccount }} Topics</td>
                                        <td>{!! (nl2br($entity->{'topiclist'}) == '') ? '-' : (nl2br($entity->{'topiclist'})) !!}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a class="btn  btn-success " href="{{ URL::to('/teacher/uisummaryres?stdList='.$entity->{'userid'}) }}"><i class="fa fa-eye"></i>&nbsp;Show UI Student Result</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @php($number++)
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
