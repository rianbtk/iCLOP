@extends('student/pythoncourse/home')

<!-- untuk mengisi yield pada home.blade.php -->
@section('script')

<!-- Code Ace Library For Python -->
<!-- <script src={{ asset('js/ace/ace.js') }}></script> -->

<!-- Code Ace Library For Python -->
@endsection

@section('content')

<div class="row">



    <div class="col-12">





        <div class="card">

            <div class="card-header">

                <h2> {{ $infotopik->nama }} </h2>
                <h5> Materi Percobaan ke - {{ $percobaan->no_percobaan . ". " . $percobaan->nama_percobaan}} </h5>

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

                    <!--<label for="description">Catatan</label>-->

                    <div style="padding: 16px; background-color: #f5f5f5; border: 2px solid #e9e9e9">
                        <b>Catatan</b><br>
                        {{ $percobaan->catatan }}

                        <div id="syntax-error" style="font-weight: bold; color: red"></div>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="button-group">

                    <!-- Jika ingin kembali ke list percobaan -->

                    @php

                    $id_topik = $percobaan->id_topik;

                    @endphp

                    <!-- jika kembali ke list percobaan -->
                    <input type="button" value="List Percobaan"
                        onclick="window.location.href='{{ url('student/pythoncourse/python/taskdetail/'. $id_topik) }}'"
                        class="mx-1 btn btn-outline-primary" style="min-width: 120px; min-height: 45px;">

                    <!-- jika sudah sampai topic terakhir, next button dinonaktifkan -->

                    <a href="{{ $previous }}" class="float-right mr-1 btn btn-outline-primary"
                        style="min-width: 120px; min-height: 45px;">
                        Sebelumnya
                    </a>



                    

                    
                    

                    <!-- Button Compile -->
                    <input type="button" id="compile" value="Check Code Validity" class="float-right mr-1 btn btn-success"
                        style="min-width: 150px; min-height: 45px; display: none">



                    <a href="{{ $next }}" class="mx-1 btn btn-outline-primary"
                        style="min-width: 120px; min-height: 45px;">
                        Selanjutnya
                    </a>

                    <!-- feedback button -->
                    <a href="{{ url('/student/pythoncourse/python/feedback/'. $infotopik->id_topik.'/'.$percobaan->id_percobaan)}}" class="float-right mr-1 btn btn-primary"
                        style="padding: 8px; min-width: 120px; min-height: 45px; color:white;"><i class="fa fa-heart" aria-hidden="true"></i>
                        Feedback
                    </a>

                </div>



                <div class="row" style="padding-top:20px">

                    <div id="left-panel" class="col-md-6">

                        <div class="code-box-container"
                            style="box-shadow: 0 2px 5px 0 rgba(62, 64, 68, 0.5);height: 684px; width:100%;border-radius:5px; border-style:solid; border-width:4px; border-color: #E1E1E8;">

                            <div>

                                @php

                                $guidefiles = asset( $percobaan->panduanpath )
                                @endphp
                                <embed class="guide-reader" style="height: 636px; width:100%;margin-bottom: -4px;"
                                    type="application/pdf" src="{{ $guidefiles }}"></embed>

                            </div>

                            <div class="mb-0 nav nav-justified btn-group">

                                <tr>

                                    <!--  button download pdf -->

                                    <td>
                                        <a class="btn btn-success" style="border-radius:0px" href="{{ $guidefiles }}"
                                            target="_blank">
                                            <i class="fa fa-download"></i>&nbsp;Download Guide</a>

                                    </td>

                                </tr>

                            </div>

                        </div>

                    </div>



                    <div id="right-panel" class="col-md-6">

                        <div class="code-box-container"
                            style="box-shadow: 0 2px 5px 0 rgba(62, 64, 68, 0.5); border-radius:5px; border-style:solid; border-width:4px; border-color: #E1E1E8;">

                            <div class="mb-0 nav nav-justified btn-group">

                                <tr>

                                    <!-- switch button menggunakan js -->

                                    <td>

                                        <input id="MainActivityTab" type="button" value="main.py"
                                            class="btn btn-secondary tab-box rounded-0 font-italic"
                                            onclick="openTab('MainActivityTab','0')"></input>

                                        <!-- Inputan id topik dan id percobaan untuk compile -->
                                        <input type="hidden" name="id_topik" value="{{ $infotopik->id_topik }}" />
                                        <input type="hidden" name="id_percobaan"
                                            value="{{ $percobaan->id_percobaan }}" />


                                    </td>

                                </tr>

                            </div>



                            <!-- Text pengerjaan -->
                            <div style="height: 540px; border-right: 1px solid #e0e0e0" id="teks-editor"></div>
                            <!-- End Text Pengerjaan -->

                            <div class="mb-0 nav nav-justified btn-group">
                                <tr>

                                    <!-- switch button menggunakan js -->

                                    <td>
                                        <!-- <a class="btn btn-secondary tab-box rounded-0 font-italic"
                                            style="border-radius:0px; color:white;" onclick="orientationbutton()">

                                            <i class="fa fa-retweet"></i>&nbsp;Change orientation</a> -->
                                    </td>

                                </tr>

                            </div>

                            <!-- data 'id' untuk UiTopicStdController -->



                        </div>

                    </div>



                </div>

            </div>






            <div class="col-12" style="padding-top:20px;padding-right:20px;padding-left:20px;">

                <div class="row">
                    <div class="col-md-1 align-middle" style="border-right: 1px solid #e0e0e0">
                        <h1 style="margin: 0px"><span id="total-percobaan" class="text-center">0</span>x</h1>
                        <small>Submit</small>
                    </div>
                    <div class="col-md-10">
                        <h1 style="margin: 0px">Result</h1>
                        {{-- <code style="font-size: 20px" id="times" data-time="0">Coding Time - s</code> --}}
                    </div>
                </div>

                {{-- <p style="color: #ea5a73; font-size:larger">(After Trying 0 Times)</p> --}}

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr class="text-center">

                            <th>Submit No.</th>

                            <th>Nama Topik</th>

                            <th>Detail Validasi</th>

                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody id="table-body">

                    </tbody>

                </table>

            </div>

            <!-- </form> -->



        </div>

    </div>


    <!-- JS Python -->
    <!-- Option 1: Bundle Bootstrap dengan Popper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- <script src="https://www.jqueryscript.net/demo/Multifunctional-jQuery-Countdown-Stopwatch-Plugin-Timer-js/jquery.timer.js"></script> --}}



    <script>

        /**
         * jquery.timer.js
         *
         * Copyright (c) 2011 Jason Chavannes <jason.chavannes@gmail.com>
         *
         * http://jchavannes.com/jquery-timer
         *
         * Permission is hereby granted, free of charge, to any person
         * obtaining a copy of this software and associated documentation
         * files (the "Software"), to deal in the Software without
         * restriction, including without limitation the rights to use, copy,
         * modify, merge, publish, distribute, sublicense, and/or sell copies
         * of the Software, and to permit persons to whom the Software is
         * furnished to do so, subject to the following conditions:
         *
         * The above copyright notice and this permission notice shall be
         * included in all copies or substantial portions of the Software.
         *
         * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
         * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
         * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
         * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
         * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
         * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
         * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
         * SOFTWARE.
         */

        $(function() {

        $.timer = Timer;

        /**
         * First parameter can either be a function or an object of parameters.
         *
         * @param {function | {
         *   action: function,
         *   time: int=,
         *   autostart: boolean=
         * }} action
         * @param {int=} time
         * @param {boolean=} autostart
         * @returns {Timer}
         */
        function Timer(action, time, autostart) {

            if (this.constructor != Timer || this.init) {
                return new Timer(action, time, autostart);
            }

            this.set(action, time, autostart);

            return this;

        }

        /**
         * @see Timer
         *
         * @param {function | {
         *   action: function,
         *   time: int=,
         *   autostart: boolean=
         * }} action
         * @param {int=} time
         * @param {boolean=} autostart
         * @returns {Timer}
         */
        Timer.prototype.set = function(action, time, autostart) {

            this.init = true;

            if (typeof action == "object") {

                if (action.time) {
                    time = action.time;
                }

                if (action.autostart) {
                    autostart = action.autostart;
                }

                action = action.action;

            }

            if (typeof action == "function") {
                this.action = action;
            }

            if (!isNaN(time)) {
                this.intervalTime = time;
            }

            if (autostart && this.isReadyToStart()) {
                this.isActive = true;
                this.setTimer();
            }

            return this;

        };

        Timer.prototype.isReadyToStart = function() {

            var notActive = !this.active;
            var hasAction = typeof this.action == "function";
            var hasTime   = !isNaN(this.intervalTime);

            return notActive && hasAction && hasTime;

        };

        /**
         * @param {int=} time
         * @returns {Timer}
         */
        Timer.prototype.once = function(time) {

            var timer = this;

            if (isNaN(time)) {
                timer.action();
                return this;
            }

            setTimeout(fnTimeout, time);
            return this;

            function fnTimeout() {
                timer.action();
            }

        };

        /**
         * @param {boolean=} reset
         * @returns {Timer}
         */
        Timer.prototype.play = function(reset) {

            if (this.isReadyToStart()) {

                if (reset) {
                    this.setTimer();
                }
                else {
                    this.setTimer(this.remaining);
                }

                this.isActive = true;

            }

            return this;

        };

        /**
         * @returns {Timer}
         */
        Timer.prototype.pause = function() {

            if (this.isActive) {

                this.isActive   = false;
                this.remaining -= new Date() - this.last;

                this.clearTimer();

            }

            return this;

        };

        /**
         * @returns {Timer}
         */
        Timer.prototype.stop = function() {

            this.isActive  = false;
            this.remaining = this.intervalTime;

            this.clearTimer();

            return this;

        };

        /**
         * @param {boolean=} reset
         * @returns {Timer}
         */
        Timer.prototype.toggle = function(reset) {

            if (this.isActive) {
                this.pause();
            }
            else if (reset) {
                this.play(true);
            }
            else {
                this.play();
            }

            return this;

        };

        /**
         * @returns {Timer}
         */
        Timer.prototype.reset = function() {

            this.isActive = false;
            this.play(true);

            return this;

        };

        /**
         * @returns {Timer}
         */
        Timer.prototype.clearTimer = function() {
            clearTimeout(this.timeoutObject);
            return this;
        };

        /**
         * @returns {Timer}
         */
        Timer.prototype.setTimer = function(time) {

            var timer = this;

            if (isNaN(time)) {
                time = this.intervalTime;
            }

            this.remaining = time;
            this.last      = new Date();

            this.clearTimer();

            this.timeoutObject = setTimeout(fnTimeout, time);

            return this;

            function fnTimeout() {
                timer.execute();
            }

        };

        /**
         * @returns {Timer}
         */
        Timer.prototype.execute = function() {

            if (this.isActive) {

                try {
                    this.action();
                }
                finally {
                    this.setTimer();
                }

            }

            return this;

        };

        })
    </script>
    <script src={{ asset('js/ace/ace.js') }}></script>
    <script>


        $(function() {


            function startTime( status ) {


                
                var count = 0;
                var timer = $.timer(function() {

                        count = ++count;
                        // $('#times').text("Coding Time "+(count)+" s");
                        // $("#times").data("time", count);  
                        
                        // console.log( count );
                    });

                    timer.set({ time : 1000, autostart : true });
                    

                
            }            
        

            function startEditor() {

                let editor;
                editor = ace.edit("teks-editor");
                // tampilan tema warna editor
                editor.setTheme("ace/theme/tomorrow_night");
                // editor.setTheme("ace/theme/clouds");
                editor.session.setMode("ace/mode/python");
                editor.setFontSize("12px");
                
            }


            function openmodule() {

                $.ajax({

                    type: "GET",
                    url: "{{ url('student/pythoncourse/python-history/'. $infotopik->id_topik.'/'. $percobaan->id_percobaan) }}",
                    dataType: "json",
                    success: function( result ) {

                        let btncompiler = $('#compile');

                        if ( (result.validasi).length > 0 ){


                            let editor = ace.edit("teks-editor");

                            // tampilan tema warna editor
                            editor.setTheme("ace/theme/tomorrow_night");
                            // editor.setTheme("ace/theme/clouds");
                            editor.session.setMode("ace/mode/python");
                            editor.setFontSize("12px");

                            if ( result.statusPassed == false ) {

                                btncompiler.show(500);


                                editor.setValue(`{{ $percobaan->texteditor }}`);
                                
                            } else { // PASSED

                                
                                // editor.setValue("");
                                editor.setValue( result.hist_texteditor );

                                // console.log( result.hist_texteditor );
                            }
                            

                            startEditor();
                            
                            // $('#times').text("Coding Time "+((result.validasi)[0].time)+" s");
                            $('#total-percobaan').text(result.submit);

                        } else {

                            btncompiler.hide();

                            $('#teks-editor').html(`
                                <div class="text-center align-middle">
                                    

                                    <div class="form-group" style="margin-top: 100px; margin-bottom: 100px">
                                        <small style="color: #9e9e9e">Mulai Mengerjakan Percobaan</small><br>
                                        <button class="btn btn-success" id="start-learning">Start Test</button>    
                                    </div>

                                </div>
                            `);
                        }
                    }
                });
            }


            openmodule();


            // execute button pertama (Start Test)
            $("#teks-editor").on("click", "#start-learning", function() {

                
                
                $("#teks-editor").html("");
                startEditor();

                let editor = ace.edit("teks-editor");
                editor.setValue(`{{ $percobaan->texteditor }}`);


                // btn compiler
                $('#compile').show(1000);
            });

            



















            // menampilkan hasil validasi ditampilan awal (Tabel Results)
            hasilValidasi();

            $('#compile').click(function () {

                executing();
            });

            function executing() {

                let editor = ace.edit("teks-editor");
                var editorString = editor.getSession().getValue();
                // #mengkosongkan error sintax
                $('#syntax-error').html("").hide();


                if ( editorString.length > 0 ) {
 
                    // ambil inputan id_topik dan id_percobaan disimpan di variabel
                    var variabelid_topik = $('input[name="id_topik"]').val();
                    var variabelid_percobaan = $('input[name="id_percobaan"]').val();

                    $.ajax({
                        url: "http://127.0.0.1:8000/student/pythoncourse/python-compile",
                        method: "GET", // GET | POST
                        data: {
                            code: editor.getSession().getValue(),
                            id_topik: variabelid_topik,
                            id_percobaan: variabelid_percobaan,
                            // time: $('#times').data("time")
                        },
                        dataType: "json",
                        success: function (response) {



                            

                            if (response.status == true) {


                                // total percobaan 
                                $('#total-percobaan').text(response.jml);

                                // true alias berhasil input data tampil hasil validasi
                                hasilValidasi();

                                if ( response.data ) {
                                    // pengerjaan telah passed 
                                    $('#compile').hide(1000);
                                    
                                }

                            } else {

                                // alert( response.status );
                                $('#syntax-error').html("<hr><h5>Sintaks Kode Error !</h5>" + response.data).hide().fadeIn(1000);
                            }

                        },

                        error: function (q) {

                            console.log(q);
                        }
                    })

                } else {

                    alert("Mohon isi source code anda");
                }
            }




            // fungsi untuk menampilkan hasil validasi 
            function hasilValidasi() {

                // ambil inputan id_topik dan id_percobaan
                var variabelid_topik = $('input[name="id_topik"]').val();
                var variabelid_percobaan = $('input[name="id_percobaan"]').val();

                $.ajax({



                    type: "GET",
                    url: "{{ url('student/pythoncourse/python/tampil-data-validation') }}",
                    data: "id_topik=" + variabelid_topik + "&id_percobaan=" + variabelid_percobaan,
                    dataType: "json",
                    success: function (response) {

                        if (response.status == true) {

                            if ( response.statusPassed == true ) {

                                $('#compile').hide();
                            }

                            $("#table-body").html(response.data);
                        }
                    }
                })
            }
        
        })
    </script>
    <!-- End JS Python -->


    @endsection

    

    

    