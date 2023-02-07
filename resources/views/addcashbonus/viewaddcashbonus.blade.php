@extends('main')

@section('heading')
    Addcash bonus Manager
@endsection('heading')

@section('sub-heading')
    View All Addcash bonus
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('AddcashController@addcash_bonus') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right"><i class="fa fa-plus"></i>&nbsp; Add Addcash bonus</a>
@endsection('card-heading-btn')

@section('content')




<div class="card mb-4">
    <div class="card-header">View All Addcash bonus</div>
    <div class="card-body">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
        <div class="datatable table-responsive">

        @include('alert_msg')

            <table class="table table-bordered table-hover text-nowrap" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Amount Range</th>
                        <th>Percentage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Amount Range</th>
                        <th>Percentage</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
              <?php if(!empty($getbonus)){ ?>
                  <?php $sno=1;?>
                  <?php foreach($getbonus as $getbonuss){?>
                    <tr role="row" class="odd">
                    <td class="sorting_1"><?php echo $sno;?></td>
                    <td class="sorting_1"><?php echo $getbonuss->amt_range;?></td>
                    <td class="sorting_1"><?php echo $getbonuss->percentage;?></td>
                    <td>
                      <a href="<?php echo asset('my-admin/editbonus/'.base64_encode(serialize($getbonuss->id)))?>" class="btn btn-sm btn-primary w-35px h-35px text-uppercase"><i class ='fas fa-pencil'></i></a>
                      <?php
                            $onclick = "delete_sweet_alert('".action('AddcashController@delleteaddcashbonus',base64_encode(serialize($getbonuss->id)))."', 'Are you sure you want to delete this data?')";
                        ?>
                      <a  onclick="<?php echo $onclick; ?>" class="btn btn-sm btn-danger w-35px h-35px text-uppercase"><i class='fas fa-trash-alt'></i></a></div>
                   </td>
                </tr>
                <?php $sno++; } ?>
                <?php } else{
                  ?>
                  <tr role="row" class="odd">
                  <td colspan="8" class="text-center">No Results Available</td>
                  </tr>
                  <?php
                }?>
            </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>
@endsection('content')
