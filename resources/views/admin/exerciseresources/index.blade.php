@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">APLAS Exercise Resources</h3>
                <div class="card-tools">
                    <a href="{{ URL::to('/admin/exerciseresources/create')}}" class="btn btn-tool">
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

                @if (!empty($itemsstage))
                {{ Form::open(['method' => 'GET']) }}
                <div class="form-group">
                    {!! Form::label('stage', 'Select Exercise Stage:') !!}
                    {!! Form::select('stageList', $itemsstage , $filter, ['class' => 'form-control', 'id' => 'stageList', 'onchange' => 'this.form.submit();']) !!}
                    <p></p>

                    {!! Form::label('exercise', 'Select Exercise:') !!}
                    {!! Form::select('exerciseList', $itemsexercise , $filterexercise, ['class' => 'form-control', 'id' => 'exerciseList', 'onchange' => 'this.form.submit();']) !!}
                    {{ Form::close() }}
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
                                        <form method="POST" action="{{ URL::to('/admin/exerciseresources/'.$entity['id']) }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE" />
                                            <div class="btn-group">
                                                <a class="btn btn-info" href="{{ URL::to('/admin/exerciseresources/'.$entity['id']) }}"><i class="fa fa-eye"></i></a>
                                                <a class="btn btn-success" href="{{ URL::to('/admin/exerciseresources/'.$entity['id'].'/edit') }}"><i class="fa fa-pencil-alt"></i></a>
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
                @else
                <td><b>Exercise Resources is not available</b></td>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection
