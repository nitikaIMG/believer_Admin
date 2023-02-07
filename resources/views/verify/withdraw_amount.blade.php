@extends('main')

@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
    Withdrawal Requests List
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('VerificationController@withdraw_amount')?>">
         <?php
            $email="";$start_date="";$end_date="";$status="0";$mobile="";
            if(isset($_GET['start_date'])){
              $start_date = $_GET['start_date'];
            }
            if(isset($_GET['end_date'])){
              $end_date = $_GET['end_date'];
            }
            if(isset($_GET['email'])){
              $email = $_GET['email'];
            }
            if(isset($_GET['status'])){
              $status = $_GET['status'];
            }
            if(isset($_GET['mobile'])){
              $mobile = $_GET['mobile'];
            }
           ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-end">

                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Status','Status',array('class'=>'control-label text-bold'))}}
                                    <select class="form-control form-control-solid p-1 selectpicker show-tick" data-container="body" data-live-search="true" title="Select Status" data-hide-disabled="true" name="status" id="status">
                                      <option value="">Select Status </option>
                                      <option value="1" @if($status=='1') selected @endif>Approved</option>
                                      <option value="0" @if($status=='0') selected @endif>Pending</option>
                                      <option value="2" @if($status=='2') selected @endif>Cancel</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Requested Date','Requested Date',array('class'=>'label-control text-bold'))}}
                                    {{ Form::text('start_date',$start_date,array('value'=>'','placeholder'=>'Search By Requested date','id'=>'start_date','class'=>'form-control form-control-solid datetimepickerget'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Search By Approved Date','Approved Date',array('class'=>'label-control text-bold'))}}
                                    {{ Form::text('end_date',$end_date,array('value'=>'','placeholder'=>'Search By Approved date','id'=>'end_date','class'=>'form-control form-control-solid datetimepickerget'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Email','Email',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('email',$email,array('value'=>$email,'placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Mobile','Mobile',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('mobile',$mobile,array('value'=>$mobile,'placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-auto text-right mb-md-3">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="{{ action('VerificationController@withdraw_amount') }}" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">Withdrawal Requests List</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                  <form action="{{ action('VerificationController@downloadwithdrawaldata') }}" method="get">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="hidden" name="start_date" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" value="{{ $end_date }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="mobile" value="{{ $mobile }}">
                    <button type="submit" data-toggle="tooltip" font-weight-600 class="btn btn-secondary text-uppercase btn-sm rounded-pill fs-md-13 fs-11" title="Download All Withdrawals Details"><i class="fad fa-download"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">

    @include('alert_msg')

        
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="dataTable5" width="100%" cellspacing="0">
                <thead>
            <tr>
              <th>S.No.</th>
              <th>User Id</th>
              <th data-toggle="tooltip" title="Account Number">A/c No.</th>
              <th data-toggle="tooltip" title="Withdrawal Amount">Withdrawal Amt.</th>
              <th>User Name</th>
              <th data-toggle="tooltip" title="Withdraw Request Id">W. Request Id</th>
              <th>Transfer ID</th>
              <th>Bank IFSC Code</th>
              <th>Bank Name</th>
              <th>Bank Branch</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Requested Date</th>
              <th><span title="Approved / Rejected Date">Aprd./ Rjctd. Date</span></th>
              <th>Withdraw Type</th>
              <th>Admin Comment</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
              <th>S.No.</th>
              <th>User Id</th>
              <th data-toggle="tooltip" title="Account Number">A/c No.</th>
              <th data-toggle="tooltip" title="Withdrawal Amount">Withdrawal Amt.</th>
              <th>User Name</th>
              <th data-toggle="tooltip" title="Withdraw Request Id">W. Request Id</th>
              <th>Transfer ID</th>
              <th>Bank IFSC Code</th>
              <th>Bank Name</th>
              <th>Bank Branch</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Requested Date</th>
              <th><span title="Approved / Rejected Date">Aprd./ Rjctd. Date</span></th>
              <th>Withdraw Type</th>
              <th>Admin Comment</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>


<script>
    $(document).ready(function (){

        // Handle click on "Expand All" button
        $('#btn-show-all-children').on('click', function(){
            // Expand row details
            table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
        });

        // Handle click on "Collapse All" button
        $('#btn-hide-all-children').on('click', function(){
            // Collapse row details
            table.rows('.parent').nodes().to$().find('td:first-child').trigger('click');
        });
    });
</script>
<script>

$(document).on('click', '#approve_btn',
  function() {
    var wid = $(this).attr('value');
    $('input[name="approve"]').prop('disabled', false);
    $('input[name="cancel"]').prop('disabled', true);
  }
);

$(document).on('click', '#cancel_btn',
  function() {
    var wid = $(this).attr('value');
    $('input[name="cancel"]').prop('disabled', false);
    $('input[name="approve"]').prop('disabled', true);
  }
);

function show_hide_comment(id) {
  $('.comment-modal'+id).toggle('hide');
  // $('body').toggleClass('overflow-hidden');
}

</script>

<script type="text/javascript">
    $(document).ready(function() {
    var status = $('#status').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var email = $('#email').val();
    var mobile = $('#mobile').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#dataTable5').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
             'responsive': true,
            "ajax":{
                     "url": '<?php echo asset('my-admin/withdrawl_amount_table');?>?status='+status+'&start_date='+start_date+'&email='+email+'&mobile='+mobile+'&end_date='+end_date,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sno"},
                { "data": "userid"},
                { "data": "acc_no" },
                { "data": "withdraw_amount" },
                { "data": "username" },
                { "data": "withdraw_request_id" },
                { "data": "transfer_id" },
                { "data": "ifsc" },
                { "data": "bankname" },
                { "data": "bankbranch" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "withdraw_request" },
                { "data": "approved_date" },
                { "data": "with_type" },
                { "data": "comment" }
            ]

        });

});
</script>
@endsection('content')
