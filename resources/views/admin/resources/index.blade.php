@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">APLAS Topic Resources</h3>
                <div class="card-tools">
                 <a href="{{ URL::to('/admin/resources/create')}}" class="btn btn-tool">
                     <i class="fa fa-plus"></i>
                     &nbsp; Add
                 </a>
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
              <label>Display as Topic</label>
              {!! Form::label('topic', 'Topic:') !!}
              {!! Form::select('topicList', $items , $filter, ['class' => 'form-control', 'id' => 'topicList', 'onchange' => 'this.form.submit();']) !!}
             {{ Form::close() }}
            <!--
              {!! Form::label('topic', 'Topic:') !!}
              {!! Form::select('topic', $items , null, ['class' => 'form-control', 'onchange' => 'doSomething(this)']) !!}
            -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>File Name</th>
                                <th>Folder Path</th>
                                <th>Topic Name</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $index => $entity)
                            <tr>
                                <td class="text-center">{{ $index +1 }}</td>
                                <td>{{ $entity['fileName'] }}</td>
                                <td>{{ $entity['path'] }}</td>
                                <td>{{ $entity['name'] }}</td>
                                <td>{{ $entity['desc'] }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ URL::to('/admin/resources/'.$entity['id']) }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="{{ URL::to('/admin/resources/'.$entity['id']) }}"><i class="fa fa-eye"></i></a>
                                            <a class="btn btn-success" href="{{ URL::to('/admin/resources/'.$entity['id'].'/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </form>
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
