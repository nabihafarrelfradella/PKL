@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <h1 class="page-title">{{$title}}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Master Data</li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
        </ol>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Data</h3>
                @if($hakTambah > 0)
                <div>
                    <a class="modal-effect btn btn-primary-light" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">Tambah Data <i class="fe fe-plus"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body border-bottom pb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label mb-1">Nama Barang</label>
                        <input type="text" id="filterNama" class="form-control form-control-sm" placeholder="Cari nama barang...">
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-primary btn-sm" onclick="doFilter()"><i class="fe fe-search me-1"></i>Cari</button>
                        <button class="btn btn-light btn-sm ms-1" onclick="resetFilter()"><i class="fe fe-x me-1"></i>Reset</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Gambar</th>
                                <th class="border-bottom-0">Kode Barang</th>
                                <th class="border-bottom-0">Nama Barang</th>
                                <th class="border-bottom-0">Jenis</th>
                                {{-- Kolom Tipe sudah dihapus --}}
                                <th class="border-bottom-0">Merk</th>
                                <th class="border-bottom-0">Stok</th>
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
        id = new Date().getTime();
        $("input[name='kode']").val("BRG-"+id);
    }
    function update(data){
        $("input[name='idbarangU']").val(data.barang_id);
        $("input[name='kodeU']").val(data.barang_kode);
        $("input[name='namaU']").val(data.barang_nama.replace(/_/g, ' '));
        $("select[name='jenisbarangU']").val(data.jenisbarang_id);
        $("select[name='satuanU']").val(data.satuan_id);
        $("select[name='merkU']").val(data.merk_id);
        $("input[name='stokU']").val(data.barang_stok);
        if(data.barang_gambar != 'image.png'){
            $("#outputImgU").attr("src", "{{asset('storage/barang/')}}"+"/"+data.barang_gambar);    
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
            $("#outputImgG").attr("src", "{{url('/assets/default/barang/image.png')}}");
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
            "scrollX": true,
            "lengthMenu": [
                [5, 10, 25, 50, 100],
                [5, 10, 25, 50, 100]
            ],
            "pageLength": 10,
            "lengthChange": true,
            "ajax": {
                "url": "{{route('barang.getbarang')}}", // KEMBALI KE ROUTE LAMA
                "data": function(d) {
                    d.filter_nama = $('#filterNama').val();
                }
            },
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                { data: 'img', name: 'barang_gambar', searchable: false, orderable: false },
                { data: 'barang_kode', name: 'barang_kode' },
                { data: 'barang_nama', name: 'barang_nama' },
                { data: 'jenisbarang', name: 'tbl_jenisbarang.jenisbarang_nama' },
                // Kolom 'tipe' tetap dihapus dari sini agar tidak error
                { data: 'merk', name: 'tbl_merk.merk_nama' },
                { data: 'totalstok', name: 'totalstok', searchable: false, orderable: false },
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
                    let htmlList = '<ul style="text-align: left;">';
                    data.forEach(item => {
                        htmlList += `<li><b>${item.nama}</b> (Stok: ${item.stok})</li>`;
                    });
                    htmlList += '</ul>';

                    swal({
                        title: "Peringatan Stok Menipis!",
                        html: true,
                        text: "Beberapa barang memiliki stok kurang dari 5:<br><br>" + htmlList,
                        type: "warning",
                        confirmButtonText: "Tutup"
                    });
                }
            }
        });
    });
</script>
@endsection