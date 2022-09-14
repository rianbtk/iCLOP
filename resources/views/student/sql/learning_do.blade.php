@extends('student/home')
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/codemirror/theme/dracula.css') }}">
@endsection
@section('js')
    <script src="{{ asset('lte/plugins/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('lte/plugins/codemirror/mode/sql/sql.js') }}"></script>
    <script>
        $(document).ready(function() {
            CodeMirror.fromTextArea($('textarea#syntax')[0], {
                lineNumbers: true,
                styleActiveLine: true,
                mode: "sql",
                theme: "dracula"
            });

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
                        $('form div#result-text').html('<span class="badge badge-success align-middle">' + lulus + ' Lulus</span> <span class="badge badge-danger align-middle">' + gagal + ' Gagal</span>');
                        if (response.status == 'lulus') {
                            $('form button[type=submit]').remove();
                            $('form div#result-action').html('<span class="badge badge-success align-middle">Lulus</span>');
                        }
                    }
                });
            });
        });
    </script>
@endsection
@section('content')
    @isset($data)
        <div class="row">
            <div class="col-12">
                <form method="POST" action="javascrip:void(0)" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-8 align-self-center">
                                    <h3 class="card-title">{{ $data['name'] }}</h3>
                                </div>
                                <div class="col-4 text-right">
                                    @if ($data['previous'])
                                        <a href="{{ route('student sql learning do', $data['previous']) }}" class="btn btn-outline-secondary">Sebelumnya</a>
                                    @endif
                                    @if ($data['next'])
                                        <a href="{{ route('student sql learning do', $data['next']) }}" class="btn btn-outline-primary">Selanjutnya</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <textarea name="syntax" id="syntax" class="d-none">{{ isset($data['user']['input'][0]) ? str_replace('pembelajaran_mahasiswa_' . Auth::user()->id . '_', '', $data['user']['input'][0]['syntax']) : '' }}</textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <div class="mb-2 d-flex justify-content-between">
                                            @php
                                                $result = isset($data['user']['input'][0]) ? json_decode($data['user']['input'][0]['result'], true) : [];
                                                $html = '';
                                                $lulus = 0;
                                                $gagal = 0;
                                            @endphp
                                            @foreach ($result as $key => $value)
                                                @if (is_array($value))
                                                    @if ($value['status'] != 'lulus')
                                                        @php
                                                            $html .= '<span class="my-1 badge badge-danger d-block text-left">' . $value['message'] . '</span>';
                                                            $gagal++;
                                                        @endphp
                                                    @else
                                                        @php
                                                            $html .= '<span class="my-1 badge badge-success d-block text-left">' . $value['message'] . '</span>';
                                                            $lulus++;
                                                        @endphp
                                                    @endif
                                                @endif
                                            @endforeach


                                            <div class="d-block" id="result-text">
                                                @if ($html != '')
                                                    <span class="badge badge-success align-middle">{{ $lulus }} Lulus</span>
                                                    <span class="badge badge-danger align-middle">{{ $gagal }} Gagal</span>
                                                @else
                                                    <h5 class="m-0 align-self-center">Hasil</h5>
                                                @endif
                                            </div>
                                            <div class="d-block" id="result-action">
                                                @if ($data['user']['status'] != 'lulus')
                                                    <button type="submit" class="btn btn-xs btn-success">Submit</button>
                                                @else
                                                    <span class="badge badge-success align-middle">Lulus</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div id="result" class="border rounded bg-dark py-1 px-2"> {!! $html !!} </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <embed id="guide" class="w-100 h-100" src="{{ asset($data['file']) }}" type="application/pdf">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endisset
@endsection
