@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    View All Users
@endsection('sub-heading')


@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('RegisteruserController@index')?>">
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
                                        <option value="activated" <?php if($status=='activated' ){ echo 'selected'; }?>>Activated</option>
                                        <option value="deactivated" <?php if($status=='deactivated' ){ echo 'selected'; }?>>Block</option>
                                        <option value="pan" <?php if($status=='pan' ){ echo 'selected'; }?>>Not Uploaded Pan</option>
                                        <option value="bank" <?php if($status=='bank' ){ echo 'selected'; }?>>Not Uploaded Bank</option>
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
                            <div class="col-md-auto text-right mb-md-3">

                                <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('RegisteruserController@index')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Users</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                  <form action="<?php echo action('RegisteruserController@downloadalluserdetails');?>" method="get">
                   <input type="hidden" name="status" value="<?php echo $status;?>">
                    <input type="hidden" name="name" value="<?php echo $name;?>">
                    <input type="hidden" name="email" value="<?php echo $email;?>">
                    <input type="hidden" name="mobile" value="<?php echo $mobile;?>">
                    <button type="submit" class="btn btn-secondary text-uppercase btn-sm rounded-pill" font-weight-600 data-toggle="tooltip" title="Download All Users Details"><i class="fas fa-download"></i>&nbsp; Download
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')

        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="view_users_datatable" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th data-toggle="tooltip" title="User ID">U ID</th>
            <th>Team Name</th>
            <th>Email</th>
            <th>Mobile No.</th>
            <th>Verification</th>
            <th data-toggle="tooltip" title="Total Referrals">T. R.</th>
            <th data-toggle="tooltip" title="Referring Amount (RS.)">R. Amt (₹)</th>
            <th data-toggle="tooltip" title="Refer Code">Refer C.</th>
            <!-- <th>Maximum <br>Withdrawl
                <br>Amount</th> -->
            <th>Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                  <th data-toggle="tooltip" title="User ID">U ID</th>
                <th>Team Name</th>
                <th>Email</th>
                <th>Mobile No.</th>
                <th>Verification</th>
                <th data-toggle="tooltip" title="Total Referrals">T. R.</th>
                <th data-toggle="tooltip" title="Referring Amount (RS.)">R. Amt (₹)</th>
                <th data-toggle="tooltip" title="Refer Code">Refer C.</th>
                <!-- <th>Maximum <br>Withdrawl
                    <br>Amount</th> -->
                <th>Action</th>
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
        $.fn.dataTable.ext.errMode = 'none';
        $('#view_users_datatable').DataTable({
            'bFilter':false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo asset('my-admin/view_users_datatable');?>?&name=' + name + '&email=' + email + '&mobile=' + mobile + '&status=' + status,
                "dataType": "json",
                "type": "POST",
                "data": {
                    _token: "{{csrf_token()}}"
                }
            },
         "columns": [
            { "data": "id" },
            { "data": "team"},
            { "data": "email" },
            { "data": "mobile" },
            { "data": "verification" },
            { "data": "total_refers" },
            { "data": "refer_amount" },
            { "data": "refercode" },
            // { "data": "maximum_withdrawl" },
            { "data": "action" }
        ]

        });

    });
</script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
<!--update withdraw script-->
<script>
function update_withdraw(userid,team,amount){
    var r = confirm("Are you sure to update the Maximum withdraw amount for "+team);
    if (r == true) {
      $.ajax({
         type:'POST',
         url:'<?php echo asset("my-admin/update_withdraw");?>',
         data:'_token=<?php echo csrf_token();?>&userid='+userid+'&withdrawamount='+amount,
         success:function(data){
           if(data==1){
             location.reload();
           }
           if(data==2){
               Swal.fire('Minimum Withdraw amount should be Rs. 300');
           }
        }
      });
    }

}


</script>
@endsection('content')
