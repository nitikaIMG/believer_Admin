@extends('main')

@section('heading')
    Notification Manager
@endsection('heading')

@section('sub-heading')
   Send Email Notification
@endsection('sub-heading')

@section('content')


<div class="card">
    <div class="card-header">Send Email Notification</div>
      {{ Form::open(array('action' => 'NotificationController@emailnotifications', 'method' => 'post','id' => 'j-forms','class'=>'j-forms','enctype'=>'multipart/form-data' ))}}

          {{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-12">
                       <input type="hidden" id="uservalues" name="uservalues">
                        <div class="form-group my-3">
                            <label for="usertype" class=" d-flex">Select User*</label>
                            <div class="custom-control custom-radio custom-control-inline">
                              <input type="radio" id="customRadio1" name="usertype" value="all" checked onclick="divshowhide('all')" class="custom-control-input">
                              <label class="custom-control-label fs-15" for="customRadio1">All Users</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                              <input type="radio" id="customRadio2" name="usertype" class="custom-control-input" value="specific" onclick="divshowhide('specific')">
                              <label class="custom-control-label fs-15" for="customRadio2">Specific users</label>
                            </div>
                           <!-- <input type="radio" name="usertype" id="usertype" value="all" checked onclick="divshowhide('all')"> All Users
                            <input type="radio" name="usertype" id="usertype" value="specific" onclick="divshowhide('specific')"> Specific users -->
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group my-3 d-none" id="specificdiv">
                             {{ Form::label('Search User*','Search User*',array('class'=>''))}}
                           {{ Form::text('users',null,array('value'=>'','placeholder'=>'Search Users','class' => 'multipleInputDynamic form-control form-control-solid','id'=>'selectusers'))}}
                        </div>
                         <div id="boxx" class="saerach-box-list position-absolute left-15px right-15px top-91px bg-white shadow rounded border border-primary z-index-1 px-3 py-2 d-none">
                                <ul id="item_list" class="d-none list-unstyled mb-0" ></ul>
                          </div>
                          <div id="showusers" class="row">
                          </div>
                    </div>
                     <div class="col-12">
                        <div class="form-group my-3">
                            <label for="first-name">Notification Title*</label>
                             <input class="form-control form-control-solid" id="first-name" type="text" placeholder="Enter Notification Title Here" name="title" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group my-3">
                            <label for="message">Enter Message*</label>
                            <textarea class="form-control form-control-solid" required placeholder="Enter Message Here" name="message" id="message"></textarea>
                        </div>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
                       <a class="btn btn-sm btn-warning" onclick='window.location.href=window.location.href'><i class="fa fa-undo" ></i>&nbsp; Reset</a>
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
    {{Form::close()}}
    @endsection
