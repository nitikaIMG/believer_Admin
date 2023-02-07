@extends('main')

@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
    Pan verify
@endsection('sub-heading')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Pan Full Details</div>
      <form id="formId" class="card-body" action="<?php echo action('VerificationController@updatepantatus')?>" enctype='multipart/form-data' method="post">
        {{csrf_field()}}


        @if ($errors->any())
           <div class="alert alert-danger">
               <ul>
                   @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                   @endforeach
               </ul>
           </div>
        @endif

        <input name="status" type="hidden" value="" id="panstatus">
        <input name="id" type="hidden" value="<?php echo $pancarddetails->id?>">

        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover" id="datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                    <th colspan="4">PAN Card Approval Request</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>User ID</td>
                    <td><?php echo $pancarddetails->userid?></td>
                    <td></td>
                    <td><a class="btn btn-info" href="<?php echo action('VerificationController@editpandetails',$pancarddetails->id)?>" style="cursor:pointer"><i class="fa fa-edit"></i> Edit Pancard Details</a></td>
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
                    <td>Pan Name</td>
                    <td class="text-uppercase"><?php echo $pancarddetails->pan_name?></td>
                    <td></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>DOB</td>
                    <td><?php echo $pancarddetails->pan_dob?></td>
                    <td></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>PAN No.</td>
                    <td class="text-uppercase"><?php echo $pancarddetails->pan_number?></td>
                    <td></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>Image</td>
                    <td>
                        <?php
                        $ext = pathinfo($pancarddetails->image, PATHINFO_EXTENSION);
                        $img= asset('public/uploads/pancard/'.$pancarddetails->image);
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
                    <?php if($pancarddetails->status==1){
                ?>
                <a  style="color:green"> Request Approved</a>
                <?php } else{
                ?>
                <a  onclick="clickpan('1');" class="btn btn-info" style="cursor:pointer"> Approve Request </a>
                <?php
                }?>
                    <?php
                        if($pancarddetails->status==2){
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
Swal.fire('Please select comment first.');
return false;
}
}
$("#panstatus").val(value);
$("#formId").submit();
}
</script>
@endsection('content')
