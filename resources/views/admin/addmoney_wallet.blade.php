@extends('main')

@section('heading')
    User Manager
@endsection('heading')

@section('sub-heading')
    Admin Wallet Details
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('AdminwalletController@adminwallet') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" ><i class="fad fa-eye"></i>&nbsp; View List</a>
@endsection('card-heading-btn')

@section('content')

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo asset('my-admin/searchadminwallet')?>">
        <?php
        $name="";$userid="";$email="";
        if(isset($_GET['name'])){
          $name = $_GET['name'];
        }
        if(isset($_GET['userid'])){
          $userid = $_GET['userid'];
        }
        if(isset($_GET['email'])){
          $email = $_GET['email'];
        }
       ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group">
                        <div class="row mx-0">

                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Search by Team Name','Search by Team Name',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search by Team Name','id'=>'name1','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Search by User Id','Search by User Id',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('userid',$userid,array('value'=>'','placeholder'=>'Search By User Id','autocomplete'=>'off','id'=>'userid','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    {{ Form::label('Search by Email','Search by Email',array('class'=>'control-label text-bold'))}}
                                    {{ Form::text('email',$email,array('value'=>'','placeholder'=>'Search By Email','autocomplete'=>'off','id'=>'email','class'=>'form-control form-control-solid'))}}
                                </div>
                            </div>
                            <div class="col-12 text-right mt-4 mb-2">
                               <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                                <a href="<?php echo action('AdminwalletController@giveadminwallet')?>" class="btn btn-sm btn-warning text-uppercase"><i class="far fa-undo" ></i>&nbsp; Reset</a>
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
        Adminwallet Details
    </div>
    <div class="card-body">

        @include('alert_msg')

        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="userswallet_table" width="100%" cellspacing="0">
                <thead>
            <tr>
              <div class="fff"></div>
              <th>User ID</th>
              <th>Team</th>
              <th>User Name</th>
              <th>Email</th>
              <th>Action</th>
            </tr>
            <tbody>
               <?php
              if(!empty($allusers)){
                   if(count($allusers)>0){
                $sno=0;
                  foreach($allusers as $users){
                    ?>
                <tr role="row" class="odd" style="<?php if($users->status!='activated'){ echo 'color:red;'; } ?>">
                  <td class="sorting_1"><a href="<?php echo asset('my-admin/getuserdetails/'.$users->id)?>" style="text-decoration:underline;"><?php echo $users->id;?></td>
                  <td class="sorting_1"><?php echo $users->team;?></td>
                  <td class="sorting_1"><?php echo $users->username;?></td>
                  <td class="sorting_1"><?php echo $users->email;?></td>
                  <td>
                  <a data-toggle="modal" data-target="#addmoneymodal<?php echo $users->id;?>" class="editbtn" style="cursor:pointer;"><i class="fa fa-plus-circle"></i> Add Money </a>
                  <a data-toggle="modal" data-target="#deductmoneymodal<?php echo $users->id;?>" class="editbtn" style="cursor:pointer; float: right; color: red; "><i class="fa fa-minus-circle"></i><b> Deduct Money </b></a>
                  </td>
                  <div id="addmoneymodal<?php echo $users->id;?>" class="modal fade abc px-0" role="dialog" style="z-index: 0.2%;">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  w-100 h-100">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title _head_ing">Add Money in wallet of <?php echo $users->email;?> (UserId:- <?php echo $users->id;?>)</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                      <form action="<?php echo action('AdminwalletController@addmoneyinwallet')?>" method="post" class="amount_etc_info">
                            {{csrf_field()}}
                        <input type="hidden" value="<?php echo $users->id?>" name="userid" id="userid2">
                        <div class="col-md-12 col-sm-12 form-group ">
                          <label>  Amount </label>
                          <input type="number" onkeypress="return isNumberKey(event)" autocomplete="off" name="amount" class="form-control form-control-solid" placeholder="Enter amount here" id="add-input<?php echo $users->id;?>" required>
                          <input type="hidden" value="addmoney" name="moneytype" id="userid2">
                        </div>
                        <div class="col-md-12 col-sm-12 form-group ">
                        <label> Select Amount Type </label>
                        <select class="form-control form-control-solid my_sel" name="bonustype" required="required" id="add-select<?php echo $users->id;?>">
                          <option value="">Select Bonus Type</option>
                            <option value="addfund">AddFund</option>
                            <option value="bonus">Bonus</option>
                            <option value="winning">Winning</option>
                          </select>
                        </div>
                        <div class="col-md-12 col-sm-12 form-group ">
                          <label> Description </label>
                          <textarea class="form-control form-control-solid" id="descripdftion" name="description"></textarea>
                        </div>
                        </div>
                      <div class="modal-footer border-0">
                        <div class="col-auto text-right ml-auto mt-4 mb-2">
                          <input type="button" class="btn btn-sm btn-success text-uppercase adsfb" value="Next" data-open-modal="#key<?php echo $users->id;?>" data-id="<?php echo $users->id;?>" data-type="add">
                          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                      
                    </div>
                    </div>
                  </div>
                    <!-- Modal -->
                  <div id="key<?php echo $users->id;?>" class="modal fade px-0" role="dialog" style="z-index: 500%;">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  w-100 h-100">
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title _head_ing">Add Money in wallet of <?php echo $users->email;?> (UserId:- <?php echo $users->id;?>)</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body abcd">

                            <div class="col-md-12 col-sm-12 form-group _m_form">
                          <label> Enter Your Master Password </label>
                      <input type="password"  name="master" class="form-control form-control-solid" placeholder="Enter password here" autocomplete="off">
                        </div>
                      </div>
                   <div class="modal-footer">
                   <div class="col-auto text-right ml-auto mt-4 mb-2">
                      <input type="submit" class="btn btn-sm btn-success text-uppercase" value="Submit">
                      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                      </div>

                          </form>
                      </div>

                    </div>
                  </div>

                  <!-- Deduct Money modal -->
                        
                  <div id="deductmoneymodal<?php echo $users->id;?>" class="modal fade abc  px-0" role="dialog" style="z-index: 0.2%;">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  h-100">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title _head_ing">Deduct Money from wallet of <?php echo $users->email;?> (UserId:- <?php echo $users->id;?>)</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                      <form action="<?php echo action('AdminwalletController@deductmoneyinwallet')?>" method="post">
                            {{csrf_field()}}
                        <input type="hidden" value="<?php echo $users->id?>" name="userid" id="userid2">
                        <div class="col-md-12 col-sm-12 form-group ">
                          <label>  Amount </label>
                          <input type="text" onkeypress="return isNumberKey(event)" autocomplete="off" name="amount" class="form-control" placeholder="Enter amount here" id="deduct-input<?php echo $users->id;?>new" required>
                          <input type="hidden" value="addmoney" name="moneytype" id="userid2" autocomplete="off">
                        </div>
                        <div class="col-md-12 col-sm-12 form-group ">
                        <label> Select Amount Type </label>
                        <select class="form-control my_sel" name="bonustype" required="required" id="deduct-select<?php echo $users->id;?>new">
                          <option value="">Select Bonus Type</option>
                            <option value="addfund">AddFund</option>
                            <option value="bonus">Bonus</option>
                            <option value="winning">Winning</option>
                          </select>
                        </div>
                        <div class="col-md-12 col-sm-12 form-group ">
                          <label> Description </label>
                          <textarea class="form-control" id="descripdftion" name="description"></textarea>
                        </div>
                        </div>
                      <div class="modal-footer" style="border:0px;">
                        <div class="col-auto text-right ml-auto mt-4 mb-2">
                          <input type="button" class="btn btn-sm btn-success text-uppercase adsfb" value="Next" data-open-modal="#key12<?php echo $users->id;?>" data-id="<?php echo $users->id;?>new" data-type="deduct">
                          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                  
                    <!-- deductmoney modal -->
                  <div id="key12<?php echo $users->id;?>" class="modal fade px-0" role="dialog" style="z-index: 500%;">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable w-100 h-100">
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title _head_ing">Deduct  Money from wallet of <?php echo $users->email;?> (UserId:- <?php echo $users->id;?>)</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body abcd">
                          
                            <div class="col-md-12 col-sm-12 form-group _m_form">
                          <label> Enter Your Master Password </label>
                      <input type="password"  name="master" class="form-control" placeholder="Enter password here" autocomplete="off">
                        </div>
                      </div>
                   <div class="modal-footer">
                   <div class="col-auto text-right ml-auto mt-4 mb-2">
                      <input type="submit" class="btn btn-sm btn-success text-uppercase" value="Submit">
                      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                      </div>
                      
                          </form>
                      </div>

                    </div>
                  </div>

                </tr>
              <?php $sno++;}?>
              <?php }else{
              ?>
                <tr><td colspan="6">No Data Available</td></tr>
              <?php }}?>
            </tbody>
          </tbody>
          <tfoot>
              <tr>
            <div class="fff"></div>
              <th style="">User ID</th>
              <th>Team</th>
              <th>User Name</th>
              <th>Email</th>
              <th>Action</th>
              </tr>
          </tfoot>
            </table>
            <?php if(!empty($allusers)){ ?>
            <span class="float-right">{{ $allusers->appends($_GET)->links() }}</span>
            <?php }?>
        </div>
    </div>
</div>

<script>
$(document).on("click", ".adsfb", function () {


  var type = $(this).attr('data-type');
  var input = $('#'+type+'-input' + $(this).attr('data-id')).val();
  var select = $('#'+type+'-select' + $(this).attr('data-id')).val();

  if(input == '' || select == '') {
    Swal.fire('Please fill out all required fields');
  } else {
    var modal = $(this).attr('data-open-modal');
    $('.abc').modal("hide");
    $(modal).modal('show');
  }
  // $('.abc').modal("hide");

  // var a = $('#drfgdgdfg1').val();
  // var c = $('#descripdftion').val();
  // var b = $('#Sel_BankApprcoval1').val();
  // var d = $('#userid2').val();
  
  // $(".abcd #amount1").val(a);
  // $(".abcd #bonustype1").val(b);
  // $(".abcd #description1").val(c);
  // $(".abcd #userid1").val(d);

  // if(a !== '' && b !== '') {
  //   $('.abc').modal("hide");
  // } else {
  //   Swal.fire('Please fill out all required fields');
  // }

});
</script>
<script>
$('#userswallet_table').dataTable({
    "paging": false,
    "searching": false,
    "LengthChange": false,
    "Filter": false,
    "Info": false,
    "showNEntries" : false
});
</script>
@endsection('content')
