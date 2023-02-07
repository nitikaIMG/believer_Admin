@extends('main')

@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
   Bank Verify
@endsection('sub-heading')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Bank Verify</div>
      <form id="formId" class="card-body" action="<?php echo action('VerificationController@updatebanktatus'); ?>" enctype='multipart/form-data' method="post">
        {{csrf_field()}}
         
        <input name="status" type="hidden" value="" id="panstatus">
        <input name="id" type="hidden" value="<?php echo $pancarddetails->id?>" id="panstatus">

        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover" id="datatable" width="100%" cellspacing="0">
                <thead>
                      <tr>
                        <th colspan="4">Bank approval Page</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>User ID</td>
                        <td><?php echo $pancarddetails->userid?></td>
                        <td></td>
                        <td><a class="btn btn-info" href="<?php echo action('VerificationController@editbankdetails',$pancarddetails->id)?>" style="cursor:pointer"><i class="fa fa-edit"></i> Edit Bank details</a></td>
                      </tr>
                      <tr>
                        <td>username</td>
                        <td class="text-uppercase"><?php echo $pancarddetails->username ;?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><?php echo $pancarddetails->email;?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Account No.</td>
                        <td><?php echo $pancarddetails->accno?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>IFSC Code</td>
                        <td class="text-uppercase"><?php echo $pancarddetails->ifsc?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Bank Name</td>
                        <td class="text-uppercase"><?php echo $pancarddetails->bankname?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Bank Branch</td>
                        <td class="text-uppercase;"><?php echo $pancarddetails->bankbranch?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>State</td>
                        <td class="text-uppercase"><?php echo $pancarddetails->state?></td>
                        <td></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>Image</td>
                        <td>
                          <?php
                          $ext = pathinfo($pancarddetails->image, PATHINFO_EXTENSION);
                          $img= asset('public/uploads/bank/'.$pancarddetails->image);
                            if($ext=='pdf'){
                              ?>
                              <i class="fa fa-file-pdf-o" style="color:red;font-size:30px;"></i>
                              <?php
                            }else{
                              ?>
                              <img src="<?php echo $img;?>" style="max-width:100px;" onerror="this.src='{{ asset( auth()->user()->settings()->logo ?? '')}}'">
                              <?php
                             }
                          ?>

                        </td>
                        <td><a href="<?php echo $img;?>" target="_blank" class="btn btn-info"> View Image </a></td>
                        <td></td>
                      </tr>
                       <tr>
                            <td class="commentbox" colspan="3" rowspan="3">
                              <textarea type="text" rows="3" class="form-control form-control-solid float-left mr-1" required="" name="comment" placeholder="Comment" id="commentpan" style="width:100%;"></textarea>
                            </td>
                            <td>
                            <?php
                              if($pancarddetails->status==1){
                                ?>
                              <a  style="color:green"> Request Approved</a>
                              <?php } else{
                                if($pancarddetails->bank_verify!=1){
                                ?>
                                <button type="button"  onclick="clickpan('1');" class="btn btn-info" style="curor:pointer">Approve Request</button>
                                <?php
                                }else{ ?>
                                  <a  onclick="clickpan('1');" class="btn btn-info" style="curor:pointer">Request Approved</a>
                              <?php }
                              }?>
                              <?php
                              if($pancarddetails->bank_verify==2){
                                ?>
                                <td><a style="color:red;"> Request Rejected </a></td>
                                <?php
                              }
                              else{
                            ?>
                            <a  onclick="clickpan('2');" class="btn btn-danger text-white"> Reject Request </a>
                            <?php } ?>
                            </td>
                          </tr>

                    </tbody>
            </table>
        </div>
     </form>
</div>

<script>
function clickpan(value){
if(value==2){
var getcomment = $("#commentpan").val();
if(getcomment==""){
Swal.fire('Please Enter comment first.');
return false;
}
$("#panstatus").val(value);
$("#formId").submit();
}
if(value==3){
Swal.fire('Please verify pan request of this user first.');
return false;
}else if(value==1){


$("#panstatus").val(value);
$("#formId").submit();
}
}
</script>

@endsection('content')
