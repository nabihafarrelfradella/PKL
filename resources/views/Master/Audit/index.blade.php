@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Audit Trail</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Master Data</li>
            <li class="breadcrumb-item text-gray">User Management</li>
            <li class="breadcrumb-item active" aria-current="page">Audit Trail</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<!-- ROW -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Log Aktivitas Sistem</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-audit" width="100%" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Timestamp</th>
                                <th class="border-bottom-0">User</th>
                                <th class="border-bottom-0">Activity</th>
                                <th class="border-bottom-0">Module</th>
                                <th class="border-bottom-0">Details</th>
                                <th class="border-bottom-0">IP Address</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END ROW -->

@endsection

@section('scripts')
<script>
    var tableAudit;
    $(document).ready(function() {
        //datatables
        tableAudit = $('#table-audit').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [[1, 'desc']], // Sort by timestamp by default
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "pageLength": 10,
            "lengthChange": true,
            "ajax": {
                "url": "{{ url('/admin/audit/show') }}",
            },
            "columns": [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'created_at',
                    name: 'tbl_audit_log.created_at',
                },
                {
                    data: 'user',
                    name: 'tbl_user.user_nmlengkap',
                },
                {
                    data: 'badge_activity',
                    name: 'activity',
                    orderable: false
                },
                {
                    data: 'module',
                    name: 'module',
                },
                {
                    data: 'details',
                    name: 'details',
                },
                {
                    data: 'ip_address',
                    name: 'ip_address',
                },
            ],
        });
    });
</script>
@endsection
