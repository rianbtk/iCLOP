@extends('student/home')

<!-- untuk mengisi yield pada home.blade.php -->
@section('script')
<script src="/js/tabScript.js"></script>
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
                            <embed class="w-100 h-100 guide-reader" style="min-height: 500px;" type="application/pdf" src="{{ $data['guidepath'] }}"></embed>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="w-100 h-100">
                            <h2 style="color: #555555;">Code Editor</h2>
                            <div class="mb-0 nav editor-header nav-justified btn-group">
                                <tr>
                                    <!-- switch button menggunakan js -->
                                    <td>
                                        <input type="button" value="activity_main.xml" class="btn btn-outline-primary rounded-0 font-italic" onclick="openTab('MainActivity')"></input>
                                    </td>
                                    <td>
                                        <input type="button" value="Color.xml" class="btn btn-outline-primary rounded-0 font-italic" onclick="openTab('Color')"></input>
                                    </td>
                                    <td>
                                        <input type="button" value="String.xml" class="btn btn-outline-primary rounded-0 font-italic" onclick="openTab('String')"></input>
                                    </td>
                                </tr>
                            </div>

                            <!-- Textarea pengerjaan -->
                            <textarea name="MainActivity" id="MainActivity" class="w-100 h-30 code-box form-control rounded-0" style="height: 93%;"></textarea>

                            <textarea name="Color" id="Color" class="w-100 h-30 code-box" style="display:none;height: 95%;">color box</textarea>

                            <textarea name="String" id="String" class="w-100 h-30 code-box" style="display:none;height: 95%;">string box</textarea>

                            <!-- data 'id' untuk UiTopicStdController -->
                            <input name="id" type="hidden" value={{$data['id']}}></input>

                        </div>
                    </div>

                </div>
            </div>

            <div class="button-group" style="padding-top:60px;padding-right:20px;padding-left:20px;">

                <!-- jika sudah sampai topic terakhir, next button disembunyikan -->
                @if($maxid > $data['id'])
                <input type="button" value="Next" onclick="window.location.href='{{ $data['id'] + 1 }}'" class="float-right mr-1 btn btn-primary" style="min-width: 120px; min-height: 45px;">
                @endif

                <!-- save button jika berhasil maka akan disabled dan berubah menjadi 'waitting' -->
                @if (Session::has('message'))
                <input type="button" value="Checking.." class="float-right mr-1 btn btn-warning" style="min-width: 120px; min-height: 45px;" disabled>
                @else
                {{ Form::submit('Save', ['class' => 'float-right mx-1 btn btn-success', 'style' => 'min-width: 120px; min-height: 45px;'])}}
                @endif

                <!-- back button -->
                <input type="button" value="Back" onclick="history.back()" class="mx-1 btn btn-danger" style="min-width: 120px; min-height: 45px;">

            </div>

            <div class="col-12" style="padding-top:30px;padding-right:20px;padding-left:20px;">
                <h1 style="color: #555555;">Result</h1>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="text-center">
                            <th>No.</th>
                            <th>Task</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Create TextView</td>
                            <td>Passed</td>
                            <td>Success</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Textview set gravity</td>
                            <td>
                                String btnText gravity is not valid expected: center but was: left<Convert[er]>
                            </td>
                            <td>FAILED</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- </form> -->
            {{ Form::close() }}
        </div>
    </div>

    @endsection