@extends('main')

@section('heading')
    Sub Admin Manager
@endsection('heading')

@section('sub-heading')
    View Sub Admin
@endsection('sub-heading')

@section('card-heading-btn')
    <a  href="<?php echo action('SubAdminController@add_sub_admin') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right"  style="float: right;" data-toggle="tooltip" title="Add Sub admin"><i class="fa fa-plus"></i> Add</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card mb-3">
  <div class="card-body">      
    <div class="sbp-preview">
        <div class="sbp-preview-content p-3">
          {{ Form::open(array('url' => 'my-admin/view_sub_admin', 'method' => 'get','id' => 'j-forms','class'=>'j-forms row' ))}}

            <?php

              $name="";$email="";$mobile="";
              if(isset($_GET['name'])){
                $name = $_GET['name'];
              }
              if(isset($_GET['email'])){
                $email = $_GET['email'];
              }
              if(isset($_GET['mobile'])){
                $mobile = $_GET['mobile'];
              }
            ?>

            <div class="col-md-4 pull-left">
                <label for="name1" class="control-label text-bold">Sub Admin Name</label>
              {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Sub Admin Name','id'=>'name1','class'=>'form-control form-control-solid','style'=>'color:black;'))}}
            </div>

            <div class="col-md-4 pull-left">
              {{ Form::label('Email','Email',array('class'=>'control-label text-bold'))}}
              {{ Form::text('email',$email,array('value'=>$email,'placeholder'=>'Search By email','id'=>'email','class'=>'form-control form-control-solid','style'=>'color:black;'))}}
            </div>

            <div class="col-md-4 pull-left">
              {{ Form::label('Mobile','Mobile',array('class'=>'control-label text-bold'))}}
              <input name="mobile" class="form-control form-control-solid" type="text" 
                placeholder="Search By Mobile" id="mobile"
                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                maxlength="10" pattern="[1-9]{1}[0-9]{9}" value="{{$mobile}}">
            </div>

            <div class="col-12 text-right mt-4 mb-2">
              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                <a href="<?php echo action('SubAdminController@view_sub_admin')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
            </div>
          {{Form::close()}}
        </div>
    </div>
  </div>
</div>

<div class="card">      
  <div class="card-header">View Sub Admin</div>
  <div class="card-body">
    
    <div class="datatable table-responsive">
      <table class="table table-bordered table-striped table-hover text-nowrap" id="allmatches_datatable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>S No.</th>
            <th>name</th>
            <th>email</th>
            <th>mobile</th>
            <th>password</th>
            <th>permission</th>
            <th>action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>S No.</th>
            <th>name</th>
            <th>email</th>
            <th>mobile</th>
            <th>password</th>
            <th>permission</th>
            <th>action</th>
          </tr>
        </tfoot>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {   
    $.fn.dataTable.ext.errMode = 'none';

    var name = $('#name1').val();
    var email = $('#email').val();
    var mobile = $('#mobile').val();
        $('#allmatches_datatable').DataTable({
        'bFilter':false,
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax":{
                      "url": '<?php echo URL::asset('my-admin/view_sub_admin_dt');?>?name='+name+'&email='+email+'&mobile='+mobile,
                      "dataType": "json",
                      "type": "POST",
                      "data":{ _token: "{{csrf_token()}}"}
                    },
              "dom": 'lBfrtip',
              "buttons": [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ],
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "password" },
                { "data": "permissions" },
                { "data": "action" },
            ]  
        });
        
});
</script>

@endsection