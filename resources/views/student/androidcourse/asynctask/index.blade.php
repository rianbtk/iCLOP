@extends('student/androidcourse/home')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-header">
            <h3 class="card-title">
                Start Learning Android Programming with APLAS
            </h3>
        </div>
        <div class="card-body">
            <form
                method="GET"
                action="http://learning.aplas.online/aplas/public/student/tasks"
                accept-charset="UTF-8"
            >
                <div class="form-group">
                    <label for="topic">Learning Topic:</label>
                    <select
                        class="form-control"
                        id="topicList"
                        onchange="this.form.submit();"
                        name="topicList"
                    >
                        <option value="6" selected="selected">
                            A2:Android Java - AsyncTask - for Android Studio
                            
                        </option>
                    </select>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea
                            id="desc"
                            class="form-control"
                            disabled=""
                            rows="2"
                        >
Java Edition for Android Studio
This topic contains learning about AsyncTask
</textarea
                        >
                    </div>

                    <!--
              <label for="topic">Topic:</label>
              <select class="form-control" onchange="doSomething(this)" id="topic" name="topic"><option value="6">A1:Java - Basic UI Java Edition - for Android Studio 3.x</option><option value="28">A1:Java - Basic UI Java Edition - for Android Studio 4.x</option><option value="15">A1:Kotlin - Basic UI Kotlin Edition</option><option value="7">B1:Java - Basic Activity Java Edition - for Android Studio 3.x</option><option value="30">B1:Java - Basic Activity Java Edition - for Android Studio 4.x</option><option value="16">B1:Kotlin - Basic Activity Kotlin Edition</option><option value="8">B2:Java - Advanced Widgets Java Edition - for Android Studio 3.x</option><option value="32">B2:Java - Advanced Widgets Java Edition - for Android Studio 4.x</option><option value="17">B2:Kotlin - Advanced Widgets Kotlin Edition</option><option value="4">B3:Java - Multiple Activities Java Edition  - for Android Studio 3.x</option><option value="34">B3:Java - Multiple Activities Java Edition - for Android Studio 4.x</option><option value="21">B3:Kotlin - Multiple Activities Kotlin Edition</option><option value="5">B4:Java - Multimedia Resources Java Edition</option><option value="23">B4:Kotlin - Multimedia Resources Kotlin Edition</option><option value="26">C1:Java - Basic Data Storage Java Edition</option></select>
            -->
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
                                <td>
                                    Resource for
                                    <b
                                        >C2:Java - AsyncTask - for
                                        Android Studio </b
                                    >
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    <div class="btn-group">
                                        <a class="btn btn-success" href="{{asset('download/asynctask/GUIDE ASYNCTASK.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                    </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    <div class="btn-group">
                                        <a class="btn btn-warning" href="{{asset('download/asynctask/TEST FILE.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                    </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    <div class="btn-group">
                                        <a class="btn btn-primary" href="{{asset('download/asynctask/SUPLEMENT FILE.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                    </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    <div class="btn-group">
                                        <a class="btn btn-info" href="{{asset('download/asynctask/GUIDE ASYNCTASK.rar')}}"><i class="fa fa-download"></i>&nbsp;Download</a>
                                    </div>
                                    </div>
                                </td>
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
                                <td> Create Layout and Project Configuration</td>
                                <td>
                                    C2:Java- AsyncTask Java Edition
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a
                                            class="btn btn-info"
                                            href="http://learning.aplas.online/aplas/public/student/tasks/39"
                                            ><i class="fa fa-eye"></i
                                        ></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td> Create Layout Activity_Main Image Download </td>
                                <td>
                                C2:Java- AsyncTask Java Edition 
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a
                                            class="btn btn-info"
                                            href="http://learning.aplas.online/aplas/public/student/tasks/31"
                                            ><i class="fa fa-eye"></i
                                        ></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>Create Layout Activity_Audio Play Music</td>
                                <td>
                                C2:Java- AsyncTask Java Edition 
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a
                                            class="btn btn-info"
                                            href="http://learning.aplas.online/aplas/public/student/tasks/32"
                                            ><i class="fa fa-eye"></i
                                        ></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td>Configuration MainActivity Download Image with AsyncTask</td>
                                <td>
                                C2:Java- AsyncTask Java Edition
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a
                                            class="btn btn-info"
                                            href="http://learning.aplas.online/aplas/public/student/tasks/33"
                                            ><i class="fa fa-eye"></i
                                        ></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">5</td>
                                <td>Configuration Activity Play Music with AsyncTask</td>
                                <td>
                                C2:Java- AsyncTask Java Edition
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a
                                            class="btn btn-info"
                                            href="http://learning.aplas.online/aplas/public/student/tasks/34"
                                            ><i class="fa fa-eye"></i
                                        ></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
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
