@extends('admin/admin')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">A Test File Detail</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="data1">File Id</label>
                              <input id="data1" type="text" value="{{ $data['id'] }}" class="form-control" disabled />
                          </div>
                            <div class="form-group">
                                <label for="data2">Task</label>
                                <input id="data2" type="text" value="{{ $task['taskno'].'. '.$task['desc'] }}" class="form-control" disabled />
                            </div>
                            <div class="form-group">
                                <label for="data3">File Name</label>
                                <input id="data3" type="text" value="{{ $data['fileName'] }}" class="form-control" disabled />
                            </div>
                            <div class="form-group">
                                <label for="data5">File Content</label>
                                <textarea id="data5" class="form-control" disabled rows="5">{{ $content }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="data4">Topic</label>
                                <input id="data4" type="text" value="{{ $topic['name'] }}" class="form-control" disabled />
                            </div>
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
