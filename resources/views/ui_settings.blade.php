@extends('main')

@section('heading')
    Ui Settings
@endsection('heading')

@section('sub-heading')
    Here we change project name, short name etc.
@endsection('sub-heading')

@section('content')

<div class="card">
    <div class="card-header">UI Settings</div>
    <form class="card-body" action="{{ action('SettingsController@ui_settings') }}" method="post" enctype="multipart/form-data">
        @csrf
        
        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                



                <div class="row mx-0">


                    <?php

                        $inputName = ['project_name', 'short_name'];
                        $labels = ['Project Name', 'Project Short Name'];

                        for($i=0; $i<count($inputName);$i++){?>
                        <?php $inputs= $inputName[$i];?>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="number-of-employees">
                                    {{ $labels[$i] }}
                                </label>
                                <input class="form-control form-control-solid" id="{{$inputs}}" type="text" placeholder="Enter {{ $labels[$i] }}" name="input[{{$inputs}}]" value="{{$settings->$inputs ?? ''}}">
                            </div>
                        </div>

                    <?php }?>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="number-of-employees">
                                Show project name or logo or both in admin
                            </label>
                            <select class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select" data-hide-disabled="true" name="input[project_name_or_logo]"  required="">
                                <option value="project_name"
                                @if( !empty($settings->project_name_or_logo) and $settings->project_name_or_logo == 'project_name')
                                selected
                                @endif
                                >Project name</option>
                                <option value="logo"
                                @if( !empty($settings->project_name_or_logo) and $settings->project_name_or_logo == 'logo')
                                selected
                                @endif
                                >Logo</option>
                                <option value="both"
                                @if( empty($settings->project_name_or_logo) or $settings->project_name_or_logo == 'both')
                                selected
                                @endif
                                >Both</option>
                            </select>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="sbp-preview mt-4">
            <div class="sbp-preview-content">
                <div class="row mx-0 justify-content-center ">


                    <?php

                        $input = ['logo', 'favicon', 'user_image', 'team_image', 'player_image'];
                        $defaultLogo = ['logo.png', 'first_logo.png', 'logo.png', 'logo.png', 'logo.png'];
                        $labels = ['Logo', 'Favicon', 'User Default Image', 'Team Default Image', 'Player Default Image'];

                        for($i=0; $i<count($input);$i++){?>

                        <?php $setting2= $input[$i];?>
                        <?php $defaultImages= $defaultLogo[$i];?>

                        <?php
                            if( !empty($settings->$setting2) ) {
                                $image = $settings->$setting2;
                            } else {
                                $image = $defaultLogo[$i];
                            }

                        ?>


                        <div class="col-md col-sm-4 col-6">
                            <div class="form-group">
                                <div class="row justify-content-center py-0">
                                    <h1 class="fs-14 font-weight-bold text-center mt-3 col-12">{{ $labels[$i] }}</h1>
                                    <div class="avatar-upload col-auto position-relative">
                                        <div class="avatar-edit position-absolute right-0px z-index-1 top-2px">
                                            <input type='file' name="input[{{ $input[$i] }}]" id="{{ $input[$i] }}" accept=".png"  class="imageUpload d-none"/>
                                            <label class="d-grid w-40px h-40px mb-0 rounded-pill bg-white text-success fs-20 shadow pointer font-weight-normal align-items-center justify-content-center" for="{{ $input[$i] }}"><i class="fad fa-pencil"></i></label>
                                        </div>
                                        <div class="avatar-preview w-100px h-100px position-relative rounded-pill shadow">
                                            <div class="w-100 h-100 rounded-pill" id="{{ $input[$i] }}-imagePreview" style="background-image: url({{ asset('public/'. $image) }});">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php }?>

                </div>
            </div>
        </div>


        <div class="sbp-preview mt-4">
            <div class="sbp-preview-content">
                <div class="row mx-0">


                    <?php

                        $inputName = ['font_family_link', 'font_family_name', 'font_awesome_link', 'font_awesome_name'];
                        $labels = ['Font Family Link (Google Fonts Required)', 'Font Family Name', 'Font Awesome Link (Only Official)', 'Font Awesome Pro'];

                        for($i=0; $i<count($inputName);$i++){?>
                        <?php $inputs= $inputName[$i];?>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="number-of-employees">
                                    {{ $labels[$i] }}
                                </label>
                                <input class="form-control form-control-solid" id="{{$inputs}}" type="text" placeholder="Enter {{ $labels[$i] }}" name="input[{{$inputs}}]" value="{{$settings->$inputs ?? ''}}">
                            </div>
                        </div>

                    <?php }?>

                </div>
            </div>
        </div>
        <div class="sbp-preview mt-4">
            <div class="sbp-preview-content">
                <div class="row mx-0">

                    <?php
                        $input = ['primary', 'secondary'];
                        $colorcode = ['#0b6ef9', '#6b00c2'];


                    for($i=0; $i<count($input);$i++){?>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color">
                        <div class="form-group mb-lg-3 mb-0 mt-lg-0 mt-3">
                            <label class="text-capitalize font-weight-bold" for="number-of-employees">
                                {{ $input[$i] }} Color <span onclick="copyToClipboard('#hex_code-{{ $input[$i] }}','#{{ $input[$i] }}-copied_hovers')" id="{{ $input[$i] }}-copied_hovers" style="color: var(--color-{{ $input[$i] }}); --switch: calc((var(--{{ $input[$i] }}-color-l) - var(--contrastThreshold)) * -100); background: hsl(0, 0%, var(--switch));" class="font-weight-900 p-1 rounded-5 text-uppercase d-inline-block">( <span id="hex_code-{{ $input[$i] }}"> {{ $colorcode[$i] }} </span> )</span>
                            </label>
                            <?php $setting= $input[$i].'_hex';?>
                            <?php $setting1= $colorcode[$i];?>

                            <input class="form-control form-control-solid h-80px" type="color" value="{{$settings->$setting ?? $setting1}}" id="{{ $input[$i] }}-color-input" name="input[{{ $input[$i] }}_hex]" data-id="{{ $input[$i] }}"/>
                            <input class="form-control form-control-solid" type="hidden" placeholder="{{ $input[$i] }}_hsl" name="input[{{ $input[$i] }}_hsl]">
                            <input class="form-control form-control-solid" type="hidden" placeholder="rgb" name="input[{{ $input[$i] }}_rgb]" id="{{ $input[$i] }}-color-input_rgb">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color z-index-30">
                        <label class="d-lg-block d-md-none d-none">&nbsp;</label>
                        <div class="row mx-0 form-group h-lg-80px h-50px d-grid align-items-center mb-lg-3 mb-0 pt-lg-3 pt-0">
                            <div onclick="copyToClipboard('#copy-color-{{ $input[$i] }}','#{{ $input[$i] }}-copied_hover')" id="{{ $input[$i] }}-copied_hover" class="col-md-12 h-lg-60px h-40px d-flex align-items-center justify-content-center shadow rounded text-lowercase border-0 fs-md-12 fs-11 font-weight-bold bg-{{ $input[$i] }} text-n-{{ $input[$i] }}"><span class="mr-2 position-absolute invisible" id="copy-color-{{ $input[$i] }}">var(--color-{{ $input[$i] }})</span>--color-{{ $input[$i] }} (Base)</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color z-index-20">
                        <label class="d-lg-block d-md-none d-none">&nbsp;</label>
                        <div class="row mx-0 form-group h-lg-80px h-50px d-grid align-items-center mb-lg-3 mb-0 pt-lg-3 pt-0">
                            <div onclick="copyToClipboard('#copy-color-{{ $input[$i] }}-light','#{{ $input[$i] }}-light-copied_hover')" id="{{ $input[$i] }}-light-copied_hover" class="col-md-12 h-lg-60px h-40px d-flex align-items-center justify-content-center shadow rounded text-lowercase border-0 fs-md-12 fs-11 font-weight-bold bg-{{ $input[$i] }}-light text-n-{{ $input[$i] }}-light"><span class="mr-2 position-absolute invisible" id="copy-color-{{ $input[$i] }}-light">var(--color-{{ $input[$i] }}-light)</span>--color-{{ $input[$i] }}-light (:hover)</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color z-index-10">
                        <label class="d-lg-block d-md-none d-none">&nbsp;</label>
                        <div class="row mx-0 form-group h-lg-80px h-50px d-grid align-items-center mb-lg-3 mb-0 pt-lg-3 pt-0">
                            <div onclick="copyToClipboard('#copy-color-{{ $input[$i] }}-dark','#{{ $input[$i] }}-dark-copied_hover')" id="{{ $input[$i] }}-dark-copied_hover" class="col-md-12 h-lg-60px h-40px d-flex align-items-center justify-content-center shadow rounded text-lowercase border-0 fs-md-12 fs-11 font-weight-bold  bg-{{ $input[$i] }}-dark text-n-{{ $input[$i] }}-dark"><span class="mr-2 position-absolute invisible" id="copy-color-{{ $input[$i] }}-dark">var(--color-{{ $input[$i] }}-dark)</span>--color-{{ $input[$i] }}-dark (:hover)</div>
                        </div>
                    </div>
                    <?php }?>



                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 text-right mt-lg-0 mt-4">
                        <div class="form-group mb-4 text-right">
                            <div class="custom-control custom-switch text-right">
                                <input type="checkbox" class="custom-control-input" id="customSwitch1" onchange="show_options(this)">
                                <label class="custom-control-label font-weight-bold text-success fs-16" for="customSwitch1">Advance Color Options</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-0 mt-4" style="display:none" id="options"> 



                    <?php $input = ['info', 'success', 'danger', 'warning', 'muted', 'dark', 'light', 'black', 'white', 'blue', 'indigo', 'purple', 'pink', 'red', 'orange','yellow', 'green', 'teal', 'cyan', 'gray', 'graydark'];

                        $colorcode = ['#17a2b8', '#28a745', '#dc3545', '#ffc107', '#dedede', '#343a40', '#f8f9fa', '#000000', '#ffffff', '#007bff', '#6610f2', '#6f25c1', '#e83e8c', '#dc3545', '#fd7e14','#ffc107', '#28a745', '#20c997', '#17a2b8', '#6c757d', '#272b30'];

                        // for($i=0; $i<count($input);$i++){
                        for($i=0; $i<9;$i++){
                            ?>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color">
                        <div class="form-group mb-lg-3 mb-0 mt-lg-0 mt-3">
                            <label class="text-capitalize font-weight-bold" for="number-of-employees">
                                {{ $input[$i] }} Color <span onclick="copyToClipboard('#hex_code-{{ $input[$i] }}','#{{ $input[$i] }}-copied_hovers')" id="{{ $input[$i] }}-copied_hovers" style="color: var(--color-{{ $input[$i] }}); --switch: calc((var(--{{ $input[$i] }}-color-l) - var(--contrastThreshold)) * -100); background: hsl(0, 0%, var(--switch));" class="font-weight-900 p-1 rounded-5 text-uppercase d-inline-block">( <span id="hex_code-{{ $input[$i] }}"> {{ $colorcode[$i] }} </span> )</span>
                            </label>
                            <?php $setting= $input[$i].'_hex';?>
                            <?php $setting1= $colorcode[$i];?>

                            <input class="form-control form-control-solid h-lg-80px h-md-50px h-50px" type="color" value="{{$settings->$setting ?? $setting1}}" id="{{ $input[$i] }}-color-input" name="input[{{ $input[$i] }}_hex]"  data-id="{{ $input[$i] }}"/>
                            <input class="form-control form-control-solid" type="hidden" placeholder="{{ $input[$i] }}_hsl" name="input[{{ $input[$i] }}_hsl]">
                            <input class="form-control form-control-solid" type="hidden" placeholder="rgb" name="input[{{ $input[$i] }}_rgb]" id="{{ $input[$i] }}-color-input_rgb">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color z-index-30">
                        <label class="d-lg-block d-md-none d-none">&nbsp;</label>
                        <div class="row mx-0 form-group h-lg-80px h-50px d-grid align-items-center mb-lg-3 mb-0 pt-lg-3 pt-0">
                            <div onclick="copyToClipboard('#copy-color-{{ $input[$i] }}','#{{ $input[$i] }}-copied_hover')" id="{{ $input[$i] }}-copied_hover" class="col-md-12 h-lg-60px h-40px d-flex align-items-center justify-content-center shadow rounded text-lowercase border-0 fs-md-12 fs-11 font-weight-bold bg-{{ $input[$i] }} text-n-{{ $input[$i] }}"><span class="mr-2 position-absolute invisible" id="copy-color-{{ $input[$i] }}">var(--color-{{ $input[$i] }})</span>--color-{{ $input[$i] }} (Base)</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color z-index-20">
                        <label class="d-lg-block d-md-none d-none">&nbsp;</label>
                        <div class="row mx-0 form-group h-lg-80px h-50px d-grid align-items-center mb-lg-3 mb-0 pt-lg-3 pt-0">
                            <div onclick="copyToClipboard('#copy-color-{{ $input[$i] }}-light','#{{ $input[$i] }}-light-copied_hover')" id="{{ $input[$i] }}-light-copied_hover" class="col-md-12 h-lg-60px h-40px d-flex align-items-center justify-content-center shadow rounded text-lowercase border-0 fs-md-12 fs-11 font-weight-bold bg-{{ $input[$i] }}-light text-n-{{ $input[$i] }}-light"><span class="mr-2 position-absolute invisible" id="copy-color-{{ $input[$i] }}-light">var(--color-{{ $input[$i] }}-light)</span>--color-{{ $input[$i] }}-light (:hover)</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 ui_settings_color z-index-10">
                        <label class="d-lg-block d-md-none d-none">&nbsp;</label>
                        <div class="row mx-0 form-group h-lg-80px h-50px d-grid align-items-center mb-lg-3 mb-0 pt-lg-3 pt-0">
                            <div onclick="copyToClipboard('#copy-color-{{ $input[$i] }}-dark','#{{ $input[$i] }}-dark-copied_hover')" id="{{ $input[$i] }}-dark-copied_hover" class="col-md-12 h-lg-60px h-40px d-flex align-items-center justify-content-center shadow rounded text-lowercase border-0 fs-md-12 fs-11 font-weight-bold  bg-{{ $input[$i] }}-dark text-n-{{ $input[$i] }}-dark"><span class="mr-2 position-absolute invisible" id="copy-color-{{ $input[$i] }}-dark">var(--color-{{ $input[$i] }}-dark)</span>--color-{{ $input[$i] }}-dark (:hover)</div>
                        </div>
                    </div>
                    <?php }?>

                </div>
                <div class="row mx-0">
                    <div class="col text-left mt-4 mb-2" id="resetcolor" style="display: none">
                        <div class="d-inline-block" data-toggle="tooltip" title="Reset Factory (Theme color, Font family)">
                            <a class="btn btn-sm btn-danger text-uppercase" data-toggle="modal" data-target="#resetthemefont"><i class="far fa-redo-alt"></i>&nbsp;Reset Theme</a>
                        </div>
                    </div>
                    <div class="col-auto text-right ml-auto mt-4 mb-2">
                        <button type="submit" class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="resetthemefont" tabindex="-1" aria-labelledby="resetthemefontLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <form class="card-body" action="{{action('SettingsController@reset_admin_theme')}}" method="post">
        @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resetthemefontLabel"><b class="text-black">Are you sure?</b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-dark mb-0">You want to reset color theme and font family.</p>
          <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="mpassword">&nbsp;</label>
                    <input class="form-control form-control-solid" id="mpassword" type="password" placeholder="Enter Master Password" name="masterpassword">
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm text-uppercase btn-muted" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-sm text-uppercase btn-danger"><i class="fad fa-redo-alt"></i>&nbsp;Reset</button>
        </div>
      </div>
    </form>
    </div>
  </div>

<script>
$(".image_to_upload").change(function (){
    var fileName = $(this).val();
    var idxDot = fileName.lastIndexOf(".") + 1;
    var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
    if (extFile=="png" || extFile=="PNG"){
        return true;
    }else{
        Swal.fire('Only png files are allowed!');
        $(this).val('');
        return false;
    }
});
</script>
<script>
function hex2rgb(element) {
            // var hex_color = document.getElementById('hex-color').value
            //     , rgb_color = document.getElementById('rgb-color')
            //     , pattern_color = "^#([A-Fa-f0-9]{6})$";

            var hex_color = element.value
                , pattern_color = "^#([A-Fa-f0-9]{6})$";

            if (hex_color.match(pattern_color)) {
                var hex_color = hex_color.replace("#", "")
                    , r = parseInt(hex_color.substring(0, 2), 16)
                    , g = parseInt(hex_color.substring(2, 4), 16)
                    , b = parseInt(hex_color.substring(4, 6), 16);

                return r + ' ' + g + ' ' + b;
            }
            else {
                Swal.fire('Error Color Format');
            }
        }
</script>

<script>

    function rgbToHsl(r, g, b){
		r /= 255, g /= 255, b /= 255;
		var max = Math.max(r, g, b), min = Math.min(r, g, b);
		var h, s, l = (max + min) / 2;

		if (max == min) { h = s = 0; }
		else {
			var d = max - min;
			s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

			switch (max){
				case r: h = (g - b) / d + (g < b ? 6 : 0); break;
				case g: h = (b - r) / d + 2; break;
				case b: h = (r - g) / d + 4; break;
			}

			h /= 6;
		}

		return [(h*100+0.5)|0, ((s*100+0.5)|0) + '%', ((l*100+0.5)|0) + '%'];
	}


</script>

<script>
$('input[type="color"]').change(
    function() {
        $('#' + this.id + '_rgb').val( hex2rgb(this) );

        $('#hex_code-' + $(this).attr('data-id') + '').text( this.value );

        var rgb = hex2rgb(this);

        var r = rgb.split(' ')[0];
        var g = rgb.split(' ')[1];
        var b = rgb.split(' ')[2];

        // alert( rgbToHsl(r, g, b) );

    }
);
</script>

<script>
   function show_options(element) {

       $('#options').toggle();
       $('#resetcolor').toggle();
   }
    </script>




<script>
    // alert( $('#primary-color-input').val() );
</script>

@endsection('content')
