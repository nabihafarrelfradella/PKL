<!-- MODAL WEBCAM -->
<div class="modal fade" id="modalWebcam" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-camera me-1"></i> Ambil Foto</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeWebcamModal()"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="webcam-container" class="position-relative bg-light d-flex justify-content-center align-items-center" style="min-height: 300px; overflow: hidden; border-radius: 8px;">
                    <!-- Video stream -->
                    <video id="webcam-video" autoplay playsinline style="width: 100%; max-height: 400px; object-fit: cover;"></video>
                    <!-- Captured snapshot -->
                    <canvas id="webcam-canvas" class="d-none" style="width: 100%; max-height: 400px; object-fit: cover;"></canvas>
                    
                    <div id="webcam-loader" class="position-absolute d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Control Buttons -->
                <div class="mt-4 d-flex justify-content-center gap-2">
                    <button type="button" id="btn-cancel-webcam" class="btn btn-light" onclick="closeWebcamModal()">
                        Batal
                    </button>
                    <button type="button" id="btn-switch-camera" class="btn btn-secondary" onclick="switchCamera()" title="Tukar Kamera">
                        <i class="fe fe-refresh-cw"></i>
                    </button>
                    <button type="button" id="btn-take-snapshot" class="btn btn-primary" onclick="takeSnapshot()">
                        <i class="fe fe-camera"></i> Ambil Foto
                    </button>
                    
                    <div id="webcam-confirm-group" class="d-none">
                        <button type="button" class="btn btn-danger me-2" onclick="retakeSnapshot()" title="Ulangi">
                            <i class="fe fe-x"></i>
                        </button>
                        <button type="button" class="btn btn-success" onclick="confirmSnapshot()" title="Gunakan Foto Ini">
                            <i class="fe fe-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let webcamStream = null;
    let targetInputId = null;
    let targetImgId = null;
    let capturedBlob = null;
    let currentFacingMode = 'environment';

    function openWebcamModal(inputId, imgId, isCircle = false) {
        targetInputId = inputId;
        targetImgId = imgId;
        
        if (isCircle) {
            $('#webcam-container').css('border-radius', '50%');
        } else {
            $('#webcam-container').css('border-radius', '8px');
        }
        
        $('#modalWebcam').modal('show');
        $('#btn-take-snapshot').removeClass('d-none');
        $('#btn-switch-camera').removeClass('d-none');
        $('#btn-cancel-webcam').removeClass('d-none');
        $('#webcam-confirm-group').addClass('d-none');
        $('#webcam-canvas').addClass('d-none');
        $('#webcam-video').removeClass('d-none');
        $('#webcam-loader').removeClass('d-none');

        startCameraStream();
    }
    
    function startCameraStream() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
        }

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: currentFacingMode } })
                .then(function(stream) {
                    webcamStream = stream;
                    const video = document.getElementById('webcam-video');
                    video.srcObject = stream;
                    video.play();
                    $('#webcam-loader').addClass('d-none');
                })
                .catch(function(error) {
                    console.error("Kamera error: ", error);
                    $('#webcam-loader').addClass('d-none');
                    swal({ title: 'Gagal mengakses kamera!', text: 'Pastikan perangkat memiliki kamera dan browser memiliki izin akses.', type: 'error' });
                    closeWebcamModal();
                });
        } else {
            swal({ title: 'Tidak didukung!', text: 'Browser Anda tidak mendukung fitur ini.', type: 'error' });
            closeWebcamModal();
        }
    }

    function switchCamera() {
        $('#webcam-loader').removeClass('d-none');
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        startCameraStream();
    }

    function closeWebcamModal() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        $('#modalWebcam').modal('hide');
        // Fix Bootstrap nested modal bug (keeps body scrollable if another modal is still open)
        setTimeout(function() {
            if ($('.modal.show').length > 0) {
                $('body').addClass('modal-open');
            }
        }, 500);
    }

    function takeSnapshot() {
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('webcam-canvas');
        
        // Sesuaikan ukuran canvas dengan ukuran asli video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob(function(blob) {
            capturedBlob = blob;
            
            // Switch UI
            $('#webcam-video').addClass('d-none');
            $('#webcam-canvas').removeClass('d-none');
            $('#btn-take-snapshot').addClass('d-none');
            $('#btn-switch-camera').addClass('d-none');
            $('#btn-cancel-webcam').addClass('d-none');
            $('#webcam-confirm-group').removeClass('d-none');
            
            // Stop stream sementara
            if (webcamStream) {
                webcamStream.getTracks().forEach(track => track.stop());
            }
        }, 'image/jpeg', 0.8);
    }

    function retakeSnapshot() {
        // Nyalakan kembali kamera
        $('#btn-take-snapshot').removeClass('d-none');
        $('#btn-switch-camera').removeClass('d-none');
        $('#btn-cancel-webcam').removeClass('d-none');
        $('#webcam-confirm-group').addClass('d-none');
        $('#webcam-canvas').addClass('d-none');
        $('#webcam-video').removeClass('d-none');
        startCameraStream();
    }

    function confirmSnapshot() {
        if (!capturedBlob) return;
        
        // Convert Blob to File
        const fileName = 'webcam_' + new Date().getTime() + '.jpg';
        const file = new File([capturedBlob], fileName, { type: "image/jpeg", lastModified: new Date().getTime() });
        
        // Assign file to input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        const fileInput = document.getElementById(targetInputId);
        if (fileInput) {
            fileInput.files = dataTransfer.files;
            // Trigger onchange event (jika ada event handler di file input)
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
        
        closeWebcamModal();
    }

    // Handle close button click inside modal or backdrop
    $('#modalWebcam').on('hidden.bs.modal', function () {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        // Fix Bootstrap nested modal bug
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    });
</script>
