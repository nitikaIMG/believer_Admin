@extends('main')

@section('heading')
    Verification Manager
@endsection('heading')

@section('sub-heading')
    Edit bank details
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('VerificationController@verifybankaccount') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase mr-2 text-primary float-right"><i class="fad fa-eye"></i> View all Bank requests</a>
@endsection('card-heading-btn')

@section('content')

@include('alert_msg')

<div class="card">
    <div class="card-header">Edit bank details</div>
      <form class="card-body" action="<?php echo action('VerificationController@updatebanktatus')?>" enctype='multipart/form-data' method="post" id="firstform">
        {{csrf_field()}}
        <input type="hidden" name="id" value="<?php echo $editpandetails->id;?>">

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label('Account Name*',' Account Name*',array('class'=>'label-control text-bold'))}}
                            {{ Form::text('accountholder',$editpandetails->accountholder,array('value'=>'','required'=>'','placeholder'=>'Enter Account Holder Name  Here','id'=>'accountholder','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label('Account number*',' Account number*',array('class'=>'label-control text-bold'))}}
                            {{ Form::text('accno',$editpandetails->accno,array('value'=>'','required'=>'','placeholder'=>'Enter Account Number Here','id'=>'accno','class'=>'form-control form-control-solid','onkeypress'=>'return isNumberKey(event)'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label(' IFSC Code*',' IFSC Code*',array('class'=>'label-control text-bold'))}}
                            {{ Form::text('ifsc',$editpandetails->ifsc,array('value'=>'','required'=>'','placeholder'=>'Enter IFSC Code Here','id'=>'ifsc','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label('Bank Name*','Bank Name*',array('class'=>'label-control text-bold'))}}
                            {{ Form::text('bankname',$editpandetails->bankname,array('value'=>'','required'=>'','placeholder'=>'Enter Bank Name Here','id'=>'bankname','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label('Bank Branch*','Bank Branch*',array('class'=>'label-control text-bold'))}}
                            {{ Form::text('bankbranch',$editpandetails->bankbranch,array('value'=>'','required'=>'','placeholder'=>'Enter Bank Branch Here','id'=>'bankbranch','class'=>'form-control form-control-solid'))}}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group my-3">
                            {{ Form::label('State*','State*',array('class'=>'label-control text-bold'))}}
                                <select class="p-1 form-control form-control-solid col-xsm-3" name="state" id="state" placeholder="-- Select Status --">
                                    <option value="">Select State</option>
                                    <option value="Andaman and Nicobar Islands"<?php if($editpandetails->state=='Andaman and Nicobar Islands'){ echo 'selected'; }?>>Andaman and Nicobar Islands</option>
                                    <option value="Andhra Pradesh"<?php if($editpandetails->state=='Andhra Pradesh'){ echo 'selected'; }?>>Andhra Pradesh</option>
                                    <option value="Arunachal Pradesh"<?php if($editpandetails->state=='Arunachal Pradesh'){ echo 'selected'; }?>>Arunachal Pradesh</option>
                                    <option value="Bihar"<?php if($editpandetails->state=='Bihar'){ echo 'selected'; }?>>Bihar</option>
                                    <option value="Chandigarh"<?php if($editpandetails->state=='Chandigarh'){ echo 'selected'; }?>>Chandigarh</option>
                                    <option value="Chhattisgarh"<?php if($editpandetails->state=='Chhattisgarh'){ echo 'selected'; }?>>Chhattisgarh</option>
                                    <option value="Dadra and Nagar Haveli"<?php if($editpandetails->state=='Dadra and Nagar Haveli'){ echo 'selected'; }?>>Dadra and Nagar Haveli</option>
                                    <option value="Daman and Diu"<?php if($editpandetails->state=='Daman and Diu'){ echo 'selected'; }?>>Daman and Diu</option>
                                    <option value="Delhi"<?php if($editpandetails->state=='Delhi'){ echo 'selected'; }?>>Delhi</option>
                                    <option value="Goa"<?php if($editpandetails->state=='Goa'){ echo 'selected'; }?>>Goa</option>
                                    <option value="Gujarat"<?php if($editpandetails->state=='Gujarat'){ echo 'selected'; }?>>Gujarat</option>
                                    <option value="Haryana"<?php if($editpandetails->state=='Haryana'){ echo 'selected'; }?>>Haryana</option>
                                    <option value="Himachal Pradesh"<?php if($editpandetails->state=='Himachal Pradesh'){ echo 'selected'; }?>>Himachal Pradesh</option>
                                    <option value="Jammu & Kashmir"<?php if($editpandetails->state=='Jammu & Kashmir'){ echo 'selected'; }?>>Jammu & Kashmir</option>
                                    <option value="Jharkhand"<?php if($editpandetails->state=='Jharkhand'){ echo 'selected'; }?>>Jharkhand</option>
                                    <option value="Karnataka"<?php if($editpandetails->state=='Karnataka'){ echo 'selected'; }?>>Karnataka</option>
                                    <option value="Kerala"<?php if($editpandetails->state=='Kerala'){ echo 'selected'; }?>>Kerala</option>
                                    <option value="Lakshadweep"<?php if($editpandetails->state=='Lakshadweep'){ echo 'selected'; }?>>Lakshadweep</option>
                                    <option value="Madhya Pradesh"<?php if($editpandetails->state=='Madhya Pradesh'){ echo 'selected'; }?>>Madhya Pradesh</option>
                                    <option value="Maharashtra"<?php if($editpandetails->state=='Maharashtra'){ echo 'selected'; }?>>Maharashtra</option>
                                    <option value="Manipur"<?php if($editpandetails->state=='Manipur'){ echo 'selected'; }?>>Manipur</option>
                                    <option value="Meghalaya"<?php if($editpandetails->state=='Meghalaya'){ echo 'selected'; }?>>Meghalaya</option>
                                    <option value="Mizoram"<?php if($editpandetails->state=='Mizoram'){ echo 'selected'; }?>>Mizoram</option>
                                    <option value="Nagaland"<?php if($editpandetails->state=='Nagaland'){ echo 'selected'; }?>>Nagaland</option>
                                    <option value="Orissa"<?php if($editpandetails->state=='Orissa'){ echo 'selected'; }?>>Orissa</option>
                                    <option value="Pondicherry"<?php if($editpandetails->state=='Pondicherry'){ echo 'selected'; }?>>Pondicherry</option>
                                    <option value="Punjab"<?php if($editpandetails->state=='Punjab'){ echo 'selected'; }?>>Punjab</option>
                                    <option value="Rajasthan"<?php if($editpandetails->state=='Rajasthan'){ echo 'selected'; }?>>Rajasthan</option>
                                    <option value="Sikkim"<?php if($editpandetails->state=='Sikkim'){ echo 'selected'; }?>>Sikkim</option>
                                    <option value="Tamil Nadu"<?php if($editpandetails->state=='Tamil Nadu'){ echo 'selected'; }?>>Tamil Nadu</option>
                                    <option value="Telangana"<?php if($editpandetails->state=='Telangana'){ echo 'selected'; }?>>Telangana</option>
                                    <option value="Tripura"<?php if($editpandetails->state=='Tripura'){ echo 'selected'; }?>>Tripura</option>
                                    <option value="Uttar Pradesh"<?php if($editpandetails->state=='Uttar Pradesh'){ echo 'selected'; }?>>Uttar Pradesh</option>
                                    <option value="Uttaranchal"<?php if($editpandetails->state=='Uttaranchal'){ echo 'selected'; }?>>Uttaranchal</option>
                                    <option value="West Bengal"<?php if($editpandetails->state=='West Bengal'){ echo 'selected'; }?>>West Bengal</option>
                                </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group my-3">
                           {{ Form::label('Approval status*','Approval status*',array('class'=>'control-label text-bold'))}}
                            <select class="form-control form-control-solid p-1" id="panstatus" required="" name="status">
                            <option value=""> Select status </option>
                                <option value="1" <?php if($editpandetails->status==1){ echo 'selected'; }?>> Approved </option>
                                <option value="2" <?php if($editpandetails->status==2){ echo 'selected'; }?>> Reject </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group my-3">
                            {{ Form::label('Description*','Description*',array('class'=>'label-control text-bold'))}}
                            <textarea type="text" rows="3" class="form-control form-control-solid float-left mr-1" required="" name="comment" placeholder="Comment" id="commentpan" style="width:100%;"><?php echo  $editpandetails->comment;?></textarea>
                        </div>
                    </div>

                    <div class="col-12 text-right mt-4 mb-2">
	                    <button type="button" class="btn-sm btn btn-primary mt-1" id="onclickbtn" onclick="pandetailsupdate()"><i class="icon-lock"></i>Update Bank Details</button>
	                </div>
                </div>
            </div>
        </div>
     </form>
</div>

<script type="text/javascript">
function pandetailsupdate(){
  var accno = $("#accno").val();
  var ifsc = $("#ifsc").val();
  var bankbranch = $("#bankbranch").val();
  var bankname = $("#bankname").val();
  var state = $("#state").val();
  var getcomment = $("#commentpan").val();
  var getstatus = $("#panstatus").val();
  if(accno!="" && ifsc!="" && bankbranch!="" && bankname!="" && state!="" && getcomment != '' && getstatus != ''){
    if(getstatus!=""){
      if(getstatus==2){
        if(getcomment==""){
          Swal.fire('Please Enter comment first.');
          return false;
        }
      }
      document.getElementById("firstform").submit();
    }
  }
  else{
    Swal.fire('Please fill out all the fields');
  }
}
</script>
@endsection('content')
