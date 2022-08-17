    @extends('student/unitycourse/home')
    @section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-header">
                <h3 class="card-title">Start Learning Unity Programming with ICLOP</h3>
            </div>
            <div class="card-body">

                <form method="GET" action="http://127.0.0.1:8000/student/tasks" accept-charset="UTF-8">
                    <div class="form-group">
                        <label for="topic">Learning Topic:</label>
                        <select class="form-control" id="topicList" onchange="this.form.submit();" name="topicList">
                            <option value="6" selected="selected">Make Game</option>
                        </select>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="desc" class="form-control" disabled="" rows="2">Pembelajaran topik Dynamic Content dengan membuat form dengan HTML</textarea>
                        </div>
                    </div>
                </form>
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

                                <tr>
                                    <td>Resource for <b>N2:NodeJS - Dynamic Content</b></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-success" href="{{asset('download/dynamic_content/Modul1.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-warning" href="{{asset('download/dynamic_content/Modul1.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                        </div>
                                    </td>
                                    <!-- <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-primary" href="{{asset('download/firebase/GUIDE_FIREBASE.rar')}}" disa><i class="fa fa-download"></i>&nbsp;Download</a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="{{asset('download/firebase/GUIDE_FIREBASE.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                        </div>
                                    </td> -->
                                </tr>

                            </tbody>
                        </table>


                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>Task No.</th>
                                    <th>Description</th>
                                    <th>Topic Name</th>
                                    <th>Show</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Make Game</td>
                                    <td>Game slincer</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="http://127.0.0.1:8000/student/tasks/39"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Make Game</td>
                                    <td>Game slincer</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="http://127.0.0.1:8000/student/tasks/31"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>Make Game</td>
                                    <td>Game slincer</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a class="btn btn-info" href="http://127.0.0.1:8000/student/tasks/32"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection