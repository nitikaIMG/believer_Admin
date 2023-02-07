@extends('layouts.app')

@section('content')
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
                        <div class="card my-5">
                            <div class="card-body p-3 text-center">
                                <div class="h3 font-weight-light">Sign In</div>
                            </div>
                            <hr class="my-0 border-light" />
                            <div class="card-body p-4">
                                <form method="POST" action="{{ route('login') }}">
                                @csrf
                                    <div class="form-group">
                                        <label class="text-gray-600 small" for="emailExample">Email Address</label>
                                        <input name="email" class="form-control form-control-solid @error('email') is-invalid @enderror  form-control form-control-solid-solid py-4" type="email" placeholder="Email Address" aria-describedby="emailExample" required/>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label type="password" class="text-gray-600 small" for="passwordExample">Password</label>
                                        <input class="form-control form-control-solid py-4 @error('password') is-invalid @enderror" type="password" placeholder="Password" aria-describedby="passwordExample" name="password" required autocomplete="current-password"/>

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                        <button type="submit" class="btn rounded-pill btn-primary btn-block" href="index.html">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="layoutAuthentication_footer">
        <footer class="footer mt-auto footer-dark">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 small">Copyright &#xA9; Admin 2021</div>
                    <div class="col-md-6 text-md-right small">
                        <a href="javascript:void(0);">Privacy Policy</a>
                        &#xB7;
                        <a href="javascript:void(0);">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
@endsection
