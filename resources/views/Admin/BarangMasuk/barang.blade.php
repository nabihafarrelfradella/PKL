<!-- MODAL BARANG -->
<div class="modal fade" data-bs-backdrop="static" style="overflow-y:scroll;" id="modalBarang">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Pilih Barang</h6><button onclick="resetB('tambah')" aria-label="Close" class="btn-close"></button>
            </div>
            <div class="modal-body p-4 pb-5">
                <input type="hidden" value="tambah" name="param">
                <input type="hidden" id="randkey">
                <div class="table-responsive">
                    <table id="table-2" width="100%" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Gambar</th>
                            <th class="border-bottom-0">Kode Barang</th>
                            <th class="border-bottom-0">Nama Barang</th>
                            <th class="border-bottom-0">Jenis</th>
                            <th class="border-bottom-0">Satuan</th>
                            <th class="border-bottom-0">Merk</th>
                            <th class="border-bottom-0">Stok</th>
                            <th class="border-bottom-0" width="1%">Action</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('formOtherJS')
<script>
    document.getElementById('randkey').value = makeid(10);

    function resetB() {
        param = $('input[name="param"]').val();
        if (param == 'tambah') {
            $('#modalBarang').modal('hide');
            setTimeout(() => { $('#modaldemo8').modal('show'); }, 400);
        } else {
            $('#modalBarang').modal('hide');
            setTimeout(() => { $('#Umodaldemo8').modal('show'); }, 400);
        }

    }

    function pilihBarang(data) {
        const key = $("#randkey").val();
        $("#status").val("true");
        $("input[name='kdbarang']").val(data.barang_kode);
        
        let rawNama = data.barang_nama.replace(/_/g, ' ');
        let parts = rawNama.split(' - ');
        $("#nmbarang").val(parts[0]);
        let merk_val = data.merk_nama ? data.merk_nama.replace(/_/g, ' ') : null;
        $("#merkbarang").val(merk_val || parts[1] || '-');
        
        $("#satuan").val(data.satuan_nama.replace(/_/g, ' '));
        $("#jenis").val(data.tipe_barang.replace(/_/g, ' '));
        
        let satuanStr = (data.satuan_nama || "").toString().toLowerCase().replace(/_/g, ' ');
        if (satuanStr.includes("meter") || satuanStr === "mtr" || satuanStr === "m") {
            $("input[name='jml']").removeAttr('readonly');
            $("input[name='jml']").removeAttr('style');
            $("input[name='jml']").removeAttr('title');
        } else {
            $("input[name='jml']").val('1');
            $("input[name='jml']").attr('readonly', true);
            $("input[name='jml']").css({'background-color': '#f3f6f9', 'cursor': 'not-allowed'});
            $("input[name='jml']").attr('title', 'Jumlah dikunci 1 karena wajib scan Serial Number per item');
        }
        $('#modalBarang').modal('hide');
        setTimeout(() => { $('#modaldemo8').modal('show'); }, 400);
    }

    function pilihBarangU(data) {
        const key = $("#randkey").val();
        $("#statusU").val("true");
        $("input[name='kdbarangU']").val(data.barang_kode);
        
        let rawNamaU = data.barang_nama.replace(/_/g, ' ');
        let partsU = rawNamaU.split(' - ');
        $("#nmbarangU").val(partsU[0]);
        let merk_valU = data.merk_nama ? data.merk_nama.replace(/_/g, ' ') : null;
        $("#merkbarangU").val(merk_valU || partsU[1] || '-');
        
        $("#satuanU").val(data.satuan_nama.replace(/_/g, ' '));
        $("#jenisU").val(data.tipe_barang.replace(/_/g, ' '));
        
        let satuanStrU = (data.satuan_nama || "").toString().toLowerCase().replace(/_/g, ' ');
        if (satuanStrU.includes("meter") || satuanStrU === "mtr" || satuanStrU === "m") {
            $("input[name='jmlU']").removeAttr('readonly');
            $("input[name='jmlU']").removeAttr('style');
            $("input[name='jmlU']").removeAttr('title');
        } else {
            $("input[name='jmlU']").val('1');
            $("input[name='jmlU']").attr('readonly', true);
            $("input[name='jmlU']").css({'background-color': '#f3f6f9', 'cursor': 'not-allowed'});
            $("input[name='jmlU']").attr('title', 'Jumlah dikunci 1 karena wajib scan Serial Number per item');
        }
        $('#modalBarang').modal('hide');
        setTimeout(() => { $('#Umodaldemo8').modal('show'); }, 400);
    }

    var table2;
    $(document).ready(function() {
        //datatables
        table2 = $('#table-2').DataTable({

            "processing": true,
            "serverSide": true,
            "info": false,
            "order": [],
            "ordering": false,

            // "lengthMenu": [
            //     [5, 10, 25, 50, 100],
            //     [5, 10, 25, 50, 100]
            // ],
            "pageLength": 10,

            "lengthChange": true,

            "ajax": {
                "url": "{{url('admin/barang/listbarang')}}/param",
                "data": function(d) {
                    d.param = $('input[name="param"]').val();
                }
            },

            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'img',
                    name: 'barang_foto',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'barang_kode',
                    name: 'barang_kode',
                },
                {
                    data: 'barang_nama',
                    name: 'barang_nama',
                },
                {
                    data: 'jenisbarang',
                    name: 'jenisbarang_nama',
                },
                {
                    data: 'satuan',
                    name: 'satuan_nama',
                },
                {
                    data: 'merk',
                    name: 'merk_nama'
                },
                {
                    data: 'totalstok',
                    name: 'barang_stok',
                    render: function (data, type, row) {
                        let cleanNumber = String(data).replace(/<[^>]*>?/gm, '').trim();
                        let stok = parseInt(cleanNumber);
                        
                        let color = "";
                        if (stok < 5) {
                            color = "#e82646";
                        } else if (stok <= 10) {
                            color = "#f7b731";
                        } else {
                            color = "#09ad95";
                        }

                        return `<span style="color: ${color} !important; font-weight: bold;">${stok}</span>`;
                    }
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

    $(document).on('hidden.bs.modal', '#modalBarang', function () {
        if (($('#modaldemo8').length && !$('#modaldemo8').hasClass('d-none')) || 
            ($('#Umodaldemo8').length && !$('#Umodaldemo8').hasClass('d-none'))) {
            $('body').addClass('modal-open');
        }
    });

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
</script>
@endsection
