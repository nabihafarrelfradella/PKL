@extends('Master.Layouts.app_login', ['title' => $title])

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="page-wrap">

    <!-- Left: Illustration -->
    <div class="left-panel">
        <img src="{{url('/assets/default/web/warehouse_illustration.png')}}" alt="Warehouse Illustration" class="illus-img">

        <!-- Decorative overlays (pointer-events:none so illustration stays clickable) -->
        <div class="deco deco-blob-tl"></div>
        <div class="deco deco-blob-br"></div>
        <div class="deco deco-ring"></div>
        <div class="deco deco-dots"></div>
    </div>

    <!-- Right: Login Form -->
    <div class="right-panel">
        <div class="form-box">

            <!-- Logo + Title -->
            <div class="brand-block">
                <img src="{{url('/assets/default/web/logo-login.png')}}" alt="Alfatindo Logo" class="brand-logo">
                <h1 class="brand-name">MANAJEMEN GUDANG</h1>
            </div>

            <!-- Form -->
            <form method="POST" name="myForm" action="{{ url('admin/proseslogin') }}" onsubmit="return validateForm()">
                @csrf

                <div class="input-field">
                    <input name="user" id="user" type="text" value="{{Session::get('userInput')}}" placeholder="Username" autocomplete="off">
                </div>

                <div class="input-field">
                    <input name="pwd" id="pwd" type="password" placeholder="Password" autocomplete="off">
                </div>

                <button type="submit" class="btn-login" id="btnLogin">Login</button>
                <button type="button" class="btn-login d-none" id="btnLoader" disabled>
                    <span class="spinner-border spinner-border-sm me-2"></span> Memproses...
                </button>
            </form>

        </div>
    </div>

</div>

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body, html {
        height: 100%;
        font-family: 'Inter', sans-serif;
        overflow: hidden;
    }

    /* ===== FULL-SCREEN SPLIT LAYOUT ===== */
    .page-wrap {
        display: flex;
        height: 100vh;
        width: 100vw;
        background: #fff;
    }

    /* --- Left Panel --- */
    .left-panel {
        flex: 1;
        position: relative;
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .illus-img {
        width: 85%;
        height: 85%;
        max-width: 85%;
        max-height: 85%;
        object-fit: contain;
        display: block;
    }

    /* --- Decorative overlays --- */
    .deco {
        position: absolute;
        pointer-events: none;
        z-index: 2;
    }

    /* Soft blob top-left */
    .deco-blob-tl {
        top: -60px;
        left: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37,99,235,0.12) 0%, rgba(37,99,235,0) 70%);
    }

    /* Soft blob bottom-right */
    .deco-blob-br {
        bottom: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(220,91,127,0.13) 0%, rgba(220,91,127,0) 70%);
    }

    /* Floating ring - top right corner */
    .deco-ring {
        top: 24px;
        right: 24px;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 3px solid rgba(37,99,235,0.18);
        animation: spinRing 12s linear infinite;
    }

    @keyframes spinRing {
        from { transform: rotate(0deg) scale(1); }
        50%  { transform: rotate(180deg) scale(1.08); }
        to   { transform: rotate(360deg) scale(1); }
    }

    /* Dot grid - bottom left */
    .deco-dots {
        bottom: 28px;
        left: 28px;
        width: 90px;
        height: 90px;
        background-image: radial-gradient(circle, rgba(37,99,235,0.22) 1.5px, transparent 1.5px);
        background-size: 14px 14px;
        opacity: 0.7;
        animation: fadeInOut 5s ease-in-out infinite alternate;
    }

    @keyframes fadeInOut {
        from { opacity: 0.4; }
        to   { opacity: 0.85; }
    }

    /* --- Right Panel --- */
    .right-panel {
        flex: 0 0 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(160deg, #c5d8f7 0%, #f4c5c5 100%);
        padding: 40px 50px;
        box-shadow: -4px 0 30px rgba(0,0,0,0.08);
    }

    .form-box {
        width: 100%;
        max-width: 320px;
    }

    /* Brand block: logo + name stacked centered */
    .brand-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        gap: 4px;
    }

    .brand-logo {
        height: 200px;
        object-fit: contain;
        filter: drop-shadow(0 4px 12px rgba(37,99,235,0.15));
    }

    .brand-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1a2540;
        letter-spacing: 2px;
        text-align: center;
        text-transform: uppercase;
    }

    /* Input fields */
    .input-field {
        margin-bottom: 14px;
    }

    .input-field input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e0e6ef;
        border-radius: 10px;
        font-size: 0.9rem;
        color: #1a2540;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
    }

    .input-field input:focus {
        border-color: #2563EB;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }

    .input-field input::placeholder { color: #b0b8c8; }

    /* Login button */
    .btn-login {
        width: 100%;
        margin-top: 10px;
        padding: 13px;
        background: linear-gradient(90deg, #2563EB, #e05b7f);
        color: #fff;
        border: none;
        border-radius: 30px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.3s, transform 0.15s, box-shadow 0.25s;
        box-shadow: 0 4px 14px rgba(37,99,235,0.25);
    }

    .btn-login:hover {
        background: linear-gradient(90deg, #e05b7f, #2563EB);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220,91,127,0.4);
    }

    .btn-login:active { transform: translateY(0); }

    /* ===== MOBILE ===== */
    @media (max-width: 768px) {
        .page-wrap {
            flex-direction: column;
            overflow: auto;
            height: 100vh;
        }

        body, html { overflow: auto; }

        /* Sembunyikan panel ilustrasi di mobile */
        .left-panel {
            display: none !important;
        }

        /* Form login mengisi seluruh layar */
        .right-panel {
            flex: 1;
            width: 100%;
            min-height: 100vh;
            padding: 60px 32px 50px;
            box-shadow: none;
            justify-content: center;
        }

        .form-box {
            max-width: 100%;
            width: 100%;
        }
    }
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        setLoading(false);

        @if(Session::has('status'))
            swal({
                title: "{{ Session::get('status') == 'success' ? 'Berhasil' : 'Gagal' }}",
                text: "{{ Session::get('msg') }}",
                icon: "{{ Session::get('status') }}",
                button: "OK",
            });
        @endif
    });

    function validateForm() {
        var usr = $('#user').val();
        var pwd = $('#pwd').val();
        if (usr.trim() == "" || pwd.trim() == "") {
            swal("Peringatan", "Username dan Password tidak boleh kosong!", "warning");
            return false;
        }
        setLoading(true);
        return true;
    }

    function setLoading(bool) {
        if (bool) {
            $('#btnLogin').addClass('d-none');
            $('#btnLoader').removeClass('d-none');
        } else {
            $('#btnLogin').removeClass('d-none');
            $('#btnLoader').addClass('d-none');
        }
    }
</script>
@endsection
