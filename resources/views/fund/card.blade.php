@extends('main')

@section('heading')
    Fund Manager
@endsection('heading')

@section('sub-heading')
    View Received Fund
@endsection('sub-heading')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('FundController@card')?>">
                  <?php
                    $startdate="";$playername="";$enddate="";$mobile="";$option="";
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
                     if(isset($_GET['option'])){
                       $option = $_GET['option'];
                    }$payment_method="";
                    if(isset($_GET['payment_method'])){
                      $payment_method = $_GET['payment_method'];
                    }
                   ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    <label for="startdate" class="">Start Date</label>
                                    <input class="form-control form-control-solid datetimepickerget" name='startdate' type="text" value="{{$startdate}}" id="startdate" placeholder="Enter Start Date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                    <label for="enddate" class="">End Date</label>
                                    <input class="form-control form-control-solid datetimepickerget"  name='enddate' type="text" value="{{$enddate}}" id="enddate" placeholder="Enter End Date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('User Name','User Name',array('class'=>'text-bold'))}}
                                {{ Form::text('playername',$playername,array('value'=>$playername,'placeholder'=>'Search By Name','id'=>'playername','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('Mobile','Mobile',array('class'=>'text-bold'))}}
                                 <input value="{{$mobile}}" placeholder="Search By Mobile" id="mobile" class="form-control form-control-solid" maxlength="10" pattern="[1-9]{1}[0-9]{9}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" name="mobile" type="text">
                                </div>
                            </div>                            
                             <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('Payment Method','Payment Method',array('class'=>'text-bold'))}}
                                {{ Form::text('payment_method',$payment_method,array('value'=>$payment_method,'placeholder'=>'Search By payment method','id'=>'payment_method','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('Search By Range','Search By Range',array('class'=>'text-bold'))}}
                              <select name="option" class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Range" data-hide-disabled="true" id="option">
                               <option value="">Select range</option>
                               <option value="1"
                                  @if($option == "1")
                                  selected
                                  @endif
                               >0-500</option>
                               <option value="2"
                                  @if($option == "2")
                                  selected
                                  @endif
                               >501-1000</option>
                               <option value="3"
                                  @if($option == "3")
                                  selected
                                  @endif
                               >1001-2000</option>
                               <option value="4"
                                  @if($option == "4")
                                  selected
                                  @endif
                               >2001-5000</option>
                               <option value="5"
                                  @if($option == "5")
                                  selected
                                  @endif
                               >more than 5000</option>
                           </select>
                                </div>
                            </div>
                            <div class="col-auto text-right ml-auto mt-3 d-flex align-items-end justify-content-end">
                              <button class="btn btn-sm btn-success text-uppercase mr-2"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                              <a href="<?php echo action('FundController@card')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo"></i>&nbsp;Reset</a>
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
    <div class="card-header">Received Fund Reports</div>
    <div class="card-body">

        @include('alert_msg')
        
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-striped table-hover text-nowrap" id="cardtable" width="100%" cellspacing="0">
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
        var option = $('#option').val();
        var payment_method = $('#payment_method').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#cardtable').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/cardtable');?>?startdate='+startdate+'&playername='+playername+'&enddate='+enddate+'&mobile='+mobile+'&option='+option+'&payment_method='+payment_method,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
                   "dom": 'lBfrtip',
                   "lengthMenu": [[10, 25, 50,100,1000,10000 ], [10, 25, 50,100,1000,10000]],
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
