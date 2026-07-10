@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Access Control</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">User Management</li>
            <li class="breadcrumb-item active">Access Control</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

@php
    // Helper sebagai Closure â€” hindari error "Cannot redeclare function" pada Blade
    $hasAccess = function($aksesCollection, $refType, $refId, $aksesType) {
        if (!$refId) return false;
        $key = ($refType === 'menu' ? 'menu_' : 'sub_') . $refId . '_' . $aksesType;
        return $aksesCollection->has($key);
    };
@endphp

<!-- Toast Notification -->
<div id="toastContainer" style="position:fixed;top:20px;right:20px;z-index:9999;"></div>



@php
    $groups = collect($modules)->groupBy('group');
    $typeLabels = ['view' => 'Lihat', 'create' => 'Tambah', 'update' => 'Edit', 'delete' => 'Hapus'];
    $typeBadgeClass = ['view' => 'bg-info', 'create' => 'bg-success', 'update' => 'bg-warning', 'delete' => 'bg-danger'];
@endphp

@foreach($groups as $groupName => $groupModules)
<div class="card mb-3">
    <div class="card-header d-flex align-items-center gap-2 py-2">
        <i class="fe fe-layers text-primary fs-16"></i>
        <h5 class="mb-0 fw-bold text-primary">{{ $groupName }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle" style="font-size:13.5px;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="text-start ps-3" style="width:35%;">Fitur / Modul</th>
                        <th class="text-center" style="width:15%;">Tipe Akses</th>
                        <th class="text-center" style="width:25%;">
                            <span class="badge bg-danger fs-12 px-2 py-1">
                                <i class="fe fe-crown me-1"></i>OWNER
                            </span>
                        </th>
                        <th class="text-center" style="width:25%;">
                            <span class="badge bg-primary fs-12 px-2 py-1">
                                <i class="fe fe-shield me-1"></i>ADMIN GUDANG
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupModules as $modIdx => $mod)
                        @php
                            // Resolve ref_id dari slug
                            if ($mod['ref_type'] === 'menu') {
                                $refObj = $menus->get($mod['ref_slug']);
                                $refId  = $refObj ? $refObj->menu_id : null;
                            } else {
                                $refObj = $submenus->get($mod['ref_slug']);
                                $refId  = $refObj ? $refObj->submenu_id : null;
                            }
                            $typesCount = count($mod['types']);
                        @endphp

                        @foreach($mod['types'] as $typeIdx => $type)
                        <tr class="{{ $typeIdx === 0 ? 'border-top-2' : '' }}">
                            @if($typeIdx === 0)
                            <td class="ps-3 fw-semibold" rowspan="{{ $typesCount }}" style="border-right:2px solid #dee2e6;vertical-align:middle;background:#fafbfc;">
                                {{ $mod['label'] }}
                                @if($refId === null)
                                    <span class="badge bg-secondary ms-1" style="font-size:10px;">tbl kosong</span>
                                @endif
                            </td>
                            @endif

                            {{-- Tipe akses badge --}}
                            <td class="text-center">
                                <span class="badge {{ $typeBadgeClass[$type] }} opacity-75 px-2">
                                    {{ $typeLabels[$type] }}
                                </span>
                            </td>

                            {{-- OWNER â€” selalu aktif, tidak bisa diubah --}}
                            <td class="text-center">
                                <i class="fe fe-check-circle text-success fs-18" title="Owner selalu punya akses penuh"></i>
                                <div style="font-size:10px;color:#aaa;">Terkunci</div>
                            </td>

                            {{-- ADMIN GUDANG (role_id=2) --}}
                            <td class="text-center">
                                @if($refId)
                                    @php $isOn2 = $hasAccess($aksesRole2, $mod['ref_type'], $refId, $type); @endphp
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input rbac-toggle" type="checkbox"
                                                id="toggle_r2_{{ $mod['ref_type'] }}_{{ $refId }}_{{ $type }}"
                                                {{ $isOn2 ? 'checked' : '' }}
                                                data-role-id="2"
                                                data-ref-type="{{ $mod['ref_type'] }}"
                                                data-ref-id="{{ $refId }}"
                                                data-akses-type="{{ $type }}"
                                                style="cursor:pointer;width:42px;height:22px;">
                                        </div>
                                        <span class="toggle-label-r2-{{ $mod['ref_type'] }}_{{ $refId }}_{{ $type }} text-{{ $isOn2 ? 'success' : 'danger' }} fw-semibold" style="font-size:11px;min-width:30px;">
                                            {{ $isOn2 ? 'ON' : 'OFF' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted small">â€”</span>
                                @endif
                            </td>

                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach





@endsection

@section('scripts')
<style>
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    .form-check-input:not(:checked) {
        background-color: #dc3545;
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
    .border-top-2 td {
        border-top: 2px solid #dee2e6 !important;
    }
    .rbac-toggle {
        transition: all 0.2s ease;
    }
    .rbac-toggle:disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
    }
    .toast-rbac {
        min-width: 260px;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { transform: translateX(100px); opacity: 0; }
        to   { transform: translateX(0); opacity: 1; }
    }
</style>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function showToast(msg, type) {
    const color = type === 'success' ? '#28a745' : '#dc3545';
    const icon  = type === 'success' ? 'âœ“' : 'âœ—';
    const id    = 'toast_' + Date.now();
    const html  = `
        <div id="${id}" class="toast-rbac p-3 mb-2 text-white d-flex align-items-center gap-2"
             style="background:${color};border-radius:8px;">
            <span style="font-size:18px;font-weight:bold;">${icon}</span>
            <span>${msg}</span>
        </div>`;
    $('#toastContainer').prepend(html);
    setTimeout(() => $('#' + id).fadeOut(400, function(){ $(this).remove(); }), 3000);
}

$(document).on('change', '.rbac-toggle', function () {
    const $toggle   = $(this);
    const roleId    = $toggle.data('role-id');
    const refType   = $toggle.data('ref-type');
    const refId     = $toggle.data('ref-id');
    const aksesType = $toggle.data('akses-type');
    const enabled   = $toggle.is(':checked');

    // Cari label yang sesuai
    const rolePrefix = 'r' + roleId;
    const labelKey   = `${rolePrefix}-${refType}_${refId}_${aksesType}`;
    const $label     = $(`.toggle-label-${labelKey}`);

    // Disable toggle sementara (menghindari double click)
    $toggle.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: '{{ route("user-mgmt.access-control.toggle") }}',
        data: {
            role_id:    roleId,
            ref_type:   refType,
            ref_id:     refId,
            akses_type: aksesType,
            enabled:    enabled ? 1 : 0,
        },
        success: function (res) {
            $toggle.prop('disabled', false);
            if (enabled) {
                $label.text('ON').removeClass('text-danger').addClass('text-success');
                showToast(res.message, 'success');
            } else {
                $label.text('OFF').removeClass('text-success').addClass('text-danger');
                showToast(res.message, 'success');
            }
        },
        error: function (xhr) {
            // Revert toggle jika gagal
            $toggle.prop('checked', !enabled).prop('disabled', false);
            const msg = xhr.responseJSON?.error || 'Gagal mengubah akses.';
            showToast(msg, 'error');
        }
    });
});
</script>
@endsection
