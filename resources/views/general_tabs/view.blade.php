@extends('main')

@section('heading')
    General Tabs
@endsection('heading')

@section('sub-heading')
    Add General Tabs Features
@endsection('sub-heading')
@section('content')

<?php
    use App\Helpers\Helpers;
?>


<div class="card mb-4">
    <div class="card-header">Add General Tabs</div>
      <form id="demo-form2" data-parsley-validate="" novalidate="" action="<?php echo action('GeneralTabsController@index')?>" method="post" class="card-body" enctype="multipart/form-data" autocomplete="">
       {{csrf_field()}}


        @include('alert_msg')


        <div class="sbp-preview">
            <div class="sbp-preview-content py-2">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="title">Select Type*</label>
                            <select name="type" class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Type" data-hide-disabled="true" required="">
                              <option value="">Select Type</option>
                              <option value="refer_bonus">Refer Bonus</option>
                              <option value="pan_bonus">Pan Bonus</option>
                              <option value="email_bonus">Email Bonus</option>
                              <option value="mobile_bonus">Mobile Bonus</option>
                              <option value="bank_bonus">Bank Bonus</option>
                              <option value="signup_bonus">Signup Bonus</option>
                              <option value="refer_winning_bonus">Refer Winning Bonus In %</option>
                          </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <label for="description">Please Enter Amount*</label>
                            <input name="amount" class="form-control form-control-solid" type="number" min="0" placeholder="Please Enter Amount" required="">
                        </div>
                    </div>

                    <div class="col-12 text-right mb-2">
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="card mb-4 mt-4">
    <div class="card-header">View General Tabs</div>
    <div class="card-body">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover general-view text-nowrap" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S no.</th>
                        <th>Type</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                
                <tbody>
              <?php
                    $i = 1;
                    if(!empty($dataa)){
                    foreach($dataa as $value){ ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>{{$value->type}}</td>
                        <td class="text-center">{{$value->amount}}</td>
                        <td class="text-center">
                        <?php
                            $onclick = "delete_sweet_alert('".action('GeneralTabsController@delete',$value->id)."', 'Are you sure you want to delete this data?')";
                        ?>
                           <a onclick="<?php echo $onclick; ?>" class="btn btn-sm btn-danger w-35px h-35px text-uppercase" data-toggle="tooltip" title="Delete"><i class ='far fa-trash-alt'></i></a>
                           </td>
                    </tr>
        
              <?php $i++; }
              } ?>
                  </tbody>
              <tfoot>
                    <tr>
                        <th>S no.</th>
                        <th>Type</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
$(function() {
    $('.noAutoComplete').attr('autocomplete', 'off');
});
</script>
@endsection
