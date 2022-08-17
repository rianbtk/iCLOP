@extends('student/home')
@section('content')
<div class="row">
    <div class="col-12">
      <div class="card-header">
          <h3 class="card-title">Start Learning Android Programming with APLAS</h3>
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
              {!! Form::label('topic', 'Learning Topic:') !!}
              {!! Form::select('topicList', $itemslearning , $filter, ['class' => 'form-control', 'id' => 'topicList', 'onchange' => 'this.form.submit();']) !!}
              <div class="form-group">
                  <label for="description">Description</label>
                  <textarea id="desc" class="form-control" disabled rows="2">{{ $topic['desc'] }}</textarea>
              </div>
             {{ Form::close() }}
            <!--
              {!! Form::label('topic', 'Topic:') !!}
              {!! Form::select('topic', $itemslearning , null, ['class' => 'form-control', 'onchange' => 'doSomething(this)']) !!}
            -->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                              <th></th>
                                <th>Guide Documents</th>
                                <th>Test Files</th>
                                <th>Supplement Files</th>
                                <th>Other Files</th>
                            </tr>
                        </thead>
                        <tbody>

                          <tr>
                              <td>Resource for <b>{{ $topic['name'] }}</b></td>
                              <td class="text-center">
                                @if($topic['guide'] !='')
                                  <div class="btn-group">
                                    <a class="btn btn-success" href="{{ URL::to('/download/guide/'.str_replace('learning/','',$topic['guide']).'/'.str_replace(' ','',$topic['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                  </div>
                                  @else
                                      Empty
                                  @endif
                              </td>
                              <td class="text-center">
                                @if($topic['testfile'] !='')
                                  <div class="btn-group">
                                    <a class="btn btn-warning" href="{{ URL::to('/download/test/'.str_replace('learning/','',$topic['testfile']).'/'.str_replace(' ','',$topic['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                  </div>
                                  @else
                                      Empty
                                  @endif
                              </td>
                              <td class="text-center">
                                @if($topic['supplement'] !='')
                                  <div class="btn-group">
                                    <a class="btn btn-primary" href="{{ URL::to('/download/supp/'.str_replace('learning/','',$topic['supplement']).'/'.str_replace(' ','',$topic['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                  </div>
                                @else
                                    Empty
                                @endif
                              </td>
                              <td class="text-center">
                                @if($topic['other'] !='')
                                  <div class="btn-group">
                                    <a class="btn btn-info" href="{{ URL::to('/download/other/'.str_replace('learning/','',$topic['other']).'/'.str_replace(' ','',$topic['name'])) }}" ><i class="fa fa-download"></i>&nbsp;Download</a>
                                  </div>
                                @else
                                  Empty
                                @endif
                              </td>
                          </tr>

                        </tbody>
                    </table>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>Task No.</th>
                                <th>Description</th>
                                <th>Topic Name</th>
                                <th>Show</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr>
                                <td class="text-center">{{ $entity['taskno'] }}</td>
                                <td>{{ $entity['desc'] }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="{{ URL::to('/student/tasks/'.$entity['id']) }}"><i class="fa fa-eye"></i></a>
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
