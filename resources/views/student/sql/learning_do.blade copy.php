@extends('student/home')
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/codemirror/theme/dracula.css') }}">
@endsection
@section('js')
    <script src="{{ asset('lte/plugins/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('lte/plugins/codemirror/mode/sql/sql.js') }}"></script>
    <script>
        var CM;
        $(document).ready(function() {
            let id = '{{ request()->segment(count(request()->segments())) }}';
            let url = '{{ route('student sql learning do read', ':id') }}'
            url = url.replace(":id", id);

            $.ajax({
                type: "GET",
                url: url,
                success: function(response) {
                    if (response.previous) {
                        let url = '{{ route('student sql learning do', ':id') }}'
                        url = url.replace(":id", response.previous);

                        $('a[data-action=previous]').attr('href', url)
                    } else {
                        $('a[data-action=previous]').addClass('d-none');
                    }

                    if (response.next) {
                        let url = '{{ route('student sql learning do', ':id') }}'
                        url = url.replace(":id", response.next);

                        $('a[data-action=next]').attr('href', url)
                    } else {
                        $('a[data-action=next]').addClass('d-none');
                    }

                    $('h3.card-title').html(response.name);
                    $('embed#guide').attr('src', '{{ asset('') }}/' + response.file);
                    formElm();

                    if (response.status == 'lulus') {
                        CM.setOption("readOnly", true);
                        $('button[type=reset]').attr('disabled', true);
                        $('button[type=submit]').attr('disabled', true);
                    }
                    formSubmit();
                }
            });
        });

        const formElm = () => {
            CM = CodeMirror.fromTextArea($('textarea#syntax')[0], {
                lineNumbers: true,
                styleActiveLine: true,
                mode: "sql",
                theme: "dracula"
            });
            CM.on('change', function() {
                if (CM.getValue() != '') {
                    $('button[type=submit]').removeAttr('disabled');
                } else {
                    $('button[type=submit]').attr('disabled', true);
                }
            });
        }

        const formSubmit = () => {
            $('form').on('submit', function(e) {
                e.preventDefault();
                let id = '{{ request()->segment(count(request()->segments())) }}';
                let url = '{{ route('student sql learning do exec', ':id') }}'
                url = url.replace(":id", id);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $('form').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('button[type=reset]').removeAttr('disabled');
                        $('button[type=submit]').attr('disabled', true);

                        let html = '';
                        let gagal = 0;
                        let lulus = 0;
                        $.each(response, function(index, value) {
                            if (typeof value.message != 'undefined' && typeof value.status != 'undefined') {
                                if (value.status == 'lulus') {
                                    html += '<span class="my-1 badge badge-success d-block text-left">' + value.message + '</span>';
                                    lulus += 1;
                                }
                                if (value.status == 'gagal') {
                                    html += '<span class="my-1 badge badge-danger d-block text-left">' + value.message + '</span>';
                                    gagal += 1;
                                }
                            }
                        });
                        $('form div#result').html(html);
                        $('form h5#result-text').html('<span class="badge badge-success align-middle">' + lulus + ' Lulus</span> <span class="badge badge-danger align-middle">' + gagal + ' Gagal</span>');
                        // if (typeof response == 'object' && response.length) {
                        // }
                    }
                });
            });
        }
    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <form method="POST" action="javascrip:void(0)" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <h3 class="card-title">1. DDL - Database Creation</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="javascript:void(0)" data-action="previous" class="btn btn-outline-secondary">Sebelumnya</a>
                                <a href="javascript:void(0)" data-action="next" class="btn btn-outline-primary">Selanjutnya</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group mb-2">
                                    <textarea name="syntax" id="syntax" class="d-none"></textarea>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="mb-2 d-flex justify-content-between">
                                        <h5 class="m-0 align-self-center" id="result-text">Hasil</h5>
                                        <div class="d-block">
                                            <button type="reset" class="btn btn-xs btn-danger" disabled>Reset</button>
                                            <button type="submit" class="btn btn-xs btn-success" disabled>Submit</button>
                                        </div>
                                    </div>
                                    <div id="result" class="border rounded bg-dark py-1 px-2"></div>
                                    {{-- <textarea id="result" class="form-control bg-dark" rows="15" disabled></textarea> --}}
                                </div>
                            </div>
                            <div class="col-md-7">
                                <embed id="guide" class="w-100 h-100" src="http://maman.sql.man//upload/62c02fe9312e5.pdf" type="application/pdf">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
