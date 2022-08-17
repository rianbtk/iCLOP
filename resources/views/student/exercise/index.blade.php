@extends('student/home')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card-header">
      <h3 class="card-title">Android Programming Excercise with APLAS</h3>
    </div>
    <div class="card-body">
      @if (Session::has('message'))
      <div id="alert-msg" class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
        {{ Session::get('message') }}
      </div>
      @endif
      @if(!empty($errors->all()))
      <div class="alert alert-danger">
        {{ Html::ul($errors->all())}}
      </div>
      @endif

      @if (!empty($itemsstage))
      {{ Form::open(['method' => 'GET']) }}
      <div class="form-group">
        {!! Form::label('stage', 'Select Stage:') !!}
        {!! Form::select('stageList', $itemsstage , $filter, ['class' => 'form-control', 'id' => 'stageList', 'onchange' => 'this.form.submit();']) !!}
        {{ Form::close() }}
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
              @foreach($topic as $data)
              <tr>
                <td rowspan="2">Resource for <b>{{ $data['name'] }}</b></td>
                <td colspan="4">
                  {{ $data['desc'] }}
                </td>
              </tr>
              <td class=" text-center">
                @if($data['guide'] !='')
                <div class="btn-group">
                  <a class="btn btn-success" href="{{ URL::to('/download/exerciseguide/'.str_replace('exercise_files/','',$data['guide']).'/'.str_replace(' ','',$data['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                </div>
                @else
                Empty
                @endif
              </td>
              <td class="text-center">
                @if($data['testfile'] !='')
                <div class="btn-group">
                  <a class="btn btn-warning" href="{{ URL::to('/download/exercisetest/'.str_replace('exercise_files/','',$data['testfile']).'/'.str_replace(' ','',$data['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                </div>
                @else
                Empty
                @endif
              </td>
              <td class="text-center">
                @if($data['supplement'] !='')
                <div class="btn-group">
                  <a class="btn btn-primary" href="{{ URL::to('/download/exercisesupp/'.str_replace('exercise_files/','',$data['supplement']).'/'.str_replace(' ','',$data['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                </div>
                @else
                Empty
                @endif
              </td>
              <td class="text-center">
                @if($data['other'] !='')
                <div class="btn-group">
                  <a class="btn btn-info" href="{{ URL::to('/download/exerciseother/'.str_replace('exercise_files/','',$data['other']).'/'.str_replace(' ','',$data['name'])) }}"><i class="fa fa-download"></i>&nbsp;Download</a>
                </div>
                @else
                Empty
                @endif
              </td>
              </tr>
              @endforeach
            </tbody>
          </table>

        </div>
        @else
        <td><b>Exercise is not available at this time</b></td>
        @endif
      </div>

    </div>
  </div>
</div>
</div>

@endsection
