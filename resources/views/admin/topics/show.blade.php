@extends('admin/admin')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">A Learning Topic Detail</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="name">Topic Id</label>
                              <input id="topicid" type="text" value="{{ $topic['id'] }}" class="form-control" disabled />
                          </div>
                            <div class="form-group">
                                <label for="name">Topic Name</label>
                                <input id="name" type="text" value="{{ $topic['name'] }}" class="form-control" disabled />
                            </div>
                            <div class="form-group">
                                <label for="price">Learning Stage</label>
                                <input id="stage" type="text" value="{{ $topic['stage'] }}" class="form-control" disabled />
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="desc" class="form-control" disabled rows="5">{{ $topic['desc'] }}</textarea>
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
