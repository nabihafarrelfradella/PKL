@extends('Master.Layouts.app', ['title' => $title])

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Merk Barang</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item text-gray">Master Barang</li>
                <li class="breadcrumb-item active" aria-current="page">Merk Barang</li>
            </ol>
        </div>
        @if ($hakTambah > 0)
        <div class="ms-auto">
            <a class="modal-effect btn btn-primary" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">
                <i class="fe fe-plus me-1"></i> Tambah Data
            </a>
        </div>
        @endif
    </div>
    <!-- PAGE-HEADER END -->


    <!-- ROW -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h3 class="card-title">Data</h3>
                </div>
                <div class="card-body">
                    <!-- Search/Filter Bar Template (injected via DataTables DOM) -->
                    <div id="custom-search-html" style="display: none;">
                        <div class="d-flex align-items-center w-100">
                            <div class="input-group input-group-sm w-100" style="min-width: 250px;">
                                <input type="text" id="merkSearchInput" class="form-control" placeholder="Pencarian...">
                                <button class="btn btn-primary" onclick="doSearchMerk()"><i class="fe fe-search"></i></button>
                                <button class="btn btn-light border" onclick="resetSearchMerk()"><i class="fe fe-x"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="w-100">
                        <table id="table-1" width="100%"
                            class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                            <thead>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Merk</th>
                                <th class="border-bottom-0">Keterangan</th>
                                <th class="border-bottom-0" width="1%">Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW -->

    @include('Admin.Merk.tambah')
    @include('Admin.Merk.edit')
    @include('Admin.Merk.hapus')

    <script>
        function update(data) {
            $("input[name='idmerkU']").val(data.merk_id);
            $("input[name='merkU']").val(data.merk_nama.replace(/_/g, ' '));
            $("textarea[name='ketU']").val(data.merk_keterangan.replace(/_/g, ' '));
        }

        function hapus(data) {
            $("input[name='idmerk']").val(data.merk_id);
            $("#vmerk").html("merk " + "<b>" + data.merk_nama.replace(/_/g, ' ') + "</b>");
        }

        function validasi(judul, status) {
            swal({
                title: judul,
                type: status,
                confirmButtonText: "Iya."
            });
        }
        function doSearchMerk() {
            var val = $('#merkSearchInput').val();
            table.search(val).draw();
        }
        function resetSearchMerk() {
            $('#merkSearchInput').val('');
            table.search('').draw();
        }
    </script>
@endsection

@section('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table;
        $(document).ready(function() {
            //datatables
            table = $('#table-1').DataTable({

                "processing": true,
                "serverSide": true,
                "info": true,
                "order": [],
                "stateSave": true,
                "lengthMenu": [
                    [5, 10, 25, 50, 100],
                    [5, 10, 25, 50, 100]
                ],
                "pageLength": 10,
                lengthChange: true,
                "language": {
                    "lengthMenu": "Show _MENU_"
                },
                "dom": "<'row mb-2'<'col-12 d-flex flex-nowrap justify-content-between align-items-center gap-2'l<'#custom-search-container.flex-grow-1.ms-auto'>>>" +
                       "<'row'<'col-sm-12 table-responsive'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "initComplete": function() {
                    $('#custom-search-container').html($('#custom-search-html').html());
                    $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity, width: '55px' });
                    $('#custom-search-container').find('#merkSearchInput').on('keypress', function(e) {
                        if (e.which === 13) doSearchMerk();
                    });
                },

                "ajax": {
                    "url": "{{ route('merk.getmerk') }}",
                },

                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'merk_nama',
                        name: 'merk_nama',
                    },
                    {
                        data: 'ket',
                        name: 'merk_keterangan',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],

            });
        });
    </script>
@endsection
