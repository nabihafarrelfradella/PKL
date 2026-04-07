<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AuditController extends Controller
{
    public function index()
    {
        $data["title"] = "Audit Trail";
        return view('Master.Audit.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_audit_log')
                ->leftJoin('tbl_user', 'tbl_user.user_id', '=', 'tbl_audit_log.user_id')
                ->select('tbl_audit_log.*', 'tbl_user.user_nama', 'tbl_user.user_nmlengkap')
                ->orderBy('tbl_audit_log.created_at', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($row) {
                    return $row->user_nmlengkap . ' (' . $row->user_nama . ')';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d-m-Y H:i:s', strtotime($row->created_at));
                })
                ->addColumn('badge_activity', function ($row) {
                    $class = 'primary';
                    if ($row->activity == 'CREATE') $class = 'success';
                    if ($row->activity == 'UPDATE' || $row->activity == 'UPDATE_PASSWORD') $class = 'warning';
                    if ($row->activity == 'DELETE') $class = 'danger';
                    if ($row->activity == 'UNAUTHORIZED_ACCESS_ATTEMPT') $class = 'dark';
                    
                    return '<span class="badge bg-' . $class . '">' . $row->activity . '</span>';
                })
                ->rawColumns(['badge_activity'])
                ->make(true);
        }
    }
}
