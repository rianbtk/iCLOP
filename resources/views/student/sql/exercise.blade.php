@extends('student/home')
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection
@section('js')
    <script src="{{ asset('lte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            loads();
        });

        const loads = () => {
            const data = $('tbody').html();
            $.ajax({
                type: "GET",
                url: "{{ route('student sql exercise read') }}",
                beforeSend: function() {
                    $('tbody').html('');
                },
                error: function() {
                    console.log('error');
                },
                success: function(response) {
                    if (response.length == 0) {
                        $('tbody').append('<tr><td colspan="4" class="align-middle text-center text-muted">tidak ada data</td></tr>');
                    }

                    $.each(response, function(index, value) {
                        $('tbody').append('<tr><td class="align-middle text-center">' + (index + 1) + '</td><td class="align-middle text-center">' + value.nilai + '</td><td class="align-middle text-center"><div class="btn-group"><button class="btn btn-sm btn-primary" data-action="result" data-id="' + value.id + '"><i class="fa fa-eye"></i></button></div></td></tr>');
                    });

                    reloads();
                    starts();
                    results();
                }
            });
        }

        const reloads = () => {
            $('[data-action=reload]').unbind('click');
            $('[data-action=reload]').on('click', function() {
                loads();
            });
        }

        const starts = () => {
            $('[data-action=start]').unbind('click');
            $('[data-action=start]').on('click', function() {
                Swal.fire({
                    title: 'Mulai Latihan',
                    text: "harap pastikan bahwa anda benar-benar yakin!",
                    type: 'info',
                    confirmButtonText: 'Saya Yakin!',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true
                }).then((result) => {
                    if (result.value) {
                        window.location.replace('{{ route('student sql exercise start') }}');
                    }
                })
            });
        }

        const results = () => {
            $('[data-action=result]').unbind('click');
            $('[data-action=result]').on('click', function() {
                let id = $(this).attr('data-id');
                $('#resultModal').unbind('shown.bs.modal');
                $('#resultModal').on('shown.bs.modal', function() {
                    let url = '{{ route('student sql exercise do detail', ':id') }}';
                    url = url.replace(":id", id);
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response) {
                            $('#resultModal .modal-body').html('');
                            if (response) {
                                $.each(response, function(index, value) {
                                    let html = '';
                                    if (value.input) {
                                        if (value.input == value.correct) {
                                            html += '<blockquote class="quote-success m-1"><p class="mb-1">' + value.soal + '</p><div class="form-group mb-0">'
                                            for (let i = 1; i <= 4; i++) {
                                                if (value.correct == i) {
                                                    html += '<small class="d-block text-success">' + value['jawaban_' + i] + '</small>'
                                                } else {
                                                    html += '<small class="d-block">' + value['jawaban_' + i] + '</small>'
                                                }
                                            }
                                            html += '</div></blockquote>'
                                        } else {
                                            html += '<blockquote class="quote-danger m-1"><p class="mb-1">' + value.soal + '</p><div class="form-group mb-0">'
                                            for (let i = 1; i <= 4; i++) {
                                                if (value.input == i) {
                                                    html += '<small class="d-block text-danger">' + value['jawaban_' + i] + '</small>'
                                                } else if (value.correct == i) {
                                                    html += '<small class="d-block text-success">' + value['jawaban_' + i] + '</small>'
                                                } else {
                                                    html += '<small class="d-block">' + value['jawaban_' + i] + '</small>'
                                                }
                                            }
                                            html += '</div></blockquote>'
                                        }
                                    } else {
                                        html += '<blockquote class="quote-secondary m-1"><p class="mb-1">' + value.soal + '</p><div class="form-group mb-0">'
                                        for (let i = 1; i <= 4; i++) {
                                            if (value.correct == i) {
                                                html += '<small class="d-block text-success">' + value['jawaban_' + i] + '</small>'
                                            } else {
                                                html += '<small class="d-block">' + value['jawaban_' + i] + '</small>'
                                            }
                                        }
                                        html += '</div></blockquote>'
                                    }
                                    $('#resultModal .modal-body').append(html);
                                });
                            }
                        }
                    });
                });

                $('#resultModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });
        }
    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hasil Latihan SQL</h3>
                    <div class="card-tools">
                        <button class="btn btn-tool" data-action="reload">
                            <i class="fa fa-sync"></i>
                            &nbsp; Refresh
                        </button>
                        <button class="btn btn-tool" data-action="start">
                            <i class="fa fa-award"></i>
                            &nbsp; Mulai Latihan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>NO</th>
                                        <th>Nilai</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Pratinjau Hasil Latihan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
