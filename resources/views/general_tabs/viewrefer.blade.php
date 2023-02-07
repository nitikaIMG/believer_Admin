@extends('main')
@section('content')
<?php
    use App\Helpers\Helpers;
?>
<div class="page-content">
    <div class="container-fluid">

    <!-- Breadcrumb-->
     <div class="row pt-2 pb-2" id="page-head" id="page-head">
        <div class="col-sm-12 my-3">
		    <h4 class="page-title">Add Special Refer Bonus</h4>
		    <ol class="breadcrumb">
		    <li class="breadcrumb-item"><a href="{{ action('DashboardController@index') }}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="javaScript:void();">Special Refer Bonus</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Special Refer Bonus</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->


    <div class="row">
      <div class="col-lg-12">
        <div class="panel col-md-12">
           <div class="panel-body">
           <div class="dt-responsive table-responsive">

        @include('alert_msg')

            
           <form id="demo-form2" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" action="<?php echo action('GeneralTabsController@viewrefer')?>" method="post" enctype="multipart/form-data">
    		{{csrf_field()}}
                <div class="form-group">
    				<label class="control-label">Refer Bonus</label>
                    <input name="code" class="form-control form-control-solid" type="text" min="0" required="" placeholder=" Enter Code Like BT116788">
                </div>
                <div class="form-group">
                <label class="control-label" for="first-name">Amount<span class="required">*</span></label>
                <input name="bonus" class="form-control form-control-solid" type="number" min="0" required="" placeholder="Enter amount">
            </div>
                <div class="form-group">
                <label class="control-label" for="first-name">Start Date<span class="required">*</span></label>
                <input name="start_date" class="form-control form-control-solid datetimepickerget" required="" type="text" min="0" placeholder="Enter start date">
            </div>
                <div class="form-group">
                <label class="control-label" for="first-name">Expired Date<span class="required">*</span></label>
                <input name="expire_date" class="form-control form-control-solid datetimepickerget" required="" type="text" min="0" placeholder="Enter expiry date">
            </div>
            <div class="form-group">
                <div class="col-md-6" col-sm-6 col-xs-12 col-md-offset-3">
                  <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i>Submit</button>
                </div>
            </div>
            </form>
         </div>
         </div>
      </div>
    </div>
    <div class="row" style="width: 100%;margin-left: 4px">
      <div class="col-md-12">
        <div class="panel col-md-12">
            <div class="panel-header">
                <h5>All Tabs</h5>
            </div>
           <div class="panel-body">

            <div class="dt-responsive table-responsive">
            <table class="table table-striped table-bordered dataTable no-footer text-center" >
                <thead>
                <th>Sno</th>
                <th>Code</th>
                <th>Amount</th>
                <th>Start Date</th>
                <th>Expire Date</th>
                <th>Action</th>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    if(!empty($dataa)){

                        $onclick = "delete_sweet_alert('".action('GeneralTabsController@deleterefer',$value->id)."', 'Are you sure?')";

                    foreach($dataa as $value){ ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>{{$value->code}}</td>
                        <td>{{$value->bonus}}</td>
                        <td>{{$value->start_date}}</td>
                        <td>{{$value->expire_date}}</td>
                        <td> <a onclick="<?php echo $onclick ?>" class="btn-danger btn">Delete</a></td>
                    </tr>
                </tbody>
            <?php $i++; }
        } ?>
            </table>
            </div>
        </div>
        </div>
      </div>
      </div>
        </div>
      {{Form::close()}}
      </div>
         </div>
      </div>
    </div>
    </div>
    <!-- End container-fluid-->

   </div>
@endsection
