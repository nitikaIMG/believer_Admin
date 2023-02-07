@extends('layouts.app')

@section('content')

@section('error-page')
error-page
@endsection

<div id="layoutError">
    <div id="layoutError_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="text-center mt-4">
                            <img class="mb-4 img-error" src="{{ asset('public/assets/img/drawkit/color/drawkit-social-feed.svg') }}" />
                            <p class="lead">Oops! An error has occured.</p>
                            <a class="text-arrow-icon" href="{{ asset('/my-admin')}}"><i class="ml-0 mr-1" class="fad fa-data-feather"></i>Return to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="layoutError_footer">
        <footer class="footer mt-auto footer-light">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 small">Copyright &#xA9; Admin 2020</div>
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