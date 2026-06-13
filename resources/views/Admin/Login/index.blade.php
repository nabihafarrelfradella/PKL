@extends('Master.Layouts.app_login', ['title' => $title])

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="split-screen">
    <div class="left-side">
        <div class="login-wrapper">

            <div class="logo-section mb-5 text-start">
                <img src="{{url('/assets/default/web/default.png')}}" height="120px" alt="logo">
            </div>

            <div class="login-header mb-5 text-start">
                <h1 class="fw-bold text-dark">Login</h1>
                <p class="text-muted">Silakan gunakan Username dan Password untuk login</p>
            </div>

            <form class="login-form validate-form" method="POST" name="myForm" action="{{ url('admin/proseslogin') }}" onsubmit="return validateForm()">
                @csrf
                
                <div class="input-container mb-4">
                    <input name="user" id="user" value="{{Session::get('userInput')}}" type="text" placeholder=" " autocomplete="off">
                    <label for="user">Username</label>
                    <i class="ri-user-3-line input-icon"></i>
                </div>

                <div class="input-container mb-4">
                    <input name="pwd" id="pwd" type="password" placeholder=" " autocomplete="off">
                    <label for="pwd">Password</label>
                    <i class="ri-lock-2-line input-icon"></i>
                </div>

                <div class="form-check text-start mb-5">
                    <input class="form-check-input" type="checkbox" id="rememberCheck">
                    <label class="form-check-label text-muted small" for="rememberCheck">
                        Keep me logged in
                    </label>
                </div>

                <div class="form-btn-container mb-4">
                    <button type="submit" class="login-form-btn w-100" id="btnLogin">LOGIN</button>
                    <button type="button" class="login-form-btn w-100 d-none" id="btnLoader" disabled>
                        <span class="spinner-border spinner-border-sm me-2"></span> Memproses...
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href='https://i.pinimg.com/736x/47/b8/68/47b8687e1a612547846960c69381aaaa.jpg' class="text-primary small text-decoration-underline" target='_blank'>
                        Trouble Logging in?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="right-side d-none d-lg-flex">
        <div class="content-wrapper text-center">
            <div class="date-section mb-5">
                <h1 class="fw-light display-3 text-white">{{ date('jS F,') }}</h1>
                <h1 class="fw-bold display-3 text-white">{{ date('Y') }}</h1>
            </div>

            <div class="modern-art">
                <div class="circle c1"></div>
                <div class="circle c2"></div>
                <div class="circle c3"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* 1. Layout Dasar */
body, html { height: 100%; margin: 0; overflow: hidden; }
.split-screen { display: flex; height: 100vh; }

.left-side { 
    flex: 1; background: #fff; display: flex; align-items: center; 
    justify-content: center; padding: 60px; position: relative; 
}

.right-side { 
    flex: 1; background: #0a1f3d; display: flex; align-items: center; 
    justify-content: center; position: relative; overflow: hidden; 
}

.login-wrapper { width: 100%; max-width: 400px; }
.top-nav-link { position: absolute; top: 30px; right: 30px; }

/* 2. Input Container & Icon Centering (Tengah Sempurna) */
.input-container { 
    position: relative; border: 1px solid #e0e0e0; border-radius: 8px; 
    height: 60px; display: flex; align-items: center; transition: all 0.3s;
}

.input-container:focus-within {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
}

.input-container input { 
    width: 100%; height: 100%; border: none; outline: none; 
    padding: 22px 15px 8px 50px; background: transparent; font-size: 1rem;
}

/* KUNCI: Membuat Ikon Berada di Tengah Vertikal */
.input-icon { 
    position: absolute; left: 18px; top: 50%; 
    transform: translateY(-50%); /* Geser ikon tepat ke tengah */
    font-size: 1.4rem; color: #bdbdbd; transition: 0.3s; 
}

.input-container label { 
    position: absolute; left: 50px; top: 50%; 
    transform: translateY(-50%); transition: 0.3s; 
    color: #999; pointer-events: none; 
}

/* Floating Label Animation */
.input-container input:focus + label, 
.input-container input:not(:placeholder-shown) + label { 
    top: 15px; font-size: 0.75rem; color: #6c5ce7; font-weight: bold; 
}

.input-container:focus-within .input-icon { color: #6c5ce7; }

/* 3. Tombol Login */
.login-form-btn { 
    background: #6c5ce7; color: #fff; border: none; height: 55px; 
    border-radius: 8px; font-weight: bold; letter-spacing: 1.5px; transition: 0.3s;
}

.login-form-btn:hover { background: #5b4cc4; transform: translateY(-2px); }

/* 4. Dekorasi Kanan */
.circle { position: absolute; border-radius: 50%; background: #6c5ce7; filter: blur(30px); opacity: 0.4; }
.c1 { width: 200px; height: 200px; top: 10%; left: 20%; }
.c2 { width: 150px; height: 150px; bottom: 15%; right: 20%; background: #a29bfe; }
.c3 { width: 80px; height: 80px; top: 50%; left: 60%; background: #fff; opacity: 0.1; }
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