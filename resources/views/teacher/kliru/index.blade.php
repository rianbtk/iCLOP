@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Student's Task Results Submission</h3>

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
              {!! Form::label('topic', 'Topic:') !!}
              {!! Form::select('topicList', $items , $filter, ['class' => 'form-control', 'id' => 'topicList', 'onchange' => 'this.form.submit();']) !!}
             {{ Form::close() }}
            <!--
              {!! Form::label('topic', 'Topic:') !!}
              {!! Form::select('topic', $items , null, ['class' => 'form-control', 'onchange' => 'doSomething(this)']) !!}
            -->
            </div>

            @php ($complete = true)
            <div class="row">

                <div class="col-md-12">
                  {!! Form::label('tit1', 'Result of Each Task:') !!}

                  @if ($valid=='0')
                   <a class="btn btn-success" href="{{ URL::to('/student/results/create/'.$filter)}}"><i class="fa fa-plus"></i>&nbsp;Submit a Task Result</a>
                   @endif
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>Task No.</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Evidence</th>
                                <th>Comment</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td class="text-center">{{ $entity['taskno'] }}</td>
                                <td>{{ $entity['desc'] }}</td>
                                @if ($valid != '0')
                                  @php ($complete = false)
                                  <td colspan="5" color="red"><b><i>Task result was already validated</i></b></td>
                                @elseif ($entity['status']=='')
                                  @php ($complete = false)
                                  <td colspan="5" color="red"><b><i>Not yet uploaded</i></b></td>
                                @else
                                <td>{{ $entity['status'] }}</td>
                                <td>{{ $entity['duration'] }} minutes</td>
                                <td class="text-center"><img src="{{ asset('storage/'.$entity['imgFile']) }}" width="120"/></td>
                                <td>{{ $entity['comment'] }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ URL::to('/student/results/'.$entity['id']) }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <input type="hidden" name="topic" value="{{ $filter }}" />
                                        <div class="btn-group">
                                            <!--<a class="btn btn-info" href="{{ URL::to('/student/results/'.$entity['id']) }}"><i class="fa fa-eye"></i></a>
                                            -->
                                            <a class="btn btn-success" href="{{ URL::to('/student/results/'.$entity['id'].'/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
            <div class="row">
            </div>
            <div class="row">

                <div class="col-md-12">
                  {!! Form::label('tit2', 'Learning Files Submission:') !!}
                  @if ($valid=='0')
                  <a class="btn btn-info" href="{{ URL::to('/student/lfiles/create/'.$filter)}}"><i class="fa fa-plus"></i>&nbsp;Submit a Learning File</a>
                  @endif
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>File Name</th>
                                <th>Folder Path</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lfiles as $index => $lfile)
                            <tr>
                                <td class="text-center">{{ $index +1 }}</td>
                                <td>{{ $lfile['fileName'] }}</td>
                                <td>{{ $lfile['path'] }}</td>
                                <td>{{ $lfile['desc'] }}</td>

                                @if ($valid!='0')
                                  @php ($complete = false)
                                  <td colspan="2" color="red"><b><i>Task result was already validated</i></b></td>
                                @elseif ($lfile['rscfile']=='')
                                  @php ($complete = false)
                                  <td colspan="2" color="red"><b><i>Not yet submitted</i></b></td>
                                @else
                                <td class="text-center">
                                  <span class="btn btn-warning"><i class="fa fa-check-circle"></i></span>
                                </td>

                                <td class="text-center">
                                    <form method="POST" action="{{ URL::to('/student/lfiles/'.$lfile['id']) }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <input type="hidden" name="topic" value="{{ $filter }}" />
                                        <div class="btn-group">
                                            <!--<a class="btn btn-info" href="{{ URL::to('/student/results/'.$entity['id']) }}"><i class="fa fa-eye"></i></a>
                                            -->
                                            <a class="btn btn-success" href="{{ URL::to('/student/lfiles/'.$lfile['id'].'/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                  @if ($valid=='0')
                    @if ($complete)
                    <a class="btn btn-danger" href="{{ URL::to('/student/lfiles/valid/'.$filter)}}"><i class="fa fa-check-square"></i>&nbsp;Validate This Learning</a>
                    @else
                    <span class="btn btn-block" ><i class="fa fa-frown"></i>&nbsp;Submission is Not Complete</a>
                    @endif
                  @else
                    <span class="btn btn-block" ><i class="fa fa-smile"></i>&nbsp;Validation is Success</a>
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>



@endsection
