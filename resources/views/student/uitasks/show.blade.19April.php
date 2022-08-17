@extends('student/home')

<!-- untuk mengisi yield pada home.blade.php -->
@section('script')
<script src="{{URL::to('/js/tabScript.js')}}"></script>
<!-- Code Mirror Script & CSS -->
<script src="{{URL::to('/js/lib/codemirror.js')}}"></script>
<script src="{{URL::to('/js/mode/xml/xml.js')}}"></script>
<script src="{{URL::to('/js/mode/active-line.js')}}"></script>
<script src="{{URL::to('/js/addon/edit/matchtags.js')}}"></script>
<script src="{{URL::to('/js/addon/edit/closetag.js')}}"></script>
<script src="{{URL::to('/js/addon/fold/xml-fold.js')}}"></script>
<link rel="stylesheet" href="{{URL::to('/css/codemirror.css')}}">
<!-- Code Mirror Script & CSS -->
@endsection

@section('content')

<div class="row">

    <div class="col-12">



        {{ Form::open(['route'=>'uitasks.store', 'files'=>true]) }}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">{{ $data['id'].'. '.$data['name'] }}</h3>

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


                <!-- kolom untuk overview materi pembelajaran -->

		<div class="form-group">

                    <!--<label for="description">Note</label>-->

                    <textarea id="desc" class="form-control" disabled rows="2"> {{ $data['note'] }}</textarea>

                </div>


                <div class="button-group">


                    <!-- jika sudah sampai topic terakhir, next button dinonaktifkan -->

		    @if($nextid > $data['id'])

                    <input type="button" value="Next" onclick="window.location.href='{{ $nextid }}'" class="float-right mr-1 btn btn-outline-primary" style="min-width: 120px; min-height: 45px;">

                    @else

                    <input type="button" value="Next" class="float-right mr-1 btn btn-outline-primary" style="min-width: 120px; min-height: 45px;" disabled>

                    @endif



                    <!-- cek Submit Button berdasarkan data student_submit  -->

                    @if (count($stdSubmit)=='0')

                    {{ Form::submit('Submit', ['class' => 'float-right mx-1 btn btn-warning', 'style' => 'min-width: 150px; min-height: 45px;', 'onclick' => 'return submitbutton()'])}}

                    @elseif ($stdSubmit[0]['checkstat']=='waiting')

                    <input type="button" value="{{$stdSubmit[0]['checkstat']}}" class="float-right mr-1 btn btn-secondary" style="min-width: 150px; min-height: 45px;" disabled>

                    @elseif ($stdSubmit[0]['checkstat']=='PASSED')

                    <input type="button" value="{{$stdSubmit[0]['checkstat']}}" class="float-right mr-1 btn btn-success" style="min-width: 150px; min-height: 45px;" disabled>

                    @else

                    {{ Form::submit('Re-Submit', ['class' => 'float-right mx-1 btn btn-warning', 'style' => 'min-width: 150px; min-height: 45px;', 'onclick' => 'return submitbutton()'])}}

                    @endif



                    <!-- back button -->

		    @if($previousid > $data['id'] || $previousid != 0)

                    <input type="button" value="Previous" onclick="window.location.href='{{ $previousid }}'" class="mx-1 btn btn-outline-primary" style="min-width: 120px; min-height: 45px;">

                    @else

                    <input type="button" value="Previous" class="mx-1 btn btn-outline-primary" style="min-width: 120px; min-height: 45px;" disabled>

                    @endif


                </div>



                <div class="row" style="padding-top:20px">

                    <div id="left-panel" class="col-md-6">

<!--                        <div>

                            <embed class="guide-reader" style="box-shadow: 0 2px 5px 0 rgba(62, 64, 68, 0.5); height: 646px; width:100%; border-radius:5px; border-style:solid; border-width:4px; border-color: #E1E1E8;" type="application/pdf" 
src="{{ 'http://aplas.polinema.ac.id/aplas/public/storage/'.$data['guidepath'] }}"></embed>

                        </div> -->

                    </div>



                    <div id="right-panel" class="col-md-6">

                        <div class="code-box-container" style="box-shadow: 0 2px 5px 0 rgba(62, 64, 68, 0.5); border-radius:5px; border-style:solid; border-width:4px; border-color: #E1E1E8;">

                            <div class="mb-0 nav nav-justified btn-group">

                                <tr>

                                    <!-- switch button menggunakan js -->

                                    <td>

                                        <input id="MainActivityTab" type="button" value="activity_main.xml" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="openTab('MainActivityTab','0')"></input>

                                    </td>

                                    <td>

                                        <input id="ColorTab" type="button" value="colors.xml" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="openTab('ColorTab','1')"></input>

                                    </td>

                                    <td>

                                        <input id="StringTab" type="button" value="strings.xml" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="openTab('StringTab','2')"></input>

                                    </td>

                                </tr>

                            </div>



                            <!-- Textarea pengerjaan -->

                            <textarea name="MainActivity" id="MainActivity" class="code-box" style="padding: 20px; height: 100px;"></textarea>



                            <textarea name="Color" id="Color" class="w-100 code-box" style="display:none; padding: 20px; height: 93%;">color box</textarea>



                            <textarea name="String" id="String" class="w-100 code-box" style="display:none; padding: 20px; height: 93%;">string box</textarea>

			    <div class="mb-0 nav nav-justified btn-group">
			    	<tr>

                               	    <!-- switch button menggunakan js -->

                                    <td>

                                        <input id="Change Orientation" type="button" value="Change Orientation" class="btn btn-secondary tab-box rounded-0 font-italic" onclick="orientationbutton()"></input>

                                    </td>

		            	</tr>

		            </div>

                            <!-- data 'id' untuk UiTopicStdController -->

                            <input name="id" type="hidden" value={{$data['id']}}></input>



                        </div>

                    </div>



                </div>

            </div>







            <div class="col-12" style="padding-top:20px;padding-right:20px;padding-left:20px;">

                <h1>Result</h1>

                <p style="color: #ea5a73; font-size:larger">(After Trying {{ $numberOfTries }} Times)</p>

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr class="text-center">

                            <th>Submit No.</th>

                            <th>Topic Name</th>

                            <th>Validation Detail</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php $no = count($entities) ?>

                        @foreach($entities as $entity)

			@if ($entity['checkstat'] == 'ERROR' || $entity['checkstat'] == 'FAILED')
                            <tr class="table-danger">
                        @elseif ($entity['checkstat'] == 'PASSED')
                            <tr class="table-success">
                        @else
                            <tr class="table-warning">
                        @endif

                            <td><?php echo ($no) ?></td>

                            <td>{{ $entity['name'] }}</td>

                            <td> {!! nl2br($entity['report']) !!} </td>

			    <td class="text-uppercase font-weight-bold"> {{ $entity['checkstat'] }} </td>

                        </tr>

                        <?php $no--; ?>

                        @endforeach

                    </tbody>

                </table>

            </div>

            <!-- </form> -->

            {{ Form::close() }}

        </div>

    </div>



    @endsection
