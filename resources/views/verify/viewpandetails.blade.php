@extends('main')
<?php
use App\Helpers\Helpers;
?>
@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
    Pan Card Verification Requests List
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('VerificationController@verifypan')?>">
        <?php
      $status="0";$name="";$email="";$pannumber="";$mobile="";
      if(isset($_GET['email'])){
        $email = $_GET['email'];
      }
      if(isset($_GET['name'])){
        $name = $_GET['name'];
      }
      if(isset($_GET['status'])){
        $status = $_GET['status'];
      }
      if(isset($_GET['pannumber'])){
        $pannumber = $_GET['pannumber'];
      }
      if(isset($_GET['mobile'])){
        $mobile = $_GET['mobile'];
      }
     ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-end">

                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Status','Status',array('class'=>'control-label text-bold'))}}
                                    <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Status" data-hide-disabled="true" name="status" id="status">
                                    <option value="">Select Status</option>
                                    <option value="1" <?php if($status=='1'){ echo 'selected'; }?>>Activated</option>
                                    <option value="0" <?php if($status=='0'){ echo 'selected'; }?>>Pending</option>
                                    <option value="2" <?php if($status=='2'){ echo 'selected'; }?>>Cancel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Name','Search By Name',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('name',$name,array('value'=>'','placeholder'=>'Search By Name','id'=>'name','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Email','Search By Email',array('class'=>'control-label text-bold'))}} <br>
                                    {{ Form::text('email',$email,array('value'=>'','placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Mobile','Search By Mobile',array('class'=>'control-label text-bold'))}}<br>
                                    {{ Form::text('mobile',$mobile,array('value'=>'','placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid', 'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');", 'maxlength' => "10", 'pattern' => "[1-9]{1}[0-9]{9}"))}}
                                </div>
                            </div>
                            <div class="col-md-auto text-right mb-md-3">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('VerificationController@verifypan')?>" class="btn btn-sm text-uppercase btn-warning"><i class="far fa-undo" ></i>&nbsp; Reset</a>

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
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">Pan Card Verification Requests List</div>
            
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')

      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">

        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap display" id="dataTable4" width="100%" cellspacing="0">
                <thead>
            <tr>
              <th>S.No.</th>
              <th>UID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th>DOB</th>
              <th>PAN Number</th>
              <th>Image</th>
              <th>Status</th>
              <th>Your Comment</th>
              <th>Uploading Date</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
            <th>S.No.</th>
            <th>UID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th>DOB</th>
              <th>PAN Number</th>
              <th>Image</th>
              <th>Status</th>
              <th>Uploading Date</th>
              <th>Action</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>

<script>
    // $(document).ready(function (){
    //     var table = $('#dataTable4').DataTable({
    //         'responsive': true
    //     });

    //     // Handle click on "Expand All" button
    //     $('#btn-show-all-children').on('click', function(){
    //         // Expand row details
    //         table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
    //     });

    //     // Handle click on "Collapse All" button
    //     $('#btn-hide-all-children').on('click', function(){
    //         // Collapse row details
    //         table.rows('.parent').nodes().to$().find('td:first-child').trigger('click');
    //     });
    // });
</script>
<script type="text/javascript">
    $(document).ready(function() {
    var status = $('#status').val();
    var name = $('#name').val();
    var email = $('#email').val();
    var mobile = $('#mobile').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#dataTable4').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo asset('my-admin/verifypan_table');?>?status='+status+'&name='+name+'&email='+email+'&mobile='+mobile,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sno"},
                { "data": "id"},
                { "data": "username" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "pan_dob" },
                { "data": "pan_number" },
                { "data": "image" },
                { "data": "status" },
                { "data": "comment" },
                { "data": "upload" },
                { "data": "action" }
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
