@extends('main')
<?php
use App\Helpers\Helpers;
?>
@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    User Wallet
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('RegisteruserController@userswallet')?>">
        <?php
          $userid="";$name="";$email="";$mobile="";$team="";
          if(isset($_GET['email'])){
            $email = $_GET['email'];
          }
          if(isset($_GET['name'])){
            $name = $_GET['name'];
          }
          if(isset($_GET['userid'])){
            $userid = $_GET['userid'];
          }
          if(isset($_GET['mobile'])){
            $mobile = $_GET['mobile'];
          }

          if(isset($_GET['team'])){
            $team = $_GET['team'];
          }
         ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0 align-items-end">

                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('User id','User id',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('userid',$userid,array('value'=>$userid,'placeholder'=>'Search By Userid','id'=>'userid','class'=>'form-control form-control-solid', 'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Username','Username',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Name','id'=>'name','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Email','Email',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('email',$email,array('value'=>$email,'placeholder'=>'Search By Email','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Mobile','Mobile',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('mobile',$mobile,array('value'=>$mobile,'placeholder'=>'Search By Mobile','id'=>'mobile','class'=>'form-control form-control-solid', 'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');", 'maxlength' => "10", 'pattern' => "[1-9]{1}[0-9]{9}"))}}
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group my-3">
                                    {{ Form::label('Team name','Team name',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('team',$team,array('value'=>$team,'placeholder'=>'Search By Team Name','id'=>'team','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-auto text-right mb-md-3">
                              <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('RegisteruserController@userswallet')?>" class="btn text-uppercase rounded-pill btn-sm btn-warning"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
    <div class="card-header">View Users Wallet</div>
    <div class="card-body">

                @include('alert_msg')


        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="userswallet_table" width="100%" cellspacing="0">
                <thead>
            <tr>
              <th data-toggle="tooltip" title="Serial Number">#</th>
              <th data-toggle="tooltip" title="User ID">ID</th>
              <th data-toggle="tooltip" title="User Name">U. Name</th>
              <th>Team</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th data-toggle="tooltip" title="Current Date">C. Date</th>
              <th>Unutilized</th>
              <th>Winning</th>
              <th data-toggle="tooltip" title="Cash Bonus">C. Bonus</th>
              <th data-toggle="tooltip" title="Extra Cash">Extra Cash</th>
              <th data-toggle="tooltip" title="Total Bonus">T. Bonus</th>
            </tr>
            </thead>
             <tbody>
          </tbody>
          <tfoot>
              <tr>
            <th data-toggle="tooltip" title="Serial Number">#</th>
              <th data-toggle="tooltip" title="User ID">ID</th>
              <th data-toggle="tooltip" title="User Name">U. Name</th>
              <th>Team</th>
              <th>Email</th>
              <th>Mobile No.</th>
              <th data-toggle="tooltip" title="Current Date">C. Date</th>
              <th>Unutilized</th>
              <th>Winning</th>
              <th data-toggle="tooltip" title="Cash Bonus">C. Bonus</th>
              <th data-toggle="tooltip" title="Extra Cash">Extra Cash</th>
              <th data-toggle="tooltip" title="Total Bonus">T. Bonus</th>
              </tr>
              <tr>
                  <th class="font-weight-900 text-dark">Total</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="font-weight-900 text-dark">{{$bal_sum}}</th>
                    <th class="font-weight-900 text-dark">{{$win_sum}}</th>
                    <th class="font-weight-900 text-dark">{{$bonus_sum}}</th>
                    <th class="font-weight-900 text-dark">{{$extracash_sum}}</th>
                    <th class="font-weight-900 text-dark">{{$total}}</th>
              </tr>
          </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
    var userid = $('#userid').val();
    var name = $('#name').val();
    var email = $('#email').val();
    var mobile = $('#mobile').val();
    var team = $('#team').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#userswallet_table').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/userswallet_table');?>?userid='+userid+'&name='+name+'&email='+email+'&mobile='+mobile+'&team='+team,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "id" },
                { "data": "userid" },
                { "data": "username" },
                { "data": "team" },
                { "data": "email" },
                { "data": "mobile" },
                { "data": "date" },
                { "data": "balance"},
                { "data": "winning"},
                { "data": "bonus"},
                { "data": "extracash"},
                { "data": "total"}
            ]

        });
});
</script>
@endsection('content')
