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
                        <div class="text-center mt-5">
                            <div class="error mx-auto" data-text="404">404</div>
                            <p class="lead text-gray-800 mb-5">Page Not Found</p>
                            <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
                            <a href="{{ asset('/my-admin')}}">&#x2190; Back to Dashboard</a>
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