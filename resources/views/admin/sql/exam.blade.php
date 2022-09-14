@extends('admin/admin')
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection
@section('js')
    <script src="{{ asset('lte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        const formContent = $('form .modal-body').html();
        var CM;

        $(document).ready(function() {
            loads();
        });

        const loads = () => {
            const data = $('#content tbody').html();
            $.ajax({
                type: "GET",
                url: "{{ route('admin sql exam read') }}",
                beforeSend: function() {
                    $('#content tbody').html('');
                },
                error: function() {
                    console.log('error');
                },
                success: function(response) {
                    if (response.length == 0) {
                        $('#content tbody').append('<tr><td colspan="4" class="align-middle text-center text-muted">tidak ada data</td></tr>');
                    }

                    $.each(response, function(index, value) {
                        $('#content tbody').append('<tr><td class="align-middle text-center">' + (index + 1) + '</td><td class="align-middle text-center">' + value.question + '</td><td class="align-middle text-center"><div class="btn-group"><button class="btn btn-sm btn-primary mx-1" data-action="detail" data-id="' + value.id + '"><i class="fa fa-eye"></i></button></div><div class="btn-group"><button class="btn btn-sm btn-success mx-1" data-action="update" data-id="' + value.id + '"><i class="fa fa-pen"></i></button></div><div class="btn-group"><button class="btn btn-sm btn-danger mx-1" data-action="delete" data-id="' + value.id + '"><i class="fa fa-trash"></i></button></div></td></tr>');
                    });

                    reloads();
                    creates();
                    updates();
                    deletes();
                    details();

                    logView();
                }
            });
        }

        const reloads = () => {
            $('[data-action=reload]').unbind('click');
            $('[data-action=reload]').on('click', function() {
                loads();
            });
        }

        const creates = () => {
            $('[data-action=create]').unbind('click');
            $('[data-action=create]').on('click', function() {
                $('#formModal').on('shown.bs.modal', function() {
                    $('form .modal-body').html(formContent);

                    $('#formModalLabel').html('Tambah Soal Latihan');
                    formElement();
                    formSubmit();
                });

                $('#formModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });
        }

        const updates = () => {
            $('[data-action=update]').unbind('click');
            $('[data-action=update]').on('click', function() {
                let id = $(this).attr('data-id');
                let url = '{{ route('admin sql exam detail', ':id') }}';
                url = url.replace(":id", id);

                $.ajax({
                    type: "GET",
                    url: url,
                    beforeSend: function() {
                        $('button[data-action]').attr('disabled', true);
                    },
                    complete: function() {
                        $('button[data-action]').removeAttr('disabled');
                    },
                    error: function() {
                        $('button[data-action]').removeAttr('disabled');

                        Swal.fire({
                            title: 'Data Tidak Ditemukan',
                            text: "data yang anda maksud tidak ada di database",
                            type: 'error',
                            confirmButtonText: 'Tutup',
                        }).then(() => {
                            loads();
                        })
                    },
                    success: function(response) {
                        $('#formModal').on('shown.bs.modal', function() {
                            $('form .modal-body').html(formContent);
                            $.each(response, function(index, value) {
                                if ($('form [name=' + index + ']').length) {
                                    switch ($('form [name=' + index + ']').prop("tagName")) {
                                        case 'TEXTAREA':
                                            $('form [name=' + index + ']').html(value)
                                            break;

                                        case 'INPUT':
                                            if ($('form [name=' + index + ']').prop("type") != 'file') {
                                                $('form [name=' + index + ']').val(value);
                                            }
                                            break;

                                        default:
                                            break;
                                    }
                                }
                            });
                            $('form button[type=submit]').attr('data-id', id);

                            $('#formModalLabel').html('Ubah Soal Latihan');
                            formElement();
                            formSubmit();
                        });

                        $('#formModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    }
                });
            });
        }

        const deletes = () => {
            $('[data-action=delete]').unbind('click');
            $('[data-action=delete]').on('click', function() {
                let id = $(this).attr('data-id');
                let url = '{{ route('admin sql exam delete', ':id') }}';
                url = url.replace(':id', id);

                Swal.fire({
                    title: 'Hapus Data',
                    text: "data yang telah dihapus tidak dapat dikembalikan, harap pastikan bahwa anda benar-benar yakin!",
                    type: 'warning',
                    confirmButtonText: 'Saya Yakin!',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true,
                    preConfirm: () => {
                        return fetch(url)
                            .then(response => {
                                return response;
                            })
                    },
                }).then((result) => {
                    if (typeof result.dismiss == 'undefined') {
                        if (result.value.ok) {
                            Swal.fire({
                                title: 'Hapus Data',
                                text: "data berhasil dihapus",
                                type: 'success',
                                confirmButtonText: 'Mantap!',
                            }).then(() => {
                                loads();
                            })
                        } else {
                            Swal.fire({
                                title: 'Hapus Data',
                                text: "data gagal dihapus",
                                type: 'error',
                                confirmButtonText: 'Tutup',
                            }).then(() => {
                                loads();
                            })
                        }
                    }
                })
            });
        }

        const formElement = () => {
            $('form input[type=file]').unbind('change');
            $('form input[type=file]').on('change', function() {
                $('label.custom-file-label[for=' + $(this).prop('name') + ']').html($(this).val().replace(/.*(\/|\\)/, ''));
            });

            $('form input,form select').unbind('click');
            $('form input,form select').on('click', function() {
                $(this).removeClass('is-invalid');
            });
        }

        const formSubmit = () => {
            let url;
            switch ($('#formModalLabel').html()) {
                case 'Tambah Soal Latihan':
                    url = '{{ route('admin sql exam create') }}'
                    break;
                case 'Ubah Soal Latihan':
                    let id = $('form button[type=submit]').attr('data-id');
                    url = '{{ route('admin sql exam update', ':id') }}'
                    url = url.replace(':id', id);
                    break;

                default:
                    break;
            }

            $('form').unbind('submit');
            $('form').on('submit', function() {
                if (url) {
                    let elm = $(this);
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: new FormData(elm[0]),
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            elm.find('button[type=submit]').html('Tunggu sebentar');
                            elm.find('button').attr('disabled', true);
                            elm.find(':input').attr('disabled', true)
                        },
                        complete: function() {
                            elm.find('button[type=submit]').html('Simpan');
                            elm.find('button').removeAttr('disabled');
                            elm.find(':input').removeAttr('disabled')
                        },
                        error: function() {
                            elm.find('button[type=submit]').html('Simpan');
                            elm.find('button').removeAttr('disabled');
                            elm.find(':input').removeAttr('disabled')
                        },
                        success: function(response) {
                            if (response != 'ok') {
                                let html = '';
                                $.each(response, function(index, value) {
                                    html += '<small class="badge badge-danger d-block text-left my-1"><i class="fa fa-times px-1 mr-1"></i> <span class="border-left px-2">' + value + '</span></small>';
                                    $('input[name=' + index + ']').addClass('is-invalid');
                                });
                                $('form #error-message').html(html)
                                $('form #error-message').addClass('d-block');
                                $('form #error-message').removeClass('d-none');
                            } else {
                                $('#formModal').modal('hide');
                                Swal.fire({
                                    title: $('#formModalLabel').html().replace(' Soal Latihan', '') + ' Data Berhasil',
                                    text: "data berhasil di" + $('#formModalLabel').html().replace(' Soal Latihan', '').toLowerCase() + " kedalam database",
                                    type: 'success',
                                    confirmButtonText: 'Mantap!'
                                }).then(() => {
                                    loads();
                                })
                            }
                        }
                    });
                }
            });
        }

        const details = () => {
            $('button[data-action=detail]').unbind('click');
            $('button[data-action=detail]').on('click', function() {
                let id = $(this).attr('data-id');
                let url = '{{ route('admin sql exam detail', ':id') }}';
                url = url.replace(":id", id);

                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response) {
                        $('#detailModal').on('shown.bs.modal', function() {
                            $.each(response, function(index, value) {
                                if ($('#detailModal textarea[name=' + index + ']').length) {
                                    $('#detailModal textarea[name=' + index + ']').val(value)
                                }
                            });
                        });

                        $('#detailModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    }
                });
            });
        }

        const logView = () => {
            $('button[data-action=log]').unbind('click');
            $('button[data-action=log]').on('click', function() {
                $('#logModal').unbind('shown.bs.modal');
                $('#logModal').on('shown.bs.modal', function() {
                    $('#logModal tbody').html('');

                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin sql exam log read') }}",
                        beforeSend: function() {
                            $('#logModal tbody').html('');
                        },
                        error: function() {
                            console.log('error');
                        },
                        success: function(response) {
                            if (response.length == 0) {
                                $('#logModal tbody').append('<tr><td colspan="3" class="align-middle text-center text-muted">tidak ada data</td></tr>');
                            }

                            console.log(response);
                            $.each(response, function(index, value) {
                                let html = '<tr><td class="text-center">' + (index + 1) + '</td><td>' + value.name + '</td><td class="text-center">';
                                if (value.exam.length != 0) {
                                    $.each(value.exam, function(indexs, values) {
                                        if (values.status == 'selesai') {
                                            html += '<div class="d-flex justify-content-between"><span>Test ' + (indexs + 1) + '</span><span>' + values.nilai + '</span></div>';
                                        }
                                    });
                                }
                                html += '</td></tr>';
                                $('#logModal tbody').html(html);
                            });
                        }
                    });
                });
                $('#logModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });
        }
    </script>
@endsection
@section('content')
    <div class="row" id="content">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Soal Ujian Teori SQL</h3>
                    <div class="card-tools">
                        <button class="btn btn-tool" data-action="reload">
                            <i class="fa fa-sync"></i>
                            &nbsp; Refresh
                        </button>
                        <button class="btn btn-tool" data-action="create">
                            <i class="fa fa-plus"></i>
                            &nbsp; Add
                        </button>
                        <button class="btn btn-tool" data-action="log">
                            <i class="fa fa-award"></i>
                            &nbsp; Nilai
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
                                        <th>Soal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <td colspan="4" class="align-middle text-center text-muted">
                                            tidak ada data
                                        </td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <td class="align-middle text-center">'+(index+1)+'</td>
                                        <td class="align-middle text-center">'+ value.question +'</td>
                                        <td class="align-middle text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary" data-action="detail" data-id="'+value.id+'"><i class="fa fa-eye"></i></button>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-success" data-action="update" data-id="'+value.id+'"><i class="fa fa-pen"></i></button>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-danger" data-action="delete" data-id="'+value.id+'"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="javascript:void(0)" enctype="multipart/form-data">
        <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4 d-none" id="error-message"></div>
                        <div class="form-group">
                            <label for="question">Soal <code>*</code></label>
                            <textarea name="question" id="question" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="answer_1">Jawaban Benar (1) <code>*</code></label>
                            <textarea name="answer_1" id="answer_1" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="answer_2">Jawaban Salah (2) <code>*</code></label>
                            <textarea name="answer_2" id="answer_2" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="answer_3">Jawaban Salah (3) <code>*</code></label>
                            <textarea name="answer_3" id="answer_3" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="answer_4">Jawaban Salah (4) <code>*</code></label>
                            <textarea name="answer_4" id="answer_4" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Soal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="question">Soal</label>
                        <textarea name="question" id="question" class="form-control" rows="4" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="answer_1">Jawaban Benar (1)</label>
                        <textarea name="answer_1" id="answer_1" class="form-control" rows="1" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="answer_2">Jawaban Salah (2)</label>
                        <textarea name="answer_2" id="answer_2" class="form-control" rows="1" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="answer_3">Jawaban Salah (3)</label>
                        <textarea name="answer_3" id="answer_3" class="form-control" rows="1" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="answer_4">Jawaban Salah (4)</label>
                        <textarea name="answer_4" id="answer_4" class="form-control" rows="1" disabled></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logModalLabel">Hasil Nilai</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>NO</th>
                                <th>Name</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
