<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function index()
    {
        $data['title'] = 'Audit Trail';
        return view('Master.Audit.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            // Get all logs, joined with tbl_user and tbl_role
            $query = DB::table('tbl_audit_log')
                ->leftJoin('tbl_user', 'tbl_user.user_id', '=', 'tbl_audit_log.user_id')
                ->leftJoin('tbl_role', 'tbl_role.role_slug', '=', 'tbl_audit_log.role_slug')
                ->select(
                    'tbl_audit_log.*',
                    'tbl_user.user_nmlengkap',
                    'tbl_role.role_title'
                )
                ->orderBy('tbl_audit_log.created_at', 'desc');

            $searchTerm = $request->search_term;
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('tbl_user.user_nmlengkap', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tbl_role.role_title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tbl_audit_log.activity', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tbl_audit_log.module', 'LIKE', "%{$searchTerm}%");
                });
            }

            return DataTables::of($query)
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? Carbon::parse($row->created_at)->format('d M Y H:i:s') : '-';
                })
                ->addColumn('user_info', function ($row) {
                    $name = $row->user_nmlengkap ?? 'System';
                    $role = $row->role_title ?? $row->role_slug;
                    return "<b>{$name}</b><br><small class='text-muted'>{$role}</small>";
                })
                ->rawColumns(['user_info'])
                ->make(true);
        }
    }
}
