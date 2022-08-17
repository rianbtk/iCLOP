@extends('admin/admin')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">A UI Learning Topic Detail</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Topic Id</label>
                            <input id="topicid" type="text" value="{{ $uitopic['id'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="name">Topic Lavel</label>
                            <input id="name" type="text" value="{{ $uitopic['level'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="name">Topic Name</label>
                            <input id="name" type="text" value="{{ $uitopic['name'] }}" class="form-control" disabled />
                        </div>
			<div class="form-group">
                            <label for="note">Topic Note</label>
                            <textarea id="note" class="form-control" disabled rows="5">{{ $uitopic['note'] }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Project Name</label>
                            <input id="stage" type="text" value="{{ $uitopic['projectname'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="price">Project Folder Path</label>
                            <input id="stage" type="text" value="{{ $uitopic['projectpath'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="price">Package Name File</label>
                            <input id="stage" type="text" value="{{ $uitopic['packname'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="desc" class="form-control" disabled rows="5">{{ $uitopic['description'] }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Guide File (*.pdf)</label>
                            <input id="stage" type="text" value="{{ $uitopic['guidepath'] }}" class="form-control" disabled />
                        </div>
                        <div class="form-group">
                            <label for="price">Status</label>
                            <input id="stage" type="text" value="{{ $uitopic['status'] }}" class="form-control" disabled />
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
