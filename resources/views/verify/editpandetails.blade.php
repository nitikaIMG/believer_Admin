@extends('main')

@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
    Edit Pan card details
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('VerificationController@verifypan') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase"><i class="fad fa-eye"></i> View all Pan card requests</a>
@endsection('card-heading-btn')

@section('content')

    @include('alert_msg')

<div class="card">
    <div class="card-header">Edit Pan card details</div>
      <form class="card-body" action="<?php echo action('VerificationController@updatepantatus')?>" enctype='multipart/form-data' method="post" id="firstform">
        {{csrf_field()}}
        <input type="hidden" name="id" value="<?php echo $editpandetails->id;?>">

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label(' Name on PAN Card*',' Name on PAN Card*',array('class'=>'text-bold'))}}
                            {{ Form::text('pan_name',$editpandetails->pan_name,array('value'=>'','required'=>'','placeholder'=>'Enter PAN Card Name here','id'=>'pancardname','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label(' Number on PAN Card*',' Number on PAN Card*',array('class'=>'text-bold'))}}
                            {{ Form::text('pan_number',$editpandetails->pan_number,array('value'=>'','required'=>'','placeholder'=>'Enter PAN Card Number here','id'=>'pancardnumber','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label('PAN Card DOB*','PAN Card DOB*',array('class'=>'text-bold'))}}
                            {{ Form::text('pan_dob',$editpandetails->pan_dob,array('value'=>'','required'=>'','placeholder'=>'Enter Date Of Birth here','id'=>'pancarddob','class'=>'form-control form-control-solid datepicker'))}}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Approval status*','Approval status*',array('class'=>'text-bold'))}}
                            <select class="form-control form-control-solid p-1" id="panstatus" required="" name="status">
                                <option value=""> Select status </option>
                                <option value="1" <?php if($editpandetails->status==1){ echo 'selected'; }?>> Approved </option>
                                <option value="2" <?php if($editpandetails->status==2){ echo 'selected'; }?>> Reject </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Description*','Description*',array('class'=>'text-bold'))}}
                            <textarea type="text" rows="3" class="form-control form-control-solid float-left mr-1" required="" name="comment" placeholder="Comment" id="commentpan" style="width:100%;"><?php echo  $editpandetails->comment;?></textarea>
                        </div>
                    </div>



                    <div class="col-12 text-right mt-4 mb-2">
	                    <button type="button" class="btn-sm btn btn-primary mt-1" id="onclickbtn" onclick="pandetailsupdate()"><i class="icon-lock mr-1"></i>Update PAN Card Details</button>
	                </div>
                </div>
            </div>
        </div>
     </form>
</div>

<script type="text/javascript">
function pandetailsupdate(){
  var pancardname = $("#pancardname").val();
  var pancardnumber = $("#pancardnumber").val();
  var pancarddob = $("#pancarddob").val();
  if(pancardname!="" && pancardnumber!="" && pancarddob!=""){
    var getstatus = $("#panstatus").val();
    if(getstatus!=""){
      if(getstatus==2){
        var getcomment = $("#commentpan").val();
        if(getcomment==""){
          Swal.fire('Please Enter comment first.');
          return false;
        }
      }
      document.getElementById("firstform").submit();
      document.getElementById("formId").submit();
    }
  }
  else{
    Swal.fire('Please fill out all the fields');
  }
}

</script>
@endsection('content')
