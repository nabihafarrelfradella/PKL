@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{$title}}</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Master Data</li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
        </ol>
    </div>
    @if($hakTambah > 0)
    <div class="ms-auto">
        <a class="modal-effect btn btn-primary" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">
            <i class="fe fe-plus me-1"></i> Tambah Data
        </a>
    </div>
    @endif
</div>
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
                            <input type="text" id="filterNama" class="form-control" placeholder="Pencarian...">
                            <button class="btn btn-primary" onclick="doFilter()"><i class="fe fe-search"></i></button>
                            <button class="btn btn-light border" onclick="resetFilter()"><i class="fe fe-x"></i></button>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Gambar</th>
                                <th class="border-bottom-0">Kode Barang</th>
                                <th class="border-bottom-0">Nama Barang</th>
                                <th class="border-bottom-0">Merk</th>
                                <th class="border-bottom-0">Jenis</th>
                                {{-- Kolom Tipe sudah dihapus --}}
                                <th class="border-bottom-0">Stok Sekarang</th>
                                <th class="border-bottom-0" width="1%">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('Admin.Barang.tambah', ['jenisbarang' => $jenisbarang, 'merk' => $merk])
@include('Admin.Barang.edit', ['jenisbarang' => $jenisbarang, 'merk' => $merk])
@include('Admin.Barang.hapus')
@include('Admin.Barang.gambar')

<script>
    function generateID(){
        $("input[name='kode']").val("Otomatis");
    }
    function update(data){
        $("input[name='idbarangU']").val(data.barang_id);
        $("input[name='kodeU']").val(data.barang_kode);
        $("input[name='namaU']").val(data.barang_nama.replace(/_/g, ' '));
        
        // Pilih Jenis Barang
        let selectJ = $("select[name='jenisbarangU']");
        let jName = (data.tipe_barang || data.jenisbarang_nama || "").toLowerCase();
        if (jName.includes('kembali')) {
            selectJ.val('bk').trigger('change');
        } else if (jName.includes('habis')) {
            selectJ.val('hp').trigger('change');
        } else {
            selectJ.val(data.jenisbarang_id).trigger('change');
        }

        // Pilih Satuan & Merk
        $("select[name='satuanU']").val(data.satuan_id).trigger('change');
        $("select[name='merkU']").val(data.merk_id).trigger('change');
        
        $("input[name='stokU']").val(data.barang_stok);
        
        if(data.barang_gambar != 'image.png'){
            $("#outputImgU").attr("src", "{{ asset('storage/barang') }}/" + data.barang_gambar);
            $('#btnHapusFotoU').removeClass('d-none');
        } else {
            $("#outputImgU").attr("src", "{{ asset('assets/default/barang/image.png') }}");
            $('#btnHapusFotoU').addClass('d-none');
        }
    }
    function hapus(data) {
        $("input[name='idbarang']").val(data.barang_id);
        $("#vbarang").html("barang " + "<b>" + data.barang_nama.replace(/_/g, ' ') + "</b>");
    }
    function gambar(data) {
        if(data.barang_gambar != 'image.png'){
            $("#outputImgG").attr("src", "{{asset('storage/barang/')}}"+"/"+data.barang_gambar);
        }else{
            $("#outputImgG").attr("src", "{{ asset('assets/default/barang/image.png') }}");
        }
    }
    function validasi(judul, status) {
        swal({
            title: judul,
            type: status,
            confirmButtonText: "Iya"
        });
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
        // DataTables menggunakan route lama kamu
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
            "lengthChange": true,
            "searching": false,
            "language": {
                "lengthMenu": "Show _MENU_"
            },
            "dom": "<'row mb-2'<'col-12 d-flex flex-nowrap justify-content-between align-items-center gap-2'l<'#custom-search-container.flex-grow-1.ms-auto'>>>" +
                   "<'row'<'col-sm-12 table-responsive'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "initComplete": function() {
                $('#custom-search-container').html($('#custom-search-html').html());
                $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity, width: '55px' });
                $('#custom-search-container').find('#filterNama').on('keypress', function(e) {
                    if (e.which === 13) doFilter();
                });
            },
            "ajax": {
                "url": "{{route('barang.getbarang')}}", // KEMBALI KE ROUTE LAMA
                "data": function(d) {
                    d.filter_nama = $('#filterNama').val();
                }
            },
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false , orderable: false },
                { data: 'img', name: 'barang_gambar', searchable: false, orderable: false },
                { data: 'barang_kode', name: 'barang_kode' },
                { data: 'barang_nama', name: 'barang_nama' },
                { data: 'merk', name: 'tbl_merk.merk_nama' },
                { data: 'jenisbarang', name: 'tbl_jenisbarang.jenisbarang_nama' },
                { 
                    data: 'totalstok', 
                    render: function (data, type, row) {
                        let cleanText = String(data).replace(/<[^>]*>?/gm, '').trim();
                        let stok = parseInt(cleanText);
                        
                        let color = "#09ad95"; // default green
                        if (stok < 5) {
                            color = "#e82646"; // red for critically low (< 5)
                        } else if (stok <= 10) {
                            color = "#f7b731"; // yellow/orange for low (5 - 10)
                        }
                        
                        return `<span style="color: ${color} !important; font-weight: bold;">${cleanText}</span>`;
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        // Definisi fungsi filter agar bisa diakses oleh onclick di HTML
        window.doFilter = function() {
            table.ajax.reload();
        }

        window.resetFilter = function() {
            $('#filterNama').val('');
            table.ajax.reload();
        }

        // Cek stok menipis (Tetap dipertahankan)
        $.ajax({
            type: 'GET',
            url: "{{route('barang.checkStok')}}",
            success: function(data) {
                if (data.length > 0) {
                    let htmlList = '<div class="text-start mt-2" style="font-family: inherit;">' +
                        '<p class="text-muted mb-3" style="margin-bottom: 15px; font-size: 13px; color: #6e7687; text-align: left;">Beberapa barang berikut memiliki jumlah stok kritis (kurang dari 5 unit):</p>' +
                        '<div style="max-height: 220px; overflow-y: auto; padding-right: 4px; display: flex; flex-direction: column; gap: 8px;">';
                    
                    data.forEach(item => {
                        htmlList += `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #fff5f5; border: 1px solid #ffe2e2; border-left: 4px solid #e84c4c; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; text-align: left;">
                                <span style="font-size: 14px; color: #e84c4c; display: inline-flex; align-items: center;"><i class="fe fe-package"></i></span>
                                <span style="font-weight: 600; color: #2d3748; font-size: 13px; word-break: break-word;">${item.nama}</span>
                            </div>
                            <span style="background: #ffe2e2; color: #e84c4c; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 20px; white-space: nowrap;">Stok: ${item.stok}</span>
                        </div>`;
                    });
                    
                    htmlList += '</div></div>';

                    swal({
                        title: "Peringatan Stok Menipis!",
                        html: true,
                        text: htmlList,
                        type: "warning",
                        confirmButtonText: "Tutup",
                        allowOutsideClick: true
                    });
                }
            }
        });
    });
</script>
@endsection
