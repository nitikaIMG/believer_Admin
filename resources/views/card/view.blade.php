@extends('main')

@section('heading')
   Card Team Manager
@endsection('heading')

@section('sub-heading')
    View All Teams
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('CardController@view_team')?>">
                  <?php
                  $name="";$fantasy_type="";
                  if(isset($_GET['name'])){
                    $name = $_GET['name'];
                  }
                  if(isset($_GET['fantasy_type'])){
                    $fantasy_type = $_GET['fantasy_type'];
                  }
                 ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-center">
                        
                            <div class="col-md col-12">
                                <div class="form-group my-3">
                                    <label for="name" class="">Team Name</label>
                                    {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Team Name','id'=>'name','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-auto col-12 mt-md-4 pt-md-1 text-right">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="{{action('CardController@view_team')}}" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Teams</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                {{-- <input type="hidden" id="fantasy_type"  value="<?php echo $fantasy_type;?>">
                  <form action="<?php //echo action('CardController@downloadteamdata');?>" method="get">
                    <input type="hidden" name="name"  value="<?php echo $name;?>">
                    <input type="hidden" name="fantasy_type"  value="<?php echo $fantasy_type;?>">
                    <button type="submit" class="btn btn-secondary text-uppercase btn-sm rounded-pill" data-toggle="tooltip" title="Download All Team Details" font-weight-600><i class="fad fa-download"></i>&nbsp; Download</button>
                </form> --}}
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('alert_msg')

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-striped table-hover text-center text-nowrap" id="view_team_datatable" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th class="myclass">Sno.</th>
              <th class="myclass1">Team Name</th>
              <th class="myclass2">Team Short Name</th>
              <th class="myclass3">Team logo</th>
              <th class="myclass4">Action</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
                  <th>Sno.</th>
                  <th>Team Name</th>
                  <th>Team Short Name</th>
                  <th>Team logo</th>
                  <th>Action</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
    var name = $('#name').val();
    var fantasy_type = $('#fantasy_type').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#view_team_datatable').DataTable({
            'responsive':false,
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/view_cardteam_datatable');?>?name='+name+'&fantasy_type='+fantasy_type,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "id" },
                { "data": "team" },
                { "data": "short_name" },
                { "data": "logo" },
                { "data": "action" }
            ]

        });

});

</script>
@endsection('content')
