
@if( Session::has('warning') or Session::has('danger') or Session::has('error') or Session::has('success') or $errors->any())
<div class="position-fixed bottom-15px right-15px z-index-1000">

    @if ($message = Session::get('warning'))
    <div class="toast border" id="toastBasic2" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
        <div class="toast-header border bg-warning text-white">
            <i class="far fa-exclamation-circle"></i>
            <strong class="mr-auto ml-2">Alert!</strong>
            <small class="text-white-50 ml-2">just now</small>
            <button class="ml-2 mb-1 close text-white bg-warning" type="button" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="toast-body">
            {{ $message }}
        </div>
    </div>
    @endif

    @if ($message = Session::get('error') or $message = Session::get('danger') or $errors->any())
    <div class="toast border" id="toastBasic3" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
        <div class="toast-header border bg-danger text-white">
            <i class="far fa-exclamation-circle"></i>
            <strong class="mr-auto ml-2">Alert!</strong>
            <small class="text-white-50 ml-2">just now</small>
            <button class="ml-2 mb-1 close text-white bg-danger" type="button" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="toast-body">
            {{ $message }}
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    @if ($message = Session::get('success'))
    <div class="toast border" id="toastBasic4" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
        <div class="toast-header border bg-success text-white">
            <i class="far fa-check-circle"></i>
            <strong class="mr-auto ml-2">Success!</strong>
            <small class="text-white-50 ml-2">just now</small>
            <button class="ml-2 mb-1 close text-white bg-success" type="button" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="toast-body">
        {{ $message }}
        </div>
    </div>
    @endif
</div>
@endif

<script>
$(window).on("load", function() {

    var success = '{{ Session::has('success') ?? 0 }}';
    var danger = '{{ Session::has('danger') ?? 0 }}';
    var error = '{{ Session::has('error') ?? 0 }}{{ $errors->any() }}';
    var warning = '{{ Session::has('warning') ?? 0 }}';

    if(success) {
        $("#toastBasic4").toast('show');
    }
    
    if(danger || error) {
        $("#toastBasic3").toast('show');
    }
    
    if(warning) {
        $("#toastBasic2").toast('show');
    }
});
</script>
