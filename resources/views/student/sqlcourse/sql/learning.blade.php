@extends('student/home')
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
            const data = $('tbody').html();
            $.ajax({
                type: "GET",
                url: "{{ route('student sql learning read') }}",
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
                        let html = '<tr><td class="align-middle text-center">' + (index + 1) + '</td><td class=align-middle>' + value.name + '</td><td class="align-middle text-center">';
                        switch (value.status) {
                            case 'lulus':
                                html += '<span class="badge badge-success">' + value.status + '</span>'
                                break;

                            case 'sedang dikerjakan':
                                html += '<span class="badge badge-info">' + value.status + '</span>'
                                break;

                            case 'gagal':
                                html += '<span class="badge badge-danger">' + value.status + '</span>'
                                break;

                            default:
                                html += '<span class="badge badge-secondary">' + value.status + '</span>'
                                break;
                        }

                        html += '</td>';
                        let url = '{{ route('student sql learning do', ':id') }}'
                        url = url.replace(":id", value.id);
                        html += '<td class="align-middle text-center"><div class="btn-group"><a class="btn btn-sm btn-success" href="' + url + '"><i class="fa fa fa-hand-pointer"></i></a></div></td>'
                        html += '</tr>'
                        $('tbody').append(html);
                    });

                    reloads();
                    resets();
                }
            });
        }

        const reloads = () => {
            $('[data-action=reload]').unbind('click');
            $('[data-action=reload]').on('click', function() {
                loads();
            });
        }

        const resets = () => {
            $('[data-action=reset]').unbind('click');
            $('[data-action=reset]').on('click', function() {
                Swal.fire({
                    title: 'Reset Pembelajaran',
                    text: "pembelajaran yang telah direset tidak dapat dikembalikan, harap pastikan bahwa anda benar-benar yakin!",
                    type: 'warning',
                    confirmButtonText: 'Saya Yakin!',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true,
                    preConfirm: () => {
                        return fetch('{{ route('student sql learning do reset') }}')
                            .then(response => {
                                return response;
                            })
                    },
                }).then((result) => {
                    if (typeof result.dismiss == 'undefined') {
                        if (result.value.ok) {
                            Swal.fire({
                                title: 'Reset Pembelajaran',
                                text: "pembelajaran berhasil direset",
                                type: 'success',
                                confirmButtonText: 'Mantap!',
                            }).then(() => {
                                loads();
                            })
                        } else {
                            Swal.fire({
                                title: 'Reset Pembelajaran',
                                text: "pembelajaran gagal direset",
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
    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modul Pembelajaran SQL</h3>
                    <div class="card-tools">
                        <button class="btn btn-tool" data-action="reload">
                            <i class="fa fa-sync"></i>
                            &nbsp; Refresh
                        </button>
                        <button class="btn btn-tool" data-action="reset">
                            <i class="fa fa-bolt"></i>
                            &nbsp; Reset Pembelajaran
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
                                        <th>Status</th>
                                        <th>Solve</th>
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
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
