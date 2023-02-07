@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    Admin Wallet Details
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('AdminwalletController@giveadminwallet') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fa fa-plus"></i>&nbsp; Add New</a>
@endsection('card-heading-btn')

@section('content')

<div class="card mb-4">
    <div class="card-header">Adminwallet Details</div>
    <div class="card-body">

        
        @include('alert_msg')
        
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="adminwallet" width="100%" cellspacing="0">
                <thead>
            <tr>
              <th>Sno.</th>
                <th>User Name</th>
                <th>Mobile No.</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Bonus Type</th>
                <th>Date</th>
                <th>Description</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
            <th>Sno.</th>
            <th>User Name</th>
            <th>Mobile No.</th>
            <th>Email</th>
            <th>Amount</th>
            <th>Bonus Type</th>
            <th>Date</th>
            <th>Description</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
    var name = $('#name1').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#adminwallet').DataTable({
            bFilter: false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/adminwallet-list');?>',
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
                { "data": "id"},
                { "data": "username" },
                { "data": "mobile" },
                { "data": "email" },
                { "data": "amount" },
                { "data": "bonus_type" },
                { "data": "created_at" },
                { "data": "description" }
            ]

        });

});
</script>
@endsection('content')
