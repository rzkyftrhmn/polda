<!DOCTYPE html>
<html lang="en">

<head>
     	<!--Title-->
	<title>Arsip Propam | Login</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="Dexignlabs">
	<meta name="robots" content="index, follow">

	<meta name="keywords" content="POLDA JAWA BARAT, Arsip Propam">

	<meta name="description" content="POLDA JAWA BARAT - SELAMAT DATANG DI APLIKASI ARSIP BERKAS PROPAM">

	<meta property="og:title" content="Arsip Propam | Login">
	<meta property="og:description" content="POLDA JAWA BARAT - SELAMAT DATANG DI APLIKASI ARSIP BERKAS PROPAM">
	
	<meta name="format-detection" content="telephone=no">

	<meta name="twitter:title" content="Arsip Propam | Login">
	<meta name="twitter:description" content="POLDA JAWA BARAT - SELAMAT DATANG DI APLIKASI ARSIP BERKAS PROPAM">
	
	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="{{ asset('dashboard/images/propam.ico') }}">
    <link href="{{ asset('dashboard/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet"/>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet"/>
</head>

<body class="body">
	<div class="authincation d-flex flex-column flex-lg-row flex-column-fluid">
		<div class="login-aside text-center  d-flex flex-column flex-row-auto">
			<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
				<div class="text-center mb-lg-4 mb-2 pt-5 logo">
					<img src="{{ asset('dashboard/images/propam-bg.png') }}" alt="" style="width: 100px;">
					<img src="{{ asset('dashboard/images/poldajabar-bg.png') }}" alt="" style="width: 100px;">
				</div>
				<h2 class="mb-2 text-white">POLDA JAWA BARAT</h2>
				<h4 class="mb-4 text-white">SELAMAT DATANG DI APLIKASI <br>ARSIP BERKAS PROPAM</h4>
			</div>
		</div>
		<div class="container flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
			<div class="d-flex justify-content-center h-100 align-items-center">
				<div class="authincation-content style-2">
					<div class="row no-gutters">
						<div class="col-xl-12 tab-content">
							<div id="sign-up" class="auth-form tab-pane fade show active  form-validation">
                                <form method="POST" action="{{ route('login') }}">
                                @csrf
									<div class="text-center mb-4">
										<h3 class="text-center mb-2 text-dark">Sign In</h3>
									</div>
									<div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label required">Email address</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Masukan email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
									</div>
									<div class="mb-3 position-relative">
										<label class="form-label required">Password</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukan password">
										<span class="show-pass eye">
											<i class="fa fa-eye-slash"></i>
											<i class="fa fa-eye"></i>
										</span>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
									</div>
									<button class="btn btn-block btn-primary">Sign In</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('dashboard/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('dashboard/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/custom.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/dlabnav-init.js') }}"></script>
</body>
</html>