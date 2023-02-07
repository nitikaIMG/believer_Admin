@extends('main')
@section('heading')
    Profit & Loss Manager
@endsection('heading')

@section('sub-heading')
    Profit & Loss Daily Report
@endsection('sub-heading')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('ProfitLossController@view_daily_report') ?>">
                  <?php
$name = "";
$status = "";
$option = "";
if (isset($_GET['name'])) {
    $name = $_GET['name'];
}
$fantasy_type = "";
if (isset($_GET['fantasy_type'])) {
    $fantasy_type = $_GET['fantasy_type'];
}
?>
                                        <?php
$startdate = "";
$enddate = "";
if (isset($_GET['startdate'])) {
    $startdate = $_GET['startdate'];
}
if (isset($_GET['enddate'])) {
    $enddate = $_GET['enddate'];
}

?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">

                             <div class="col-md-3">
                                <div class="form-group my-3">
                                {{ Form::label('Start Date','Start Date',array('class'=>'control-label text-bold'))}}
                                {{ Form::text('startdate',$startdate,array('value'=>$startdate,'placeholder'=>'Search By Start Date','id'=>'startdate','class'=>'form-control datepicker','style'=>'color:black;'))}}
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group my-3">
                                {{ Form::label('End Date','End Date',array('class'=>'control-label text-bold'))}}
                                {{ Form::text('enddate',$enddate,array('value'=>$enddate,'placeholder'=>'Search By End Date','id'=>'enddate','class'=>'form-control datepicker','style'=>'color:black;'))}}
                                </div>
                            </div>

                            <div class="col-12 text-right mt-4 mb-2">
                                <button type="submit" class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp; Submit</button>
                                <a href="<?php echo action('ProfitLossController@view_daily_report') ?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
    <div class="card-header">Profit & Loss Day Wise Report</div>
    <div class="card-body">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">

        @include('alert_msg')

            <table class="table table-bordered table-striped table-hover text-nowrap" id="allmatches_datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                      <th>Sno.</th>
                      <th style="width: 60px;">Date</th>
                      <th style="width: 100px;">Net Amount</th>
                      <th>Received Amount</th>
                      <th>Withdraw Amount</th>
                      <th>2% on Received Amount</th>
                      <th>2% on Withdraw Amount</th>
                      <th>Profit</th>
                      <th>Loss</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                      <th>Sno.</th>
                      <th style="width: 60px;">Date</th>
                      <th style="width: 100px;">Net Amount</th>
                      <th>Received Amount</th>
                      <th>Withdraw Amount</th>
                      <th>2% on Received Amount</th>
                      <th>2% on Withdraw Amount</th>
                      <th>Profit</th>
                      <th>Loss</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
                <!--
                <tr>
                  <th>Total</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr> -->
            </table>
        </div>
    </div>
</div>
    <!-- End container-fluid-->

<script type="text/javascript">
    $(document).ready(function() {

    $.fn.dataTable.ext.errMode = 'none';
    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val();
        $('#allmatches_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/view_daily_report_dt'); ?>?startdate='+startdate+'&enddate='+enddate,
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
                { "data": "report_date" },
                { "data": "net_amount" },
                { "data": "total_received" },
                { "data": "total_withdraw" },
                { "data": "cashfreeRper" },
                { "data": "cashfreeWper" },
                { "data": "profit" },
                { "data": "loss" }
            ]

        });

});
</script>

@endsection