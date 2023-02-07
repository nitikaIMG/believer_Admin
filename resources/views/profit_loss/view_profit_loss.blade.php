@extends('main')

@section('heading')
    Profit & Loss Manager
@endsection('heading')

@section('sub-heading')
    Profit & Loss Report
@endsection('sub-heading')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('ProfitLossController@view_profit_loss')?>">
                  <?php
                    $name="";$status="";$option="";
                    if(isset($_GET['name'])){
                      $name = $_GET['name'];
                    }
                    $fantasy_type="";
                    if(isset($_GET['fantasy_type'])){
                      $fantasy_type = $_GET['fantasy_type'];
                    }
                  ?>
                  <?php
                      $startdate="";$enddate="";
                      if(isset($_GET['startdate'])){
                        $startdate = $_GET['startdate'];
                      }
                      if(isset($_GET['enddate'])){
                        $enddate = $_GET['enddate'];
                      }
                          if(isset($_GET['option'])){
                            $option = $_GET['option'];
                          }
                    ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">

                            <div class="col-md-3">
                                <div class="form-group my-3">
                                {{ Form::label('Match Name','Match Name',array('class'=>'control-label text-bold'))}}
                                {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Match Name','id'=>'name1','class'=>'form-control','style'=>'color:black;'))}}
                                </div>
                            </div>
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
                            <div class="col-md-3">
                              <div class="form-group my-3">
                                {{ Form::label('Search By Range','Search By Range',array('class'=>'control-label text-bold'))}}
                                <select name="option" class="form-control" id="option">
                                  <option value="">Select Date Range</option>
                                  <option value="1"
                                      @if($option == "1")
                                      selected
                                      @endif
                                  >Today</option>
                                  <option value="2"
                                      @if($option == "2")
                                      selected
                                      @endif
                                  >This Week</option>
                                  <option value="3"
                                      @if($option == "3")
                                      selected
                                      @endif
                                  >This Month</option>
                                </select>
                              </div>
                            </div>

                            <div class="col-12 text-right mt-4 mb-2">
                                <button type="submit" class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp; Submit</button>
                                <a href="<?php echo action('ProfitLossController@view_profit_loss')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
    <div class="card-header">Profit & Loss Report</div>
    <div class="card-body">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">

        @include('alert_msg')

            <table class="table table-bordered table-striped table-hover text-nowrap" id="allmatches_datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>                              
                      <th class="text-capitalize">Sno.</th>
                      <th class="text-capitalize">Match</th>
                      <th class="text-capitalize">Match Date</th>
                      <th class="text-capitalize">Investment Amount</th>
                      <th class="text-capitalize">Refund Amount</th>
                      <th class="text-capitalize">Winning Amount</th>
                      <th class="text-capitalize">Youtuber Bonus</th>
                      <th class="text-capitalize">Profit or Loss</th>
                      <th class="text-capitalize">Amount of P&L</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>                              
                      <th class="text-capitalize">Sno.</th>
                      <th class="text-capitalize">Match</th>
                      <th class="text-capitalize">Match Date</th>
                      <th class="text-capitalize">Investment Amount</th>
                      <th class="text-capitalize">Refund Amount</th>
                      <th class="text-capitalize">Winning Amount</th>
                      <th class="text-capitalize">Youtuber Bonus</th>
                      <th class="text-capitalize">Profit or Loss</th>
                      <th class="text-capitalize">Amount of P&L</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
                
                <tr> 
                  <th>Total</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th>
                      <div style="max-width: fit-content;">Profit - {{number_format($profit, 2, '.', '')}} </div>
                  </th>
                  <th>
                      <div style="max-width: fit-content;">Loss - {{number_format($loss, 2, '.', '')}} </div>
                  </th>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>

    <!-- End container-fluid-->
   <script>
       $(document).datepicker({
           format:"yyyy/mm/dd"
       });
   </script>
   
<script type="text/javascript">
    $(document).ready(function() {  
    $.fn.dataTable.ext.errMode = 'none';
    var name = $('#name1').val();
    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val(); 
        var option = $('#option').val();
        var fantasy_type = $('#fantasy_type').val();   
        $('#allmatches_datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,

            "ajax":{
                     "url": '<?php echo action('ProfitLossController@view_profit_loss_dt');?>?name='+name+'&startdate='+startdate+'&enddate='+enddate+'&option='+option+'&fantasy_type='+fantasy_type,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
                   "dom": 'lBfrtip',
                   "lengthMenu": [[10, 25, 50,100,200,300,500,1000,10000 ], [10, 25, 50,100,200,300,500,1000,10000]],
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
                { "data": "start_date" },
                { "data": "invested_amt" },
                { "data": "refund_amt" },
                { "data": "win_amt" },
                { "data": "youtuber_bonus" },
                { "data": "profit_or_loss" },
                { "data": "amount_profit_or_loss" }
            ]  

        });
        
});
</script>
@endsection
