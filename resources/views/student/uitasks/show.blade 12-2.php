@extends('student/home')

<!-- untuk mengisi yield pada home.blade.php -->
@section('script')
<script src="/js/tabScript.js"></script>
<!-- Code Mirror Script & CSS -->
<script src="/js/lib/codemirror.js"></script>
<script src="/js/scroll/simplescrollbars.js"></script>
<script src="/js/mode/xml.js"></script>
<script src="/js/mode/active-line.js"></script>
<link rel="stylesheet" href="/css/codemirror.css">
<!-- Code Mirror Script & CSS -->
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        {{ Form::open(['route'=>'uitasks.store', 'files'=>true]) }}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $data['name'] }}</h3>
            </div>
            <div class="card-body">

                <!-- pesan jika berhasil (session) -->
                @if (Session::has('message'))
                <div id="alert-msg" class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                    {{ Session::get('message') }}
                </div>
                @endif

                <!-- pesan jika error (withErrors) -->
                @if(!empty($errors->all()))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                    {{ Html::ul($errors->all())}}
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h2 style="color: #555555;">Guide Document</h2>
                        <div class="w-100 h-100 guide-document">
                            <embed class="w-100  guide-reader" style="height: 640px; border-radius:5px; border-style:solid; border-width:2px; border-color: #E1E1E8;" type="application/pdf" src="{{ $data['guidepath'] }}"></embed>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="w-100 h-100">
                            <h2 style="color: #555555;">Code Editor</h2>
                            <div class="mb-0 nav nav-justified btn-group">
                                <tr>
                                    <!-- switch button menggunakan js -->
                                    <td>
                                        <input id="MainActivityTab" type="button" value="activity_main.xml" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="openTab('MainActivityTab','0')"></input>
                                    </td>
                                    <td>
                                        <input id="ColorTab" type="button" value="Color.xml" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="openTab('ColorTab','1')"></input>
                                    </td>
                                    <td>
                                        <input id="StringTab" type="button" value="String.xml" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="openTab('StringTab','2')"></input>
                                    </td>
                                </tr>
                            </div>

                            <!-- Textarea pengerjaan -->
                            <textarea name="MainActivity" id="MainActivity" class="code-box" style="padding: 20px; height: 100px;"></textarea>

                            <textarea name="Color" id="Color" class="w-100 code-box" style="display:none; padding: 20px; height: 93%;">color box</textarea>

                            <textarea name="String" id="String" class="w-100 code-box" style="display:none; padding: 20px; height: 93%;">string box</textarea>

                            <!-- data 'id' untuk UiTopicStdController -->
                            <input name="id" type="hidden" value={{$data['id']}}></input>

                        </div>
                    </div>

                </div>
            </div>

            <div class="button-group" style="padding-right:20px;padding-left:20px;">

                <!-- jika sudah sampai topic terakhir, next button disembunyikan -->
                @if($maxid > $data['id'])
                <input type="button" value="Next" onclick="window.location.href='{{ $data['id'] + 1 }}'" class="float-right mr-1 btn btn-outline-info" style="min-width: 120px; min-height: 45px;">
                @endif

                <!-- cek berdasarkan data student_submit  -->
                @if (count($stdSubmit)=='0')
                {{ Form::submit('Submit', ['class' => 'float-right mx-1 btn btn-primary', 'style' => 'min-width: 150px; min-height: 45px;'])}}
                @elseif ($stdSubmit[0]['checkstat']=='PASSED' || $stdSubmit[0]['checkstat']=='waiting')
                <input type="button" value="{{$stdSubmit[0]['checkstat']}}" class="float-right mr-1 btn btn-secondary" style="min-width: 150px; min-height: 45px;" disabled>
                @else
                {{ Form::submit('Submit', ['class' => 'float-right mx-1 btn btn-primary', 'style' => 'min-width: 150px; min-height: 45px;'])}}
                @endif

                <!-- back button -->
                <input type="button" value="Back" onclick="history.back()" class="mx-1 btn btn-outline-info" style="min-width: 120px; min-height: 45px;">

            </div>

            <div class="col-12" style="padding-top:30px;padding-right:20px;padding-left:20px;">
                <h1 style="color: #555555;">Result</h1>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="text-center">
                            <th>No.</th>
                            <th>Task</th>
                            <th>Report</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entities as $entity)
                        <!-- <tr>
                            <td>{{ $entity['taskno'] }}</td>
                            <td>{{ $entity['desc'] }}</td>
                            <td>{!! nl2br(e($entity['report'])) !!}</td>
                            <td>
                                @if ($entity['status']=='')
                                WAITING
                                @else
                                {{ $entity['status'] }}
                                @endif
                            </td>

                        </tr> -->
                        <tr>
                            <td>1</td>

                            <td>{{ $entity['name'] }}</td>

                            <td>{!! nl2br($entity['checkresult']) !!}</td>
                            <td>{{ $entity['checkstat'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- </form> -->
            {{ Form::close() }}
        </div>
    </div>

    @endsection