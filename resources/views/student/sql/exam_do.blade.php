<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>APLAS - Administrator Site</title>
    <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .table td,
        .table th {
            padding: 0.25rem;
        }

        .card-footer {
            background-color: transparent;
        }
    </style>
</head>

<body class="hold-transition layout-top-nav">
    <div class="container-fluid mt-2">
        {{-- <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Soal</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @for ($i = 1; $i <= 18; $i++)
                                <div class="col-2 my-2">
                                    <button type="button" class="btn btn-secondary btn-block">{{ $i }}</button>
                                </div>
                            @endfor
                            <div class="col-2 my-2">
                                <button type="button" class="btn btn-primary btn-block">19</button>
                            </div>
                            <div class="col-2 my-2">
                                <button type="button" class="btn btn-success btn-block">20</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <h3 class="card-title">DDL - Table Creation 1</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="http://maman.sql.man/student/sql/pembelajaran/kerjakan/2" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                                <a href="http://maman.sql.man/student/sql/pembelajaran/kerjakan/4" class="btn btn-sm btn-outline-primary">Selanjutnya</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            Siapakan nama saya?
                        </p>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="customRadio1" name="customRadio">
                                <label for="customRadio1" class="custom-control-label font-weight-normal">Custom Radio</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="customRadio2" name="customRadio">
                                <label for="customRadio2" class="custom-control-label font-weight-normal">Custom Radio checked</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="customRadio3" name="customRadio">
                                <label for="customRadio3" class="custom-control-label font-weight-normal">Custom Radio</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="customRadio4" name="customRadio" checked="">
                                <label for="customRadio4" class="custom-control-label font-weight-normal">Custom Radio checked</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        @isset($data)
            <div class="row">
                @isset($data['semua'])
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                        <div class="card card-secondary card-outline">
                            <div class="card-body p-1">
                                <table class="table table-borderless p-0 m-0">
                                    <tr>
                                        <td colspan="5">
                                            <a href="{{ route('student sql exam complete') }}" class="btn btn-warning btn-block">Selesaikan Ujian</a>
                                        </td>
                                    </tr>
                                    @php($i = 1)
                                    @foreach ($data['semua'] as $number)
                                        @if ($i % 5 == 1)
                                            <tr>
                                        @endif
                                        @if ($number == $data['sekarang'])
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-primary btn-block">{{ $i }}</a>
                                            </td>
                                        @elseif (in_array($number, $data['terjawab']))
                                            <td>
                                                <a href="{{ route('student sql exam do', $number) }}" class="btn btn-success btn-block">{{ $i }}</a>
                                            </td>
                                        @else
                                            <td>
                                                <a href="{{ route('student sql exam do', $number) }}" class="btn btn-secondary btn-block">{{ $i }}</a>
                                            </td>
                                        @endif
                                        @php($i++)
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                @endisset
                @isset($data['soal'])
                    <div class="col-12 col-md-7 col-lg-8 col-xl-9">
                        <form method="POST">
                            @csrf
                            <div class="card card-secondary card-outline">
                                <div class="card-body pt-3 px-3 pb-0">
                                    <p>
                                        {{ $data['soal'] }}
                                    </p>
                                    <div class="form-group">
                                        @isset($data['jawaban'])
                                            @foreach ($data['jawaban'] as $index => $key)
                                                @if (isset($data['input']) && $data['input'] == $index)
                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio" id="answer_{{ $index }}" name="jawaban" value="{{ $key }}" checked>
                                                        <label for="answer_{{ $index }}" class="custom-control-label font-weight-normal">{{ $key }}</label>
                                                    </div>
                                                @else
                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio" id="answer_{{ $index }}" name="jawaban" value="{{ $key }}">
                                                        <label for="answer_{{ $index }}" class="custom-control-label font-weight-normal">{{ $key }}</label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </div>
                                </div>
                                <div class="card-footer p-3 border-top d-block">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan Jawaban</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endisset
            </div>
        @endisset
    </div>
    <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('lte/dist/js/adminlte.min.js') }}"></script>
</body>

</html>
