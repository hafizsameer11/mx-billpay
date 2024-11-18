<!DOCTYPE html>
<!--
Template Name: NobleUI - HTML Bootstrap 5 Admin Dashboard Template
Author: NobleUI
Website: https://www.nobleui.com
Portfolio: https://themeforest.net/user/nobleui/portfolio
Contact: nobleui123@gmail.com
Purchase: https://1.envato.market/nobleui_admin
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
	<meta name="author" content="NobleUI">
	<meta name="keywords" content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<title>Mx Bill Pay</title>

  <!-- color-modes:js -->
  <script src="../../../assets/js/color-modes.js"></script>
  <!-- endinject -->

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  <!-- End fonts -->

	<!-- core:css -->
	<link rel="stylesheet" href="../../../assets/vendors/core/core.css">
	<!-- endinject -->

	<!-- Plugin css for this page -->
	<!-- End plugin css for this page -->

	<!-- inject:css -->
	<link rel="stylesheet" href="../../../assets/fonts/feather-font/css/iconfont.css">
	<!-- endinject -->

  <!-- Layout styles -->  
	<link rel="stylesheet" href="../../../assets/css/demo1/style.css">
  <!-- End layout styles -->

  <link rel="shortcut icon" href="../../../assets/images/favicon.png" />
</head>
<body>
	<div class="main-wrapper">
		<div class="page-wrapper full-page">
			<div class="page-content d-flex align-items-center justify-content-center">

				<div class="row w-100 mx-0 auth-page">
					<div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
						<div class="card">
							<div class="row d-flex justify-content-center">
                {{-- <div class="col-md-4 pe-md-0"> --}}
                  {{-- <div class="auth-side-wrapper"> --}}

                  {{-- </div> --}}
                {{-- </div> --}}
                <div class="col-md-8 ps-md-0">
                    <div class="auth-form-wrapper px-4 py-5">
                        <a href="#" class="nobleui-logo d-block mb-2 text-center">Mx Bill Pay</a>
                        <h5 class="text-secondary fw-normal mb-4 text-center">Welcome back! Log in to your account.</h5>
                        @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{route('admin.authenticate')}}" class="forms-sample" method="POST" >
                        @csrf
                      <div class="mb-3">
                        <label for="userEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="userEmail" placeholder="Email">
                        <span class="text-danger">
                            @error('email')
                                {{$message}}
                            @enderror
                        </span>
                      </div>
                      <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password"  id="userPassword" autocomplete="current-password" placeholder="Password">
                        <span class="text-danger">
                            @error('password')
                                {{$message}}
                            @enderror
                        </span>
                      </div>
                      <div class="form-check mb-3">
                        <input type="checkbox" disabled class="form-check-input" id="authCheck">
                        <label class="form-check-label" for="authCheck">
                          Remember me
                        </label>
                      </div>
                      <div>
                        <button  class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Login</button>
                        {{-- <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                          <i class="btn-icon-prepend" data-feather="github"></i>
                          Login with Github
                        </button> --}}
                      </div>
                      {{-- <a href="register.html" class="d-block mt-3 text-secondary">Not a user? Sign up</a> --}}
                    </form>
                  </div>
                </div>
              </div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- core:js -->
	<script src="../../../assets/vendors/core/core.js"></script>
	<!-- endinject -->

	<!-- Plugin js for this page -->
	<!-- End plugin js for this page -->

	<!-- inject:js -->
	<script src="../../../assets/vendors/feather-icons/feather.min.js"></script>
	<script src="../../../assets/js/app.js"></script>
	<!-- endinject -->

	<!-- Custom js for this page -->
	<!-- End custom js for this page -->

</body>
</html>