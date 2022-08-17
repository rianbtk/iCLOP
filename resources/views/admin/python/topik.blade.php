@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Python Learning Topics</h3>
                <div class="card-tools">
                    <a href="{{ URL::to('/admin/python/tambahtopik')}}" class="btn btn-tool">
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
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>ID Topik</th>
                                    <th>Bab</th>
                                    <th>Nama Topik</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $topik AS $tp )
                                <tr>
                                    <td>{{ $tp->id_topik }}</td>
                                    <td>{{ $tp ->bab}}</td>
                                    <td>{{ $tp->nama }}</td>
                                    <td>{{ $tp->deskripsi }}</td>
                                    <td>{{ $tp->status }}</td>
                                    <td align="center">
                                        <a href="{{ url('/admin/python/proseshapustopik/'. $tp->id_topik) }}" onclick="return confirm('Apakah anda ingin menghapus topik ini ?')" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <a href="{{ url('/admin/python/edittopik/'. $tp->id_topik) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
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
