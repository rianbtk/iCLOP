@extends('teacher/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">JPLAS Learning Result</h3>
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
             {!! Form::label('student', 'Lesson:') !!}
             <select class="form-control" id="stdList" name="stdList" onChange='this.form.submit();'>
             @foreach($items as $item)
             <option value="{{ $item->id }}" {{ $item->id == $filter ? 'selected' : '' }}>{{ '['.$item->id.'] '.$item->name }}</option>
             @endforeach
             </select>
            {{ Form::close() }}
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
				<th>No</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Comment</th>
                                <th>Upload Time</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $x => $entity)
                            <tr>
				<td>{{ $x+1 }}</td>
                                <td>{{ $entity['stdname'].' / '.$entity['userid'] }}</td>
                                <td>{{ $entity['tcname'].' / ' .$entity['cname']}}</td>
                                <td>{{ $entity['comment'] }}</td>
                                <td>{{ $entity['created_at'] }}</td>
                                <td class="text-center">
                                        <div class="btn-group">
@if (file_exists('/var/www/html/aplas/storage/app/public/'.$entity['filepath']))
                        <a class="btn  btn-info " 
href="{{ URL::to('/download/jresult/'.str_replace('results/','',$entity['filepath']).'/'.$entity['jplasid'].'-'.str_replace('_','',$entity['stdname'])) }}">
			<i class="fa fa-download"></i></a>
@endif
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
