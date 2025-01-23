@extends('layouts.main')
@section('main')
<div class="page-wrapper full-page">
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
                <div class="card">
                    <div class="row">
                        <div class="col-md-4 pe-md-0">
                            <div class="auth-side-wrapper"></div>
                        </div>
                        <div class="col-md-8 ps-md-0">
                            <div class="auth-form-wrapper px-4 py-5">
                                <a href="#" class="nobleui-logo d-block mb-2">Noble<span>UI</span></a>
                                <h5 class="text-secondary fw-normal mb-4">
                                    Welcome back! Log in to your account.
                                </h5>
                                <form class="forms-sample">
                                    <div class="mb-3">
                                        <label for="userEmail" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="userEmail" placeholder="Email" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="userPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="userPassword"
                                            autocomplete="current-password" placeholder="Password" />
                                    </div>
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="authCheck" />
                                        <label class="form-check-label" for="authCheck">
                                            Remember me
                                        </label>
                                    </div>
                                    <div>
                                        <a href="../../dashboard.html"
                                            class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Login</a>
                                        <button type="button"
                                            class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                            <i class="btn-icon-prepend" data-feather="github"></i>
                                            Login with Github
                                        </button>
                                    </div>
                                    <a href="register.html" class="d-block mt-3 text-secondary">Not a user? Sign up</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
