@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    View all custom contests
@endsection('sub-heading')

<style>
    .cursor-pointer : {
        cursor: pointer !important;
    }
</style>

@section('content')
<div class="row">
  <div class="col-lg-12">

    <div class="card">
        <div class="card-header">Choose Match</div>
        <div class="card-body">
          
          
        @include('alert_msg')
        
          {{ Form::open(array('action' => 'ContestController@create_custom_contest', 'method' => 'get'))}}

            {{csrf_field()}}

            <?php $matches="";
              $getmatchid="";
                if(isset($_GET['matchid'])){
                  $getmatchid = $_GET['matchid'];
                }
            ?>
            <div class="sbp-preview">
                <div class="sbp-preview-content p-2">
                    <div class="row mx-0 align-items-end">
                        <div class="col-md">
                            <div class="form-group">
                                {{ Form::label('Select Match*','Select Match*',array('style'=>'color:black;'))}}
                                <select class="form-control form-control-solid selectpicker show-tick" data-container="body" data-live-search="true" title="Select Match" data-hide-disabled="true" name="matchid" onchange="this.form.submit()" id="matchid">
                                <option value=""> Select Match </option>
                                <?php
                                    if(!empty($findalllistmatches->toarray())){
                                    foreach($findalllistmatches as $matches){

                                        ?>
                                        <option value="<?php echo $matches->matchkey; ?>"
                                        <?php if($matches->matchkey==$getmatchid){ echo 'selected'; }?>>
                                        <?php echo ucwords($matches->name);?> </option>
                                        <?php
                                    }
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto mb-md-3">
                            <?php if(isset($_GET['matchid'])){
                            ?>
                            <!-- <a  href="<?php //echo action('ContestController@importdata', [$_GET['matchid']])?>" class="btn btn-sm btn-warning font-weight-bold text-uppercase h-35px" data-toggle='tooltip' title='Import Default Contests'><i class="fas fa-download"></i> &nbsp;Import</a> -->
                            <!-- ================================================================= -->
                            <a href="<?php echo action('ContestController@selectglobalcontest',[$_GET['matchid']])?>" class="btn btn-sm btn-warning font-weight-bold text-uppercase h-35px" data-toggle='tooltip' title='Import Default Contests'><i class="fas fa-download"></i> &nbsp;Import</a>
                            <?php } ?>
                        </div>
                        <div class="col-md-auto mb-md-3">
                        <a  href="<?php echo action('ContestController@create_custom', $_GET) ?>" class="btn btn-sm btn-primary font-weight-bold text-uppercase h-35px" data-toggle='tooltip' title='Add New Custom Contests'><i class="fas fa-plus"></i> &nbsp;Add</a>
                        </div>
                    </div>
                </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
  </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Contest Manager - Contest List</div>
            <div class="card-body">

                <div class="row" row>
                <?php
                $matchkey="";
                  if(!empty($allchallenges)){
                    foreach($allchallenges as $challenge){
                          if(($challenge->is_private == '0') || (($challenge->is_private == '1') && ($challenge->joinedusers != '0'))){
                        $matchkey=$challenge->matchkey;
                        ?>
                            <div class="row shadow border-0 mx-0 p2 h-100 rounded-10 border border-primary bg-primary pt-1 m-2">
                                <div class="datatable table-responsive bg-white rounded-10">
                                    <table class="table mb-0 table-hover text-nowrap w-100 fs-12" id="global_index_datatable" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td class="font-weight-bold text-primary fs-15">Win Amount</td>
                                            <td class="font-weight-bold text-success fs-17 text-right"><?php echo $challenge->win_amount;?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Fantasy Type</td>
                                            @if($challenge->fantasy_type=='Duo')
                                                <td class="font-weight-bold text-gray text-right"><?php echo $challenge->fantasy_type.'('.$challenge->duotype.')';?></td>
                                            @else
                                                <td class="font-weight-bold text-gray text-right"><?php echo $challenge->fantasy_type;?></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">League Size</td>
                                            <td class="font-weight-bold text-gray text-right"><?php echo $challenge->maximum_user;?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Entry Fee</td>
                                            <td class="font-weight-bold text-gray text-right"><?php echo $challenge->entryfee;?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Contest Type</td>
                                            <td class="font-weight-bold text-gray text-right"><?php echo $challenge->contest_type;?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">League Type</td>
                                            <td class="font-weight-bold text-gray text-right"><?php if($challenge->confirmed_challenge==1){ echo 'Confirmed League'; }else{ echo 'Not Confirmed'; }?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Multi Entry</td>
                                            <td class="font-weight-bold text-gray text-right"><?php if($challenge->multi_entry==1){ echo '<span class="font-weight-bold text-success">Yes</span>'; }else{ echo '<span class="font-weight-bold text-danger">No</span>'; }?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Auto Repeat</td>
                                            <td class="font-weight-bold text-gray text-right"><?php if($challenge->is_running==1){ echo '<span class="font-weight-bold text-success">Yes</span>'; }else{ echo '<span class="font-weight-bold text-danger">No</span>'; }?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Joined Users</td>
                                            <td class="font-weight-bold text-gray text-right"><?php if($challenge->joinedusers){ echo $challenge->joinedusers; }else{ echo 0; }?></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Bonus Percentage</td>
                                            <td class="font-weight-bold text-gray text-right"><?php if($challenge->bonus_percentage){ echo $challenge->bonus_percentage; }else{ echo 0; }?></td>
                                        </tr>
                                        <tr class="">
                                            <td class="font-weight-bold text-black d-flex align-items-center">Created Date</td>
                                            <td class="font-weight-bold text-gray text-right">
                                            <span class="font-weight-bold text-success"><?php echo date('l,',strtotime($challenge->created_at))?></span><br>
                                            <span class="font-weight-bold text-primary"><?php echo date('d-M-y',strtotime($challenge->created_at))?></span><br>
                                            <span class="font-weight-bold text-danger"><?php echo date('h:i:s a',strtotime($challenge->created_at))?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-black">Created By</td>
                                            <?php
                                            $findteam = "";
                                            if($challenge->created_by!=0){
                                                $findteamaname = DB::table('registerusers')->where('id',$challenge->created_by)->select('team')->first();
                                                $findteam = $findteamaname->team;
                                                }else{
                                                    $findteam = 'Admin';
                                                }
                                                ?>
                                                <td class="font-weight-bold text-gray text-right"><?php echo $findteam; ?></td>
                                            </tr>
                                            <?php if($challenge->status!='canceled'){?>
                                            <tr>
                                                <td class="font-weight-bold text-black">Action</td>
                                                <td class="font-weight-bold text-gray text-right position-relative">
                                                <div class="btn-group dropdown position-absolute bottom-6px right-8px">
                                                    <button class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-icon border-0 shadow-none overflow-hidden" data-toggle="dropdown" type="button" aria-expanded="true">
                                                        Action <i class="dropdown-caret"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" style="opacity: 1;">
                                                    
                                                    <?php if($challenge->joinedusers == 0){ ?>
                                                        <li><a class="nav-link" href="<?php echo action('ContestController@editcustomcontest',base64_encode(serialize($challenge->id)))?>">Edit Contest</a></li>
                                                    <?php } ?>
                                                        <?php if(empty($challenge->joinedusers)){?>
                                                        <li><a class="nav-link cursor-pointer" onclick="delete_confirmation('<?php echo action('ContestController@delete_customcontest',base64_encode(serialize($challenge->id)))?>', 'Are you sure you want to delete this contest?')" style="cursor: pointer">Delete Contest</a></li>
                                                        <?php }?>
                                                        <li><a class="nav-link cursor-pointer" onclick="delete_confirmation('<?php echo action('ContestController@contestcancel',$challenge->id)?>', 'Are you sure you want to Cancel this contest?')" style="cursor: pointer">Cancel Contest</a></li>
                                                        <?php if($challenge->contest_type == 'Amount'){ ?>
                                            <li><a class="nav-link" href="<?php echo action('ContestController@addmatchpricecard',base64_encode(serialize($challenge->id)))?>" style="" class="">Add/Edit Price Card</a></li>
                                        <?php }?>
                                            <?php if($challenge->confirmed_challenge == '0'){ ?>
                                            <li><a  class="nav-link" href="<?php echo action('ContestController@makeConfirmed',base64_encode(serialize($challenge->id)))?>" style="" class="">Confirm Contest</a></li>
                                        <?php }?>
                                                    </ul>
                                                </div>
                                                </td>
                                                </tr>
                                                <?php }else{?>
                                                <tr>
                                                    <td class="font-weight-bold text-black">Status</td>
                                                    <td class="font-weight-bold text-gray text-right">
                                                        <?php echo $challenge->status;?>
                                                        </td>
                                                </tr>
                                                <?php }?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php
                                }
                            }
                        }
                    ?>


                </div>
    </div>
</div>
</div>
</div>

<script>
function get_fantasy_matches(value){
    var fantasy_type = value;
    $.ajax({
    type:'POST',
    url:'<?php echo asset('my-admin/get_fantasy_matches');?>',
    data:'_token=<?php echo csrf_token();?>&fantasy_type='+fantasy_type,
    success:function(data){
        $('#matchid').html('<option value="">Select Match</option>');

        for(var i = 0; i < data.length; i++) {
            // alert(data[i]['id']);
            $('#matchid').append('<option value="'+data[i]['matchkey']+'">'+data[i]['name']+'</option>');
        }

    }
    });
}
</script>
<!-------------- End of panel body -------------->
<script>
function delete_confirmation(url, msg) {
  // sweet alert
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-sm btn-success ml-2',
        cancelButton: 'btn btn-sm btn-danger'
    },
    buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
    title: msg,
    text: "You won't be able to revert this!",
    icon: 'success',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    reverseButtons: true
    }).then((result) => {
    if (result.isConfirmed) {

      swalWithBootstrapButtons.fire(
                '',
                'Successfully Done',
                'success'
                );

      window.location.href = url;

        
    } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
    ) {
        swalWithBootstrapButtons.fire(
        'Cancelled',
        'Cancelled successfully :)',
        'error'
        );
        return false;
    }
  })
}
</script>

@endsection('content')
