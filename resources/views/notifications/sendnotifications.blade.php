@extends('main')

@section('heading')
    Notification Manager
@endsection('heading')

@section('sub-heading')
    Send Android App Notification
@endsection('sub-heading')

@section('content')

<div class="card  md-4">
    <div class="card-header">Send App Notification</div>
      {{ Form::open(array('action' => 'NotificationController@pushnotifications', 'method' => 'post','id' => 'j-forms','class'=>'j-forms','enctype'=>'multipart/form-data' ))}}

          {{csrf_field()}}

        @include('alert_msg')
        
        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-12">
                       <input type="hidden" id="uservalues" name="uservalues">
                        <div class="form-group my-3">
                          {{ Form::label('Select User*','Select User*',array('class'=>'d-flex'))}}
                          <div class="custom-control custom-radio custom-control-inline">
                              <input type="radio" id="customRadio1" name="usertype" value="all" checked onclick="divshowhide('all')" class="custom-control-input">
                              <label class="custom-control-label fs-15" for="customRadio1">All Users</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                              <input type="radio" id="customRadio2" name="usertype" class="custom-control-input" value="specific" onclick="divshowhide('specific')">
                              <label class="custom-control-label fs-15" for="customRadio2">Specific users</label>
                            </div>
                            <!--<input type="radio" name="usertype" value="all" checked onclick="divshowhide('all')"> All Users
                            <input type="radio" name="usertype" value="specific" onclick="divshowhide('specific')"> Specific users-->
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group my-3 d-none" id="specificdiv">
                             {{ Form::label('Search User*','Search User*',array('class'=>''))}}
                           {{ Form::text('users',null,array('value'=>'','placeholder'=>'Search Users','class' => 'multipleInputDynamic form-control form-control-solid','id'=>'selectusers'))}}

                           <?php $i=0;
                          if(!empty($excel_data)){
                              foreach($excel_data as $key){
                                  if(!empty($key)){
                                        $showname = $key[0]->id.'('.$key[0]->email.')'.' | '.$key[0]->mobile;?>
                                        <div id="dsafd<?php echo $key[0]->id; ?>">
                                      <input type="hidden" name="uservaluess[]" readonly value="<?php echo $key[0]->id; ?>">
                                      <div class="my-1 mx-0 p-2 border pointer" id="userid1<?php echo $key[0]->id; ?> "><?php echo $showname; ?>
                                      <span class="text-danger pointer" onclick="deletediv1(this,<?php echo $key[0]->id; ?>)">Delete</span></div></div>
                        <?php }} }?>
                        </div>
                         <div id="boxx" class="saerach-box-list d-none position-absolute left-15px right-15px top-91px bg-white shadow rounded border border-primary z-index-1 px-3 py-2" >
                                <ul id="item_list" class="d-none list-unstyled mb-0"></ul>
                            </div>
                            <div id="showusers" class="row" >
                            </div>
                    </div>
                     <div class="col-12">
                        <div class="form-group my-3">
                          {{ Form::label('Notification Title*','Notification Title*',array('class'=>''))}}

                           {{ Form::text('title',null,array('value'=>'','required'=>'','placeholder'=>'Enter Notification Title Here','class' => 'form-control form-control-solid multipleInputDynamic','autocomplete'=>'off','id'=>'first-name'))}}
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group my-3">
                          {{ Form::label('Enter Message*','Enter Message*',array('class'=>''))}}
                            <textarea class="form-control form-control-solid" required placeholder="Enter Message Here" name="message"></textarea>
                        </div>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
                       <a class="btn btn-sm btn-warning" onclick='window.location.href=window.location.href'><i class="fas fa-undo" ></i>&nbsp; Reset</a>
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle" ></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
    {{Form::close()}}
</div>

@endsection
