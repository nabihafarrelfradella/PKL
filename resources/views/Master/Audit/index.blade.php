@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Audit Trail</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Manajemen Pengguna</li>
            <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Log Aktivitas Pengguna</h3>
            </div>
            <div class="card-body">
                <!-- Search/Filter Bar Template (injected via DataTables DOM) -->
                <div id="custom-search-html" style="display: none;">
                    <div class="d-flex align-items-center w-100">
                        <div class="input-group input-group-sm w-100">
                            <input type="text" id="auditSearchInput" class="form-control" placeholder="Pencarian...">
                            <button class="btn btn-primary" onclick="doSearchAudit()"><i class="fe fe-search"></i></button>
                            <button class="btn btn-light border" onclick="resetSearchAudit()"><i class="fe fe-x"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="table-1" width="100%" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%"></th>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Waktu</th>
                                <th class="border-bottom-0">Pengguna & Role</th>
                                <th class="border-bottom-0">Modul</th>
                                <th class="border-bottom-0">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->
@endsection

@section('scripts')
<style>
    .btn-expand-bk {
        width: 28px;
        height: 28px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        margin: 0 auto; /* Center button to avoid getting cut off */
    }
    
    .btn-expand-bk.expanded {
        transform: rotate(90deg);
        background-color: #e8f0fe;
        border-color: #4a6cf7;
        color: #4a6cf7;
    }
    
    .child-row-table {
        background: #f8faff;
        border-radius: 8px;
        padding: 10px 16px;
        margin: 4px 0;
        border: 1px solid #e8f0fe;
    }
</style>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        getData();
    });

    var auditTable;
    var auditSearchTerm = '';
    function getData() {
        auditTable = $('#table-1').DataTable({
            processing: true,
            serverSide: true,
            info: true,
            searching: false, // Matikan pencarian bawaan
            responsive: false, // Matikan responsive bawaan DataTables
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            lengthChange: true,
            language: {
                lengthMenu: "Show _MENU_"
            },
            dom: "<'row mb-2'<'col-12 d-flex flex-nowrap justify-content-between align-items-center gap-2'l<'#custom-search-container.flex-grow-1.ms-auto'>>>" +
                 "<'row'<'col-sm-12 table-responsive'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            initComplete: function() {
                $('#custom-search-container').html($('#custom-search-html').html());
                
                // Initialize select2 for length menu
                $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity, width: '55px' });
                
                // Re-bind enter key
                $('#custom-search-container').find('#auditSearchInput').on('keypress', function(e) {
                    if (e.which === 13) doSearchAudit();
                });
            },
            ajax: {
                url: "{{ url('/admin/audit/show') }}",
                type: "GET",
                data: function(d) {
                    d.search_term = auditSearchTerm;
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return '<button class="btn btn-sm btn-light btn-expand-bk" title="Lihat Detail"><i class="fe fe-chevron-right"></i></button>';
                    }
                },
                {
                    data: null,
                    sortable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'user_info', name: 'user_info'},
                {data: 'module', name: 'module'},
                {data: 'activity', name: 'activity'},
            ],
            order: [[2, 'desc']]
        });

        // Event click untuk tombol expand
        $('#table-1 tbody').off('click', '.btn-expand-bk').on('click', '.btn-expand-bk', function(e) {
            e.stopPropagation();
            var btn = $(this);
            var tr = btn.closest('tr');
            var row = auditTable.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                btn.removeClass('expanded');
            } else {
                var detailHtml = row.data().details ? row.data().details : '-';
                var childContent = '<div class="child-row-table"><strong>Detail Aktivitas:</strong><br>' + detailHtml + '</div>';
                row.child(childContent).show();
                btn.addClass('expanded');
            }
        });
    }

    function doSearchAudit() {
        auditSearchTerm = $('#custom-search-container').find('#auditSearchInput').val().trim();
        auditTable.ajax.reload();
    }

    function resetSearchAudit() {
        auditSearchTerm = '';
        $('#custom-search-container').find('#auditSearchInput').val('');
        auditTable.ajax.reload();
    }
</script>
@endsection
