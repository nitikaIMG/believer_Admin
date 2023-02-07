@extends('main_youtuber')
@section('content')
<?php echo $id; ?>
<style>
    #pageloader-overlay {
        background-color : none;
    }
    #pageloader-overlay.visible {
         opacity: 0 !important;
    }
</style>
<div class="page-content">
    <div class="container-fluid">

        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2" id="page-head" id="page-head">
            <div class="col-sm-12 my-3">
		        <h4 class="page-title">View All Refer Users</h4>
		        <ol class="breadcrumb">
		            <li class="breadcrumb-item"><a href="{{ action('DashboardController@index') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="javaScript:void();">Youtuber Manager</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View All Refer Users</li>
                </ol>
	        </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel col-md-12">
                    <div class="panel-body">
                        {{ Form::open(array('action' => ['YoutuberHomeController@allrefer',$id], 'method' => 'get','id' => 'j-forms','class'=>'j-forms row' ))}}
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
        <div class="col-md-2 pull-left">
          {{ Form::label('Status','Status',array('class'=>'control-label text-bold'))}}
          <select class="form-control form-control-solid p-1" name="status" id="status">
            <option value="">Select Status</option>
            <option value="activated" <?php if($status=='activated'){ echo 'selected'; }?>>Activated</option>
            <option value="deactivated" <?php if($status=='deactivated'){ echo 'selected'; }?>>Block</option>
            <option value="pan" <?php if($status=='pan'){ echo 'selected'; }?>>Not Uploaded Pan</option>
            <option value="bank" <?php if($status=='bank'){ echo 'selected'; }?>>Not Uploaded Bank</option>
            </select>
        </div>
        <div class="col-md-2 pull-left">
          {{ Form::label('Team Name','Team Name',array('class'=>'control-label text-bold'))}}
          {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Team Name','id'=>'name','class'=>'form-control form-control-solid'))}}
        </div>
        <div class="col-md-2 pull-left">
          {{ Form::label('Email','Email',array('class'=>'control-label text-bold'))}}
          {{ Form::text('email',$email,array('value'=>$email,'placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
        </div>
        <div class="col-md-2 pull-left">
          {{ Form::label('Mobile','Mobile',array('class'=>'control-label text-bold'))}}
          {{ Form::text('mobile',$mobile,array('value'=>$mobile,'placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid'))}}
        </div>
        <div class="col-md-2 pull-left">
          {{ Form::label('Special Code','Special Code',array('class'=>'control-label text-bold'))}}
          {{ Form::text('code',$code,array('value'=>$code,'placeholder'=>'Search By Special Code','id'=>'code','class'=>'form-control form-control-solid'))}}
        </div>
        <div class="col-md-2 pull-left"  style="padding: 30px 0px 0px 10px;">
          <button type="submit" class="btn-sm btn btn-sm btn-success ml-1"><i class="fa fa-sign-in" ></i>&nbsp;   Submit</button>
          <a href="{{action('YoutuberHomeController@allrefer')}}" class="btn-sm btn btn-sm btn-warning ml-1"><i class="fa fa-undo"></i>&nbsp;Reset</a>


        </div>
        {{Form::close()}}
         </div>
         </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="panel col-md-12">
           <div class="panel-body" style="overflow:auto;">

            @include('alert_msg')

         <input type="hidden" value="<?php echo $id; ?>" id="myid">
         <table id="view_users_datatable" class="table table-striped thead_FixId table-bordered dataTable no-footer dtr-inline" cellspacing="0" width="100%" role="grid" aria-describedby="demo-dt-basic_info" style="width: 100%;">
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
        </table>
         </div>
         </div>
      </div>
    </div>


    </div>
    <!-- End container-fluid-->

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
            'bFilter':false,
      "processing": true,
          "serverSide": true,
          "ajax":{
                   "url": '<?php echo URL::asset('my-admin/view_refer_datatable_youtuber');?>?name='+name+'&shid='+rrrid+'&email='+email+'&mobile='+mobile+'&status='+status+'&code='+code,
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
@endsection
