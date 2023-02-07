@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    View All Rewarded Users
@endsection('sub-heading')

@section('card-heading-btn')
<!-- <a  href="<?php //echo action('YoutuberController@add_youtuber') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right" data-toggle="tooltip" title="Add Youtuber"><i class="fas fa-plus"></i>&nbsp; Add</a> -->
@endsection('card-heading-btn')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('UserBonusController@view_rewarded_users')?>">
                  <?php
                  $name="";$email="";$mobile="";
                  if(isset($_GET['name'])){
                    $name = $_GET['name'];
                  }
                  if(isset($_GET['email'])){
                    $email = $_GET['email'];
                  }
                  if(isset($_GET['mobile'])){
                    $mobile = $_GET['mobile'];
                  }
                 ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">

                            <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('Youtuber Name','Youtuber Name',array('class'=>'text-bold'))}}
                                {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Youtuber','id'=>'name1','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group my-3">
                                {{ Form::label('Email','Email',array('class'=>'text-bold'))}}
                                {{ Form::text('email',$email,array('value'=>$email,'placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group my-3">
                              {{ Form::label('Mobile','Mobile',array('class'=>'text-bold'))}}
                              <input name="mobile" class="form-control form-control-solid" type="text"
                                placeholder="Search By Mobile" id="mobile"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                maxlength="10" pattern="[1-9]{1}[0-9]{9}" value="{{$mobile}}">
                                </div>
                            </div>

                            <div class="col-12 text-right mt-4 mb-2">
                                <button type="submit" class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp; Submit</button>
                                <a href="<?php echo action('UserBonusController@view_rewarded_users')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
    <div class="card-header">View All Youtubers</div>
    <div class="card-body">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">

        @include('alert_msg')

            <table class="table table-bordered table-striped table-hover text-nowrap" id="allmatches_datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                      <th class="text-capitalize">S No.</th>
                      <th class="text-capitalize">name</th>
                      <th class="text-capitalize">email</th>
                      <th class="text-capitalize">mobile</th>
                      <th class="text-capitalize">password</th>
                      <th class="text-capitalize">Refer Code</th>
                      <th class="text-capitalize">Total Earned</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                      <th class="text-capitalize">S No.</th>
                      <th class="text-capitalize">name</th>
                      <th class="text-capitalize">email</th>
                      <th class="text-capitalize">mobile</th>
                      <th class="text-capitalize">password</th>
                      <th class="text-capitalize">Refer Code</th>
                      <th class="text-capitalize">Total Earned</th>
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
        $.fn.dataTable.ext.errMode = 'none';

        var name = $('#name1').val();
        var email = $('#email').val();
        var mobile = $('#mobile').val();
            $('#allmatches_datatable').DataTable({
            'bFilter':false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax":{
                         "url": '<?php echo URL::asset('my-admin/view_rewarded_users_dt');?>?name='+name+'&email='+email+'&mobile='+mobile,
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
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "mobile" },
                    { "data": "password" },
                    { "data": "refer_code" },
                    { "data": "total_earned",orderable: true },
                ]
            });

    });
    </script>
@endsection
