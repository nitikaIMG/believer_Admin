@extends('main')

@section('heading')
    Card Series Manager
@endsection('heading')

@section('sub-heading')
    View All Card Series
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('CardController@importcarddatafromapi')?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fa fa-download"></i>&nbsp; Import Data</a>
@endsection('card-heading-btn')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
      
        @include('alert_msg')

        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('CardController@index')?>">
                  <?php
                  $name="";
                  if(isset($_GET['name'])){
                    $name = $_GET['name'];
                  }
                  ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">
                        
                            <div class="col-md-6">
                                <div class="form-group my-3">
                                {{ Form::label('Series Name','Series Name',array('class'=>'text-bold'))}}
                                {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Name','id'=>'name1','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>

                            <div class="col-6" style="margin-top: 50px;">
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
                        <th>Series Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        <th>Series Status</th>
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
    $.fn.dataTable.ext.errMode = 'none';
        $('#series_datatable').DataTable({
            // 'responsive': true,
            "bFilter" : false,
      "processing": true,
          "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/series_carddatatable');?>?name='+name,
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
                { "data": "status" },
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
