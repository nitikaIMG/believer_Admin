@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    User Transactions
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('RegisteruserController@viewtransactions',$uid)?>">
                  <?php
                    $start_date="";$end_date="";$cid="";$typ='';
                    if(isset($_GET['start_date'])){
                        $start_date = $_GET['start_date'];
                    }
                    if(isset($_GET['end_date'])){
                        $end_date = $_GET['end_date'];
                    }
                    if(isset($_GET['cid'])){
                        $cid = $_GET['cid'];
                    }
                    if(isset($_GET['type'])){
                        $typ = $_GET['type'];
                    }
                    ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                            <input type="hidden" name="cid" id="cid" value="{{ $cid }}">

                            <div class="col-md-3">
                                <div class="form-group my-3">
                                    <label for="start_date" class="">Start Date</label>
                                    <input class="form-control form-control-solid datetimepickerget" autocomplete="off" name='start_date' type="text" value="{{$start_date}}" id="start_date" placeholder="Enter Start Date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group my-3">
                                    <label for="end_date" class="">End Date</label>
                                    <input class="form-control form-control-solid datetimepickerget"  autocomplete="off" name='end_date' type="text" value="{{$end_date}}" id="end_date" placeholder="Enter End Date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group my-3">
                                    <label for="t_type" class="">Types</label>
                                    <select class="form-control form-control-solid" name="type" id="t_type">
                                        <option value="">Select Type</option>
                                        @forelse($transaction_types as $type)
                                            <option value="{{$type->type}}" <?php echo (($typ==$type->type)?'selected':''); ?>>{{$type->type}}</option>
                                        @empty

                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-auto mt-5">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('RegisteruserController@viewtransactions',$uid)?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>

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
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View User Transactions</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                  <form action="<?php echo action('RegisteruserController@downloadalluserstransaction',$uid);?>" method="get">
                   <input type="hidden" name="start_date"  value="<?php echo $start_date;?>">
                    <input type="hidden" name="end_date"  value="<?php echo $end_date;?>">
                    <button type="submit" class="btn btn-secondary text-uppercase btn-sm rounded-pill" data-toggle="tooltip" title="Download All User Transaction Details" font-weight-600><i class="fas fa-download"></i>&nbsp; Download
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')


        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="viewtransactions_table" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th rowspan="2" data-toggle="tooltip" title="Serial Number">#</th>
              <th rowspan="2" data-toggle="tooltip" title="User ID">U. ID</th>
              <th rowspan="2">Date & Time</th>
              <th rowspan="2" data-toggle="tooltip" title="Amount">Amt</th>
              <th colspan="2" class="text-center text-dark">Transaction</th>
              <th colspan="3" class="text-center text-dark">Bonus</th>
              <th colspan="3" class="text-center text-dark">Winning</th>
              <th colspan="3" class="text-center text-dark">Balance</th>
              <th colspan="3" class="text-center text-dark">Extra Cash</th>
              <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th class="text-center">Type</th>
                <th class="text-center">Reason</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                <th rowspan="2" data-toggle="tooltip" title="Serial Number">#</th>
                <th rowspan="2" data-toggle="tooltip" title="User ID">U. ID</th>
                <th rowspan="2">Date & Time</th>
                <th rowspan="2" data-toggle="tooltip" title="Amount">Amt</th>
                <th class="text-center">Type</th>
                <th class="text-center">Reason</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th class="text-center" data-toggle="tooltip" title="Available">AVL</th>
                <th class="text-center" data-toggle="tooltip" title="Credit">Cr</th>
                <th class="text-center" data-toggle="tooltip" title="Debit">Dr</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
              <th colspan="2" class="text-center text-dark">Transaction</th>
              <th colspan="3" class="text-center text-dark">Bonus</th>
              <th colspan="3" class="text-center text-dark">Winning</th>
              <th colspan="3" class="text-center text-dark">Balance</th>
              <th colspan="3" class="text-center text-dark">Extra Cash</th>
            </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var type = $('#t_type').val();
    var cid = $('#cid').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#viewtransactions_table').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/viewtransactions_table/'. $uid .'/');?>?start_date='+start_date+'&end_date='+end_date+'&type='+type+'&cid='+cid,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "id" },
                { "data": "userid" },
                { "data": "date" },
                { "data": "amt" },
                { "data": "ttype" },
                { "data": "treason"},
                { "data": "bonusA"},
                { "data": "bonusC"},
                { "data": "bonusD"},
                { "data": "winningA"},
                { "data": "winningC"},
                { "data": "winningD"},
                { "data": "balanceA"},
                { "data": "balanceC"},
                { "data": "balanceD"},
                { "data": "extracashA"},
                { "data": "extracashC"},
                { "data": "extracashD"},
                { "data": "total"}
            ]

        });

});
</script>
@endsection('content')
