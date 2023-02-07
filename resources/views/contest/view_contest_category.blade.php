@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    View all contest category
@endsection('sub-heading')

@section('card-heading-btn')
<a href="<?php echo action('ContestController@create_category') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" data-toggle="tooltip" title="Add New Contest Category"><i class="fas fa-plus"></i>&nbsp; Add</a>
@endsection('card-heading-btn')

@section('content')

<style>
#dataTable_paginate {
    display:none;
}
</style>

<div class="row">
    <div class="col-md-12">
      <div class="card mb-3">
        <div class="card-heading p-3">
          <form method="get" action="<?php echo action('ContestController@view_search_contest_category')?>">
                  <?php
                    $name="";
                    if(isset($_GET['name'])){
                      }
                    $fantasy_type = '';
                    if(isset($_GET['fantasy_type'])){
                      $fantasy_type = $_GET['fantasy_type'];
                    }
                  ?>
                <div class="sbp-preview position-relative">
                    <div class="form-group mb-0">
                        <div class="row mx-0 align-items-end">
                        
                        <div class="col-md">
                            <div class="form-group my-3">
                                {{ Form::label('Contest Name','Contest Name',array('class'=>'text-bold'))}}
                                {{ Form::text('name',$name,array('value'=>$name,'placeholder'=>'Search By Contest Name','id'=>'name','class'=>'form-control form-control-solid'))}}
                            </div>
                        </div>
                        <div class="col-md-auto text-right h-100 mb-3">
                            <button type="submit" class="btn btn-success text-uppercase btn-sm rounded-pill"><i class="far fa-check-circle" ></i>&nbsp; Submit</button>
                            <a href="<?php echo action('ContestController@view_contest_category')?>" class='btn btn-warning text-uppercase btn-sm rounded-pill'><i class="fa fa-undo"></i>&nbsp;Reset</a>
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
    <div class="card-header">Contest Categories</div>
    <div class="card-body">
        
        @include('alert_msg')
        
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="dataTabless" width="100%" cellspacing="0">
                <thead>
            <tr>

              <th>Sno.</th>
              <th>Contest Category Name</th>
              <th>Sub Title</th>
              <th>Image</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
    <?php
        if(count($contest_data)>0){
            if(!empty($contest_data)){
                $sno = 1;
            foreach($contest_data as $value){
              ?>
                <tr>
                    <!--<td><input type="checkbox" name="checkCat" class="checkbox" id="check" value="<php echo $value->id;?>" style="align-items: center;justify-content: center;display: flex;"></td>-->
                    <td class="font-weight-bold">
                        <?php echo $sno; ?>
                    </td>
                    <td>
                        <?php echo $value->name; ?>
                    </td>
                    <td>
                        <?php echo $value->sub_title; ?>
                    </td>
                    <td>
                        <?php if($value->image == ''){ ?>
                            <img src="<?php echo asset('public/'.auth()->user()->settings()->logo ?? '');?>" class="w-40px view_team_table_images h-40px rounded-pill">
                            <?php }else{ ?>
                                <img src="<?php echo asset('public/'.$value->image );?>" class="w-40px view_team_table_images h-40px rounded-pill" onerror="this.src='{{ asset('public/'.auth()->user()->settings()->logo ?? '')}}'">
                                <?php } ?>
                    </td>
                    <td>
                        <a class="btn w-35px h-35px mr-1 btn-orange text-uppercase btn-sm" data-toggle="tooltip" title="Edit" href="{{ action('ContestController@edit_contest_category',base64_encode(serialize($value->id)))}}">
                            <i class="fas fa-pencil"></i>
                        </a>

                        <?php
                            $onclick = "delete_sweet_alert('".action('ContestController@delete_contest_category',base64_encode(serialize($value->id)))."', 'Are you sure you want to delete this data?')";
                        ?>
                        <a class="btn w-35px h-35px mr-1 btn-danger text-uppercase btn-sm" data-toggle="tooltip" title="Delete" onclick="<?php echo $onclick; ?>">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php
              $sno++;
            }

            ?>
            <?php }}else{?>
                <td colspan="5" style="text-align:center;">No data available</td>
                <?php }
            ?>
            </tbody>
          <tfoot>
              <tr>
                  <th>Sno.</th>
                  <th>Contest Category Name</th>
                  <th>Sub Title</th>
                  <th>Image</th>
                  <th>Action</th>
              </tr>
          </tfoot>
            </table>
            <!-- <span class="float-right"></span> -->
        </div>
    </div>
</div>
@endsection('content')
