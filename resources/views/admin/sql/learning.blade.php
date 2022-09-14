@extends('admin/admin')
@section('css')
    <link rel="stylesheet" href="{{ asset('lte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/codemirror/theme/dracula.css') }}">
    <link href='https://fonts.googleapis.com/css?family=Courier Prime' rel='stylesheet'>
@endsection
@section('js')
    <script src="{{ asset('lte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('lte/plugins/codemirror/mode/sql/sql.js') }}"></script>
    <script>
        const formContent = $('form .modal-body').html();
        var CM;

        $(document).ready(function() {
            loads();
        });

        const loads = () => {
            const data = $('tbody').html();
            $.ajax({
                type: "GET",
                url: "{{ route('admin sql learning read') }}",
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
                        $('#content tbody').append('<tr><td class="align-middle text-center">' + (index + 1) + '</td><td class="align-middle">' + value.name + '</td><td class="align-middle text-center"><div class="btn-group"><button class="btn btn-sm btn-primary" data-action="syntax" data-syntax="' + value.syntax.replaceAll('"', "'") + '"><i class="fa fa-eye mr-1"></i>Tinjau</button></div></td><td class="align-middle text-center"><div class="btn-group"><button class="btn btn-sm btn-primary" data-action="file" data-file="' + value.file + '"><i class="fa fa-eye mr-1"></i>Tinjau</button></div></td><td class="align-middle text-center"><div class="btn-group"><button class="btn btn-sm btn-success mr-1" data-action="update" data-id="' + value.id + '"><i class="fa fa-pen"></i></button></div><div class="btn-group"><button class="btn btn-sm btn-danger" data-action="delete" data-id="' + value.id + '"><i class="fa fa-trash"></i></button></div></td></tr>');
                    });

                    reloads();
                    creates();
                    updates();
                    deletes();

                    fileView();
                    syntaxView();
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

                    $('#formModalLabel').html('Tambah Modul Pembelajaran');
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
                let url = '{{ route('admin sql learning detail', ':id') }}';
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
                        $('#formModal').unbind('shown.bs.modal');
                        $('#formModal').on('shown.bs.modal', function() {
                            $('form .modal-body').html(formContent);
                            $('form button[type=submit]').attr('data-id', id);

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

                            $('#formModalLabel').html('Ubah Modul Pembelajaran');
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
                let url = '{{ route('admin sql learning delete', ':id') }}';
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

            CodeMirror.fromTextArea($('form textarea')[0], {
                lineNumbers: true,
                styleActiveLine: true,
                mode: "sql",
                theme: "dracula",
            });
        }

        const formSubmit = () => {
            let url;
            switch ($('#formModalLabel').html()) {
                case 'Tambah Modul Pembelajaran':
                    url = '{{ route('admin sql learning create') }}'
                    break;
                case 'Ubah Modul Pembelajaran':
                    let id = $('form button[type=submit]').attr('data-id');
                    url = '{{ route('admin sql learning update', ':id') }}'
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
                                    title: $('#formModalLabel').html().replace(' Modul Pembelajaran', '') + ' Data Berhasil',
                                    text: "data berhasil di" + $('#formModalLabel').html().replace(' Modul Pembelajaran', '').toLowerCase() + " kedalam database",
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

        const fileView = () => {
            $('button[data-action=file]').unbind('click');
            $('button[data-action=file]').on('click', function() {
                let file = $(this).attr('data-file');
                if (file) {
                    $('#fileModal embed').prop('src', '{{ asset('') }}' + file);
                    $('#fileModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    })
                } else {
                    Swal.fire({
                        title: 'File Tidak Ditemukan',
                        text: "file yang anda maksud tidak ditemukan",
                        type: 'error',
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        loads();
                    })
                }
            });
        }

        const syntaxView = () => {
            $('button[data-action=syntax]').unbind('click');
            $('button[data-action=syntax]').on('click', function() {
                let syntax = $(this).attr('data-syntax');
                if (syntax) {
                    $('#syntaxModal').on('shown.bs.modal', function() {
                        $('#syntaxModal .modal-body').html('<textarea class="d-none"></textarea>');
                        $('#syntaxModal .modal-body textarea').html(syntax);
                        CodeMirror.fromTextArea($('#syntaxModal .modal-body textarea')[0], {
                            lineNumbers: true,
                            styleActiveLine: true,
                            mode: "sql",
                            theme: "dracula",
                            readOnly: true,
                        });
                    });

                    $('#syntaxModal').on('hidden.bs.modal', function() {
                        $('#syntaxModal .modal-body').html('');
                    });

                    $('#syntaxModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    })
                } else {
                    Swal.fire({
                        title: 'Sintak Tidak Ditemukan',
                        text: "syntax yang anda maksud tidak ditemukan",
                        type: 'error',
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        loads();
                    })
                }
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
                        url: "{{ route('admin sql learning log read') }}",
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

                            $.each(response, function(index, value) {
                                $('#logModal tbody').html('<tr><td class="text-center">' + (index + 1) + '</td><td>' + value.name + '</td><td class="text-center"><button class="btn btn-sm btn-primary" data-id="' + value.id + '" data-action="download"><i class="fa fa-download mr-1"></i>Unduh</button></td></tr>');
                            });

                            download();
                        }
                    });
                });
                $('#logModal').modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });
        }

        const download = () => {
            $('button[data-action=download]').unbind('click');
            $('button[data-action=download]').on('click', function() {
                let id = $(this).attr('data-id');
                let url = '{{ route('admin sql learning log detail', ':id') }}';
                url = url.replace(":id", id);
                $.ajax({
                    type: "GET",
                    url: url,
                    error: function() {
                        console.log('error');
                    },
                    success: function(response) {
                        if (typeof response != 'undefined' && response.length != 0) {
                            let filename = response[0].user.name.replaceAll(" ", "_") + '.txt';
                            let text = '';
                            $.each(response, function(index, value) {
                                if (value.input.length != 0) {
                                    $.each(value.input, function(indexs, values) {
                                        text += values.syntax + '\n';
                                    });
                                }
                            });
                            downloadFile(filename, text);
                        }
                    }
                });
            });
        }

        const downloadFile = (filename, text) => {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }
    </script>
@endsection
@section('content')
    <div class="row" id="content">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modul Pembelajaran SQL</h3>
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
                            <i class="fa fa-sitemap"></i>
                            &nbsp; Log
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
                                        <th>Name</th>
                                        <th>Sintak Tes</th>
                                        <th>Dokumen Penuntun</th>
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

    <form action="javascript:void(0)" enctype="multipart/form-data">
        <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4 d-none" id="error-message"></div>
                        <div class="modal-body">
                            <div class="row">
                              <div class="col-md-5">
                                <div class="form-group">
                                    <label for="name">Nama Modul <code>*</code></label>
                                    <input type="text" class="form-control" name="name" id="name" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="syntax">Sintak Tes <code>*</code></label>
                                    <textarea name="syntax" id="syntax" class="d-none"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="file">Dokumen Pendukung <code>*</code></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file" id="file" accept="application/pdf" />
                                            <label class="custom-file-label" for="file">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-7" style="background-color: Black;">
                                <label for="template" style="color:White;">Template</label>
                                <table class="table table-bordered table-hover" style="color: White;font-family: 'Courier Prime';font-size:10px;">
                                    <tr style="color:White;">
                                      <th>Name</th>
                                      <th>Syntax</th>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Start a transaction</td>
                                      <td>BEGIN;</td>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Plan the tests and Run the tests</td>
                                      <td>SELECT tap.plan(1);</td>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Untuk mengidentifikasi database</td>
                                      <td>SELECT has_schema('nama_database', 'deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Untuk mengidentifikasi tabel</td>
                                      <td>SELECT has_table('nama_database', 'nama_tabel', 'deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Untuk mengidentifikasi kolom tabel</td>
                                      <td>SELECT has_column('nama_database', 'nama_tabel','nama_column','deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Untuk mengidentifikasi primary key</td>
                                      <td>SELECT col_has_primary_key('nama_database', 'nama_tabel','nama_column','deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                      <td>Untuk mengidentifikasi constraint FK</td>
                                      <td>SELECT has_constraint('nama_database','nama_tabel','nama_kolom','deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Untuk mengidentifikasi type tabel</td>
                                        <td>SELECT col_has_type('nama_database', 'nama_tabel', 'nama_column', 'type_data', 'deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Untuk mengidentifikasi null</td>
                                        <td>SELECT col_is_null('nama_database', 'nama_tabel', 'nama_kolom', 'deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Untuk mengidentifikasi not null</td>
                                        <td>SELECT col_not_null('nama_database', 'nama_tabel', 'nama_kolom', 'deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Untuk mengidentifikasi unique</td>
                                        <td>SELECT col_is_unique('nama_database','nama_tabel', 'nama_kolom','deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Untuk mengidentifikasi constraint default</td>
                                        <td>SELECT col_has_default('nama_database','nama_tabel', 'nama_kolom_default','deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Untuk mengidentifikasi isi dari default</td>
                                        <td>SELECT col_default_is('nama_database', 'nama_tabel', 'nama_kolom', 'nilai_default ex:’5’', 'deskripsi');</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>Finish the tests.</td>
                                        <td>CALL tap.finish();</td>
                                    </tr>
                                    <tr style="color:White;">
                                        <td>clean up.</td>
                                        <td>ROLLBACK;</td>
                                    </tr>
                                  </table>
                              </div>
                            </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Pratinjau Dokumen Penuntun</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <embed src="" type="application/pdf" style="width: 100%; height: 84vh;">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="syntaxModal" tabindex="-1" role="dialog" aria-labelledby="syntaxModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="syntaxModalLabel">Pratinjau Sintaks Testing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logModalLabel">Log Sintaks Testing</h5>
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
                                <th>Unduh Sintaks</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- <tr>
                                <td>1</td>
                                <td>Kuda</td>
                                <td class="text-centere">
                                    <button class="btn btn-sm btn-primary" data-action="download"><i class="fa fa-eye mr-1"></i>Tinjau</button>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
