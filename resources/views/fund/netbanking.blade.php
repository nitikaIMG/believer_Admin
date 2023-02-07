@extends('main')

@section('heading')
    Fund Manager
@endsection('heading')

@section('sub-heading')
    NetBanking Report
@endsection('sub-heading')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('FundController@netbanking')?>">
                  <?php
                    $startdate="";$playername="";$enddate="";$mobile="";
                    if(isset($_GET['startdate'])){
                      $startdate = $_GET['startdate'];
                    }
                    if(isset($_GET['playername'])){
                      $playername = $_GET['playername'];
                    }
                    if(isset($_GET['enddate'])){
                      $enddate = $_GET['enddate'];
                    }
                    if(isset($_GET['mobile'])){
                      $mobile = $_GET['mobile'];
                    }
                   ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                            <div class="col-md-3">
                                <div class="form-group my-3">
                                    <label for="startdate" class="">Start Date</label>
                                    <input class="form-control form-control-solid datetimepickerget" name='startdate' type="text" value="{{$startdate}}" id="startdate" placeholder="Enter Start Date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group my-3">
                                    <label for="enddate" class="">End Date</label>
                                    <input class="form-control form-control-solid datetimepickerget"  name='enddate' type="text" value="{{$enddate}}" id="enddate" placeholder="Enter End Date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group my-3">
                                {{ Form::label('User Name','User Name',array('class'=>'text-bold'))}}
                                {{ Form::text('playername',$playername,array('value'=>$playername,'placeholder'=>'Search By Name','id'=>'playername','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group my-3">
                                {{ Form::label('Mobile','Mobile',array('class'=>'text-bold'))}}
                                {{ Form::text('mobile',$mobile,array('value'=>$mobile,'placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-12 text-right mt-4 mb-2">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                              <a href="<?php echo action('FundController@netbanking')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo"></i>&nbsp;Reset</a>
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
    <div class="card-header">Net-Banking Report</div>
    <div class="card-body">

        @include('alert_msg')
        
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-striped table-hover text-nowrap" id="netbankingtable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S No.</th>
                        <th>User Id</th>
                        <th>Name</th>
                        <th>Mobile no.</th>
                        <th>Transaction Method</th>
                        <th>Transaction Id</th>
                        <th>Transaction Date</th>
                        <th>Transaction Amount</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S No.</th>
                        <th>User Id</th>
                        <th>Name</th>
                        <th>Mobile no.</th>
                        <th>Transaction Method</th>
                        <th>Transaction Id</th>
                        <th>Transaction Date</th>
                        <th>Transaction Amount</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
    var startdate = $('#startdate').val();
        var enddate = $('#enddate').val();
        var playername = $('#playername').val();
        var mobile = $('#mobile').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#netbankingtable').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/netbankingtable');?>?startdate='+startdate+'&playername='+playername+'&enddate='+enddate+'&mobile='+mobile,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sno" },
                { "data": "id" },
                { "data": "name" },
                { "data": "mobile" },
                { "data": "transaction" },
                { "data": "tid" },
                { "data": "tdate"},
                { "data": "tamt"}
            ]

        });

});
</script>
@endsection
