
@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    View All Refer Users
@endsection('sub-heading')


@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          {{ Form::open(array('action' => ['RegisteruserController@allrefer',$id], 'method' => 'get','id' => 'j-forms','class'=>'j-forms' ))}}
                  <?php
                    $status="";$name="";$email="";$mobile="";$code="";
                    if(isset($_GET['email'])){
                    $email = $_GET['email'];
                    }
                    if(isset($_GET['name'])){
                    $name = $_GET['name'];
                    }
                    if(isset($_GET['status'])){
                    $status = $_GET['status'];
                    }
                    if(isset($_GET['mobile'])){
                    $mobile= $_GET['mobile'];
                    }if(isset($_GET['code'])){
                    $code= $_GET['code'];
                    }
                ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-end">

                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Status','Status',array('class'=>'control-label text-bold '))}}
                                    <select class="form-control form-control-solid input-sm p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Status" data-hide-disabled="true" name="status" id="status">
                                        <option value="">Select Status</option>
                                        <option value="activated" <?php if($status=='activated'){ echo 'selected'; }?>>Activated</option>
                                        <option value="deactivated" <?php if($status=='deactivated'){ echo 'selected'; }?>>Block</option>      
                                        <option value="pan" <?php if($status=='pan'){ echo 'selected'; }?>>Not Uploaded Pan</option>      
                                        <option value="bank" <?php if($status=='bank'){ echo 'selected'; }?>>Not Uploaded Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Team Name','Team Name',array('class'=>'control-label text-bold input-sm'))}} {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Team Name','id'=>'name','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Email','Email',array('class'=>'control-label text-bold'))}} {{ Form::text('email',$email,array('value'=>$email,'placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Mobile','Mobile',array('class'=>'control-label text-bold'))}} {{ Form::text('mobile',$mobile,array('value'=>$mobile,'placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid', 'maxlength'=>"10", 'pattern' => "[1-9]{1}[0-9]{9}",
                                    'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                    ))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Refer Code','Refer Code',array('class'=>'control-label text-bold'))}} {{ Form::text('code',$code,array('value'=>$code,'placeholder'=>'Search By Refer Code','id'=>'code','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-auto text-right mb-md-3">

                                <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('RegisteruserController@allrefer', $id)?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <div class="row w-100 align-items-center mx-0">
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Refer Users</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                  
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')
         <input type="hidden" value="<?php echo $id; ?>" id="myid">

        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="view_users_datatable" width="100%" cellspacing="0">
                <thead>
            <tr>
                <th>User ID</th>
              <th style="width: 78%;">Team Name</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th>Verification</th>
              <th>Refer<br>Code</th>
              <th>Cash Added</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                  <th>User ID</th>
              <th style="width: 78%;">Team Name</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th>Verification</th>
              <th>Refer<br>Code</th>
              <th>Cash Added</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function() { 
  var name = document.getElementById('name').value;
  var email = document.getElementById('email').value;
  var mobile = document.getElementById('mobile').value;
  var status = document.getElementById('status').value;   
  var code = document.getElementById('code').value;   
  var rrrid = document.getElementById('myid').value;
  $.fn.dataTable.ext.errMode = 'none';
      $('#view_users_datatable').DataTable({
          bFilter: false,
      "processing": true,
          "serverSide": true,
          "ajax":{
                   "url": '<?php echo URL::asset('my-admin/view_refer_datatable');?>?name='+name+'&shid='+rrrid+'&email='+email+'&mobile='+mobile+'&status='+status+'&code='+code,
                   "dataType": "json",
                   "type": "POST",
                   "data":{ _token: "{{csrf_token()}}"}
                 },
          "columns": [
              { "data": "id" },
              { "data": "team" },
              { "data": "email" },
              { "data": "mobile" },
              { "data": "verification" },
              { "data": "refercode" },
              { "data": "balance" }
          ]  

      });
        
});
</script>
<script>
  $(document).ready(function(){
    $.ajaxSetup({
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
  });
</script>
@endsection('content')
