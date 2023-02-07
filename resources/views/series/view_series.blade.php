@extends('main')

@section('heading')
    Series Manager
@endsection('heading')

@section('sub-heading')
    View All Series
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('MatchController@importseriesdata')?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fa fa-download"></i>&nbsp; Import New Series</a>
<a  href="<?php echo action('SeriesController@create') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fa fa-plus"></i>&nbsp; Add New Series</a>
@endsection('card-heading-btn')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
      
        @include('alert_msg')

        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('SeriesController@index')?>">
                  <?php
                  $name="";$start_date="";$end_date="";$fantasy_type="";
                  if(isset($_GET['name'])){
                    $name = $_GET['name'];
                  }
                  if(isset($_GET['start_date'])){
                    $start_date = $_GET['start_date'];
                  }
                  if(isset($_GET['end_date'])){
                    $end_date = $_GET['end_date'];
                  }
                  ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                        
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('Series Name','Series Name',array('class'=>'text-bold'))}}
                                {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Name','id'=>'name1','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <label for="start_date" class="">Start Date</label>
                                    <input class="form-control form-control-solid datetimepickerget" name='start_date' type="text" value="{{$start_date}}" id="start_date" placeholder="Search by start date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <label for="end_date" class="">End Date</label>
                                    <input class="form-control form-control-solid datetimepickerget"  name='end_date' type="text" value="{{$end_date}}" id="end_date" placeholder="Search by end date">
                                </div>
                            </div>

                            <div class="col-12 text-right mt-4 mb-2">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('SeriesController@index')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
    <div class="card-header">View All Series</div>
    <div class="card-body">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-striped table-hover text-nowrap" id="series_datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        <th>Start date</th>
                        <th>End date</th>
                        <th>Series Status</th>
                        <!-- <th>Has LeaderBoard</th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        <th>Start date</th>
                        <th>End date</th>
                        <th>Series Status</th>
                        <!-- <th>Has LeaderBoard</th> -->
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    var name = $('#name1').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    // var fantasy_type = $('#fantasy_type').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#series_datatable').DataTable({
            // 'responsive': true,
            "bFilter" : false,
      "processing": true,
          "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/series_datatable');?>?name='+name+'&start_date='+start_date+'&end_date='+end_date,
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
                { "data": "id" },
                { "data": "name" },
                { "data": "start_date" },
                { "data": "end_date" },
                { "data": "status" },
                // { "data": "has_leaderboard" },
                { "data": "action" },
            ]

        });
        
    
        // // Handle click on "Expand All" button
        // $('#series_datatable').on('click', function(){
        //     // Expand row details
        //     table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
        // });

});
</script>

@endsection('content')
