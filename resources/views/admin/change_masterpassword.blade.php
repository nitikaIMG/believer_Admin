@extends('layouts.app')

@section('content')
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
                        <div class="card mt-5 mb-3">
                            <div class="card-body p-3 text-center">
                                <div class="h3 font-weight-light">Change Master Password</div>
                            </div>
                            <hr class="my-0 border-light" />
                            <div class="card-body p-4">
                                {!! Form::open(array('method' =>'post', 'action' => 'HomeController@change_masterpassword','files' => true,'id' => 'j-forms','class'=>'j-forms')) !!}
                            {{csrf_field()}}
                            
                                @include('alert_msg')


                                    <div class="form-group">
                                        <label class="text-gray-600 small" for="current_masterpassword">Current Master Password*</label>
                                        <input class="form-control form-control-solid py-4" id="current_masterpassword" type="password" placeholder="Enter Current Master Password" name="current_masterpassword" required />
                                    </div>

                                    <div class="form-group">
                                        <label class="text-gray-600 small" for="new_masterpassword">New Master Password*</label>
                                        <input class="form-control form-control-solid py-4" id="new_masterpassword" type="password" placeholder="Enter New Master Password" name="new_masterpassword" required/>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-gray-600 small" for="confirm_masterpassword">Confirm Master Password*</label>
                                        <input class="form-control form-control-solid py-4" id="confirm_masterpassword" type="password" placeholder="Enter Your Confirm Master Password" name="confirm_masterpassword" required/>
                                    </div>


                                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                                        <button type="submit" class="btn rounded-pill btn-primary btn-block"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                    </div>




                                    {!! Form::close() !!}
                            </div>
                        </div>

                        <div class="card my-0 bg-transparent shadow-none border-0">
                            <div class="card-body p-3 text-center bg-transparent">
                                <a href="{{ asset('/my-admin')}}" class="btn shadow-none"><i class="fad fa-long-arrow-alt-left"></i>&nbsp;&nbsp;&nbsp; Return to Dashboard </a>
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











