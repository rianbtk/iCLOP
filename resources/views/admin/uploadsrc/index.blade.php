@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Source of Result File [Student name: {{ $student['name'] }}, Topic: {{ $topic['name'] }} ]</h3>
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
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No.</th>
                                <th>File Name</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $index => $entity)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $entity['fileName'] }}</td>
                                <td>
				<textarea id="{{ 'src'.($index+1) }}" rows="20" cols="80" disabled 
				style="font-family:Courier New; color:#003399; font-size: 10px; white-space:pre-wrap">
				{{ File::get(storage_path('app/public/'.$entity['rscfile'])) }}
				</textarea>
				{{ storage_path('app/public/'.$entity['rscfile']) }}

				</td>
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
            <a href="{{ URL::to('admin/resview') }}" class="btn btn-outline-info">Back</a>
          -->
        </div>
    </div>
</div>
</div>

@endsection
