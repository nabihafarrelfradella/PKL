<!-- MODAL BATCH PENGEMBALIAN -->
<div class="modal fade" data-bs-backdrop="static" id="BatchKmodaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Batch Pengembalian Transaksi <span id="batchBkKodeTitle" class="text-primary"></span></h6>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="batchBkKode">
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="batchTglkembali" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="batchTglkembali" id="batchTglkembali" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Pilih Barang yang Dikembalikan:</label>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th width="5%" class="text-center">
                                        <input type="checkbox" id="checkAllBatch" checked onchange="toggleAllBatch(this)">
                                    </th>
                                    <th>Nama Barang</th>
                                    <th>Kode Unik / SN</th>
                                    <th width="15%" class="text-center" style="min-width: 70px;">Jumlah</th>
                                    <th width="25%" class="text-center" style="min-width: 140px;">Kondisi Fisik</th>
                                </tr>
                            </thead>
                            <tbody id="batchItemsTableBody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info d-none" id="btnLoaderBatchK" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                <a href="javascript:void(0)" onclick="submitBatchK()" id="btnSimpanBatchK" class="btn btn-info">Simpan Pengembalian <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>
