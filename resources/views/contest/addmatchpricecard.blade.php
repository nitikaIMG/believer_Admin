@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    Custom Contest Price Card
@endsection('sub-heading')

@section('card-heading-btn')
    <a href="<?php echo action('ContestController@create_custom_contest'); ?>"
        class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" data-toggle="tooltip"
        title="View All Custom Contest"><i class="fas fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="sbp-preview">
                <div class="sbp-preview-content">
                    <div class="row">
                        <div class="col-md mr-md-5">
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Winning Amount :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    {{ ucwords($findchallenge1->win_amount) }}</div>
                            </div>
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Entry Fees :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    {{ ucwords($findchallenge1->entryfee) }}</div>
                            </div>
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Maximum Users :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    {{ ucwords($findchallenge1->maximum_user) }}</div>
                            </div>
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Multiple Entry :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    @if ($findchallenge1->multi_entry == 1) <span
                                        class="font-weight-bold text-success">Yes</span> @else <span
                                            class="font-weight-bold text-danger">No</span> @endif
                                </div>
                            </div>
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Is Running :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    @if ($findchallenge1->is_running == 1) <span
                                        class="font-weight-bold text-success">Yes</span> @else <span
                                            class="font-weight-bold text-danger">No</span> @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md ml-md-5">
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Confirm Contest :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    @if ($findchallenge1->confirmed_challenge == 1) <span
                                        class="font-weight-bold text-success">Yes</span> @else <span
                                            class="font-weight-bold text-danger">No</span> @endif
                                </div>
                            </div>
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Is Bonus Allowed :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    @if ($findchallenge1->is_bonus == 1) <span
                                        class="font-weight-bold text-success">Yes</span> @else <span
                                            class="font-weight-bold text-danger">No</span> @endif
                                </div>
                            </div>
                            <div class="row py-2 border-bottom">
                                <div class="col-md font-weight-bold fs-md-15 fs-14">Contest Category :-</div>
                                <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                    @if (!empty($cat)) {{ $cat->name }} </beautify
                                        end="@endif">
                                </div>
                            </div>
                            @if ($findchallenge1->is_bonus == 1)
                                <div class="row py-2 border-bottom">
                                    <div class="col-md font-weight-bold fs-md-15 fs-14">Bonus Percentage :-</div>
                                    <div class="col-md-auto font-weight-bold fs-md-15 fs-14">
                                        {{ $findchallenge1->bonus_percentage }}% </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    Add Match Price Cards
                </div>
                <div class="card-body">
                    <div class="sbp-preview">
                        <div class="sbp-preview-content">
                            <?php $getid = $findchallenge1->id; ?>

                            {{ Form::open(['action' => ['ContestController@addmatchpricecard', base64_encode(serialize($getid))], 'method' => 'post', 'id' => 'j-forms', 'class' => 'j-forms', 'enctype' => 'multipart/form-data']) }}

                            {{ csrf_field() }}


                            @include('alert_msg')

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('Start Position*', 'Start Position*', ['class' => 'label-control']) }}
                                        {{ Form::text('min_position', $min_position, ['value' => '$min_position', 'required' => '', 'placeholder' => 'Enter starting position', 'readonly' => '', 'min' => '0', 'class' => 'form-control form-control-solid']) }}
                                    </div>
                                </div>
                                <?php if($findchallenge1->pricecard_type == 'Percentage'){ ?>
                                    <div class="col-lg-3 col-md-6 col-12">
                                            <label class="label-control text-bold">Users Selection</label>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox custom-control-inline">
                                               <input type="radio" class="custom-control-input sel_all" id="select_all2" name="user_selection" value="number" required onchange="$('#labelnumber').html('Number Of Winners*'),$('#winnum, #winnum2, #winnum3').html('0'),$('#percentuser').stop().slideUp(1000),$('#winnernumber, #winnerpercent').val('')">
                                                <label class="custom-control-label fs-15" for="select_all2">Users By Numbers</label>
                                            </div>
                                        </div>
                                    </div>
                                    @if($findchallenge1->fantasy_type!='Duo')
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="label-control text-bold"></label>
                                        <div class="form-group my-1">
                                            <div class="custom-control custom-checkbox custom-control-inline">
                                               <input type="radio" class="custom-control-input sel_all" id="select_all3" name="user_selection" value="percentage" required onchange="$('#labelnumber').html('Percentage of Users *'),$('#percentuser, #percentuser2').stop().slideDown(1000),$('#winnernumber, #winnerpercent').val(''),$('#winnum, #winnum2, #winnum3').html('0')">
                                                <label class="custom-control-label fs-15" for="select_all3">Users By Percentage</label>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('Number Of Winners*', 'Number Of Winners*', ['class' => 'label-control text-bold']) }}
                                        {{ Form::text('winners', null, ['value' => '$min_position', 'required' => '', 'placeholder' => 'Enter number of winner', 'min' => '1', 'class' => 'form-control form-control-solid', 'autocomplete' => 'off','onkeyup'=>'isNumberKey(this)','id'=>'winnernumber']) }}
                                        <span class="text-danger" id="percentuser" style="display: none;">Total Number of users: - <span id="winnum">0</span></span>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('Number Of Winners*', 'Number Of Winners*', ['class' => 'label-control text-bold']) }}
                                        {{ Form::number('winners', null, ['value' => '$min_position', 'required' => '', 'placeholder' => 'Enter number of winner', 'min' => '1', 'class' => 'form-control form-control-solid', 'autocomplete' => 'off']) }}
                                        
                                    </div>
                                </div>
                            <?php 
                                }
                                if ($findchallenge1->pricecard_type == 'Amount') { ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('Price Amount In Rupees*', 'Price Amount In Rupees*', ['class' => 'label-control text-bold']) }}
                                        {{ Form::text('price', null, ['value' => '', 'required' => '', 'placeholder' => 'Price Amount In Rupees', 'min' => '1', 'autocomplete' => 'off', 'class' => 'form-control form-control-solid', 'onkeyup' => 'isNumberKey(this)']) }}
                                    </div>
                                </div>
                                <?php } else { ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('Price Amount In Percentage*', 'Price Amount In Percentage*', ['class' => 'label-control text-bold']) }}

                                        {{ Form::text('price_percent', null, ['value' => '', 'required' => '', 'placeholder' => 'Price Amount In Percentage ', 'min' => '1', 'autocomplete' => 'off', 'class' => 'form-control form-control-solid','onkeyup'=>'isNumberKey(this)','id'=>'winnerpercent']) }}
                                        <span class="text-danger" id="percentuser2" style="/*display: none;*/">Amount of Each User: - <span id="winnum2">0</span><br>Amount of All User: - <span id="winnum3">0</span></span>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="row justify-content-end">
                              <div class="col-md-auto">
                                <button type="submit" class=" btn btn-sm btn-success ml-1"><i class="far fa-check-circle"></i>&nbsp;
                                  Submit</button>
                              <button type="reset" class="btn btn-sm btn-warning"
                                  onclick="window.location.href=window.location.href"><i
                                      class="fa fa-undo"></i>&nbsp;Reset</button>
                              </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    Custom Contest Price Card
                </div>
                <div class="card-body">
                    <div class="datatable table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>PriceCard Type</th>
                                    <th>Min Position</th>
                                    <th>Max Position</th>
                                    <th>Winning Users</th>
                                    <th>Each Winner {{$findchallenge1->pricecard_type}}</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $per = 0;
                                $tper = 0;
                                $tamt = 0;
                                $twin = 0;
                                $totalper = 0;
                                if (!empty($findallpricecards)) {

                                $snoo = 0;
                                $countpricecards = count($findallpricecards);
                                ?>
                                <?php foreach ($findallpricecards as $pricecars) {
                                $snoo++; ?>
                                <tr role="row" class="odd">
                                    <td class="font-weight-bold"><?php echo $snoo; ?></td>
                                    <?php if ($pricecars->price == null) { ?>
                                    <td class="font-weight-bold"><?php echo 'Percentage'; ?></td>
                                    <?php } else { ?>
                                    <td class="font-weight-bold"><?php echo 'Amount'; ?></td> <?php }
                                    ?>
                                    <td class="font-weight-bold"><?php echo $pricecars->min_position; ?></td>
                                    <td class="font-weight-bold"><?php echo $pricecars->max_position; ?></td>
                                    <td class="font-weight-bold"><?php echo $pricecars->winners; ?></td>

                                    <?php
                                    if ($pricecars->price_percent != null) { ?>
                                    <td class="font-weight-bold"><?php echo number_format((($findchallenge1->win_amount*$pricecars->price_percent)/100),2,'.','').'Rs - '.$pricecars->price_percent.'%'; ?></td>
                                    <td class="font-weight-bold"><?php echo $pricecars->total; ?>rs(<?php echo $pricecars->price_percent * $pricecars->winners; ?>%)</td>
                                    <?php }
                                    if ($pricecars->price != null) { ?>
                                    <td class="font-weight-bold"><?php echo $pricecars->price; ?></td>
                                    <td class="font-weight-bold"><?php echo $pricecars->total; ?></td>
                                    <?php }
                                    ?>

                                    <?php
                                    $wamt = $findchallenge1->win_amount;
                                    $ttt = $pricecars->total;
                                    if ($wamt != 0) {
                                    $totalper = ($ttt / $wamt) * 100;
                                    }
                                    $tper = $tper + $totalper;
                                    $tamt = $tamt + $ttt;
                                    $twin = $twin + $pricecars->winners;
                                    ?>

                                    @if ($snoo == $countpricecards)
                                    <td class="font-weight-bold"><a class="btn btn-sm btn-danger w-35px h-35px"
                                            onclick="delete_confirmation('<?php echo action("ContestController@deletematchpricecard", base64_encode(serialize($pricecars->id))); ?>')" data-toggle="tooltip" title="Delete"> <i class="far fa-trash"></i> </a></td>
                                    @else
                                    <td class="font-weight-bold"></td>
                                    @endif
                                </tr>

                                <?php
                                } ?>
                                <?php
                                }
                                ?>
                            </tbody>
                            <tfoot>
                            
                                <tr>
                                    <td class="font-weight-bold"></td>
                                    <td class="font-weight-bold"></td>
                                    <td class="font-weight-bold"></td>
                                    <td class="font-weight-bold"></td>
                                    <td class="font-weight-bold">Total-<?php echo $twin.'('.number_format((($twin/$findchallenge1->maximum_user)*100),2,'.','').'%)'; ?></td>
                                    <td class="font-weight-bold"></td>
                                    <td class="font-weight-bold">Total-<?php echo $tamt.'('.number_format($tper,2,'.','').'%)'; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>S No.</th>
                                    <th>PriceCard Type</th>
                                    <th>Min Position</th>
                                    <th>Max Position</th>
                                    <th>Winning Users</th>
                                    <th>Each Winner {{$findchallenge1->pricecard_type}}</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var winning_amount = '<?php echo $findchallenge1->win_amount ; ?>';

        function perrre() {
            var perr = $('#perrer').val();
            if (perr > 100) {
                Swal.fire('You cannot enter more than 100%');
            }
            var result = (perr / 100) * winning_amount;
            // alert(result);
            document.getElementById('rsss').value = result;
        }

        function rss() {
            var rss = $('#rsss').val();
            var result = (rss / winning_amount) * 100;
            document.getElementById('perrer').value = result;
        }

    </script>
    <script>
function delete_confirmation(url) {
  // sweet alert
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-sm btn-success ml-2',
        cancelButton: 'btn btn-sm btn-danger'
    },
    buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'success',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'No, cancel!',
    reverseButtons: true
    }).then((result) => {
    if (result.isConfirmed) {

      swalWithBootstrapButtons.fire(
                'Deleted!',
                'Price card deleted successfully.',
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
<script>
    var winning_amount = '<?php echo $findchallenge1->win_amount ; ?>';
    $('#winnernumber').keyup(function(event) {
        let users = '{{(int)$findchallenge1->maximum_user}}';
        let winamt = '{{(int)$findchallenge1->win_amount}}';
        let typedusers = $(this).val();
        let totalusers = Math.round((users*typedusers)/100);
        // console.log(typedusers);
        $('#winnum').html(totalusers);
        // if($('input[name=user_selection]:checked').val()=='number'){
            let winper = $('#winnerpercent').val();
            if(winper!='' && typedusers!=''){
             let totaluse = ((winamt*winper)/100);
             $('#winnum2').html(totaluse.toFixed(2));
             if($('input[name=user_selection]:checked').val()=='number'){
                    let amt = $('#winnernumber').val()*totaluse;
                    
                    $('#winnum3').html(amt.toFixed(2));
                }else{
                 let tusers = $('#winnum').text();
                    let amt = tusers*totaluse;
                    // $('#winnum2').html(totaluse.toFixed(2));
                    $('#winnum3').html(amt.toFixed(2));
                }
            }else{
             $('#winnum2').html('0');
                $('#winnum3').html('0');
            }
        // }
    });

    $('#winnerpercent').keyup(function(event) {
          let winamt = '{{(int)$findchallenge1->win_amount}}';
          let typedusers = $('#winnum').text();
          let winper = $(this).val();
          if(winper!=''){
              let totalusers = ((winamt*winper)/100);
              $('#winnum2').html(totalusers.toFixed(2));
              if($('input[name=user_selection]:checked').val()=='number'){
                  let amt = $('#winnernumber').val()*totalusers;
                  $('#winnum3').html(amt.toFixed(2));
              }else{
                  let amt = typedusers*totalusers;
                  $('#winnum3').html(amt.toFixed(2));
              }
          }else{
              $('#winnum2').html('0');
              $('#winnum3').html('0');
          }
          
    });
</script>
@endsection('content')
