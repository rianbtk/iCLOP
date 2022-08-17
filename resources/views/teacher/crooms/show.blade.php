@extends('teacher/home')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">A Learning Topic Detail</h3>
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
                                        <th>No</th>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entities as $index => $entity)
                                    <tr>
                                        <td class="text-center">{{ $index +1 }}</td>
                                        <td>{{ $entity['name'] }}</td>
                                        <td>{{ $entity['email'] }}</td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
        
        
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                  <input type="button" value="Back" onclick="history.back()" class="btn btn-outline-info">
                  <!--
                    <a href="{{ URL::to('admin/topic') }}" class="btn btn-outline-info">Back</a>
                  -->
                </div>

            </div>
        </div>
    </div>
@endsection
