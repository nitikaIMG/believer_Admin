@extends('main')

@section('heading')
    Series Manager
@endsection('heading')

@section('sub-heading')
    Series Price Card
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('SeriesController@index') ?>" class="btn btn-sm  btn-sm rounded-pill btn-light font-weight-bold text-primary float-right"><i class="fad fa-eye"></i>&nbsp; View All Series</a>
@endsection('card-heading-btn')

@section('content')

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    Add Price Cards
                </div>
                <div class="card-body">
                    <div class="sbp-preview">
                        <div class="sbp-preview-content">
                            <?php $getid = $findchallenge1->id; ?>

                            {{ Form::open(['action' => ['SeriesController@addmatchpricecard', base64_encode(serialize($getid))], 'method' => 'post', 'id' => 'j-forms', 'class' => 'j-forms', 'enctype' => 'multipart/form-data']) }}

                            {{ csrf_field() }}


                            @include('alert_msg')

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('Start Position*', 'Start Position*', ['class' => 'label-control']) }}
                                        {{ Form::text('min_position', $min_position, ['value' => '$min_position', 'required' => '', 'placeholder' => 'Enter starting position', 'readonly' => '', 'min' => '0', 'class' => 'form-control form-control-solid']) }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('Number Of Winners*', 'Number Of Winners*', ['class' => 'label-control text-bold']) }}
                                        {{ Form::number('winners', null, ['value' => '$min_position', 'required' => '', 'placeholder' => 'Enter number of winner', 'min' => '1', 'class' => 'form-control form-control-solid', 'autocomplete' => 'off']) }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('Price Amount In Rupees*', 'Price Amount In Rupees*', ['class' => 'label-control text-bold']) }}
                                        {{ Form::text('price', null, ['value' => '', 'required' => '', 'placeholder' => 'Price Amount In Rupees', 'min' => '1', 'autocomplete' => 'off', 'class' => 'form-control form-control-solid', 'onkeyup' => 'isNumberKey(this)']) }}
                                    </div>
                                </div>
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
                    Series Price Card
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
                                    <th>Each Winner</th>
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
                                    <td class="font-weight-bold"><?php echo $pricecars->price_percent; ?></td>
                                    <td class="font-weight-bold"><?php echo $pricecars->total; ?>rs(<?php echo $pricecars->price_percent * $pricecars->winners; ?>%)</td>
                                    <?php }
                                    if ($pricecars->price != null) { ?>
                                    <td class="font-weight-bold"><?php echo $pricecars->price; ?></td>
                                    <td class="font-weight-bold"><?php echo $pricecars->total; ?></td>
                                    <?php }
                                    ?>

                                    <?php
                                    $wamt = $findchallenge1->win_amount ?? "";
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
                                            onclick="delete_confirmation('<?php echo action("SeriesController@deletematchpricecard", base64_encode(serialize($pricecars->id))); ?>')" data-toggle="tooltip" title="Delete"> <i class="far fa-trash"></i> </a></td>
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
                                    <td class="font-weight-bold">Total-<?php echo $twin; ?></td>
                                    <td class="font-weight-bold"></td>
                                    <td class="font-weight-bold">Total-<?php echo $tamt; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>S No.</th>
                                    <th>PriceCard Type</th>
                                    <th>Min Position</th>
                                    <th>Max Position</th>
                                    <th>Winning Users</th>
                                    <th>Each Winner</th>
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
        var winning_amount = '<?php echo $findchallenge1->win_amount ?? "" ; ?>';

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

@endsection('content')
