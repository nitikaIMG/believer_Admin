@extends('main')

@section('heading')
    Full Series Detail
@endsection('heading')

@section('sub-heading')
    View All Contest Detail
@endsection('sub-heading')

@section('content')


<div class="card mb-4">
    <div class="card-header">View All Contests</div>
    <div class="card-body">

        @include('alert_msg')
        
        <div class="datatable table-responsive overflow-auto">
            <table class="table table-bordered table-hover text-nowrap" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th data-toggle="tooltip" title="Serial Number">#</th>
                        <th data-toggle="tooltip" title="Winning Amount">Win Amt.</th>
                        <th data-toggle="tooltip" title="League Size">L Size</th>
                        <th data-toggle="tooltip" title="Entry Fee">E. Fee</th>
                        <th data-toggle="tooltip" title="Contest Type">C. Type</th>
                        <th data-toggle="tooltip" title="League Type">L. Type</th>
                        <th data-toggle="tooltip" title="Multi Entry">M. Entry</th>
                        <th>Is Running</th>
                        <th>Joined Users</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th data-toggle="tooltip" title="Serial Number">#</th>
                        <th data-toggle="tooltip" title="Winning Amount">Win Amt.</th>
                        <th data-toggle="tooltip" title="League Size">L Size</th>
                        <th data-toggle="tooltip" title="Entry Fee">E. Fee</th>
                        <th data-toggle="tooltip" title="Contest Type">C. Type</th>
                        <th data-toggle="tooltip" title="League Type">L. Type</th>
                        <th data-toggle="tooltip" title="Multi Entry">M. Entry</th>
                        <th>Is Running</th>
                        <th>Joined Users</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                <?php
                  if(!empty($getdata)){
                    $i=1;
                    foreach($getdata as $data){
                      $matchkey=$data->matchkey;
                 ?>
                  <tr>
                    <td><span class="font-weight-bold px-2 py-1">{{$i}}</span></td>
                    <td><span class="font-weight-bold">₹{{$data->win_amount}}</span></td>
                    <td><span class="font-weight-bold">{{$data->maximum_user}}</span></td>
                    <td><span class="font-weight-bold">₹{{$data->entryfee}}</span></td>
                    <td><span class="font-weight-bold">{{$data->contest_type}}</span></td>
                    <td>
                        @if($data->confirmed_challenge==1)
                        <span class="font-weight-bold text-success">Confirmed League</span>
                        @else
                        <span class="font-weight-bold text-warning">Not Confirmed</span>
                        @endif
                    </td>
                    <td>@if($data->multi_entry==1) <span class="font-weight-bold text-success px-1">Yes</span> @else <span class="font-weight-bold text-danger px-1">No</span> @endif</td>
                    <td>@if($data->is_running==1) <span class="font-weight-bold text-success px-1">Yes</span> @else <span class="font-weight-bold text-danger px-1">No</span> @endif</td>

                    <td><span class="font-weight-bold text-primary">{{ $data->joinedusers}}<span></td>
                    @if($data->joinedusers)
                        <td>
                            <a href=" {{ action('ContestFullDetailController@allusers',[$data->id,$matchkey])}}"  class="btn btn-sm btn-primary w-35px h-35px"  data-toggle="tooltip" title="View Users"><i class="fas fa-eye"></i></a>
                            @if($data->status!='canceled')
                            <a href=" {{ action('ContestFullDetailController@allwinners',[$data->id,$matchkey])}}"  class="btn btn-sm btn-primary w-35px h-35px"  data-toggle="tooltip" title="View Winners"><i class="fas fa-trophy"></i></a>
                            @endif
                        </td>
                    @else
                        <td></td>
                    @endif
                    </tr>
                <?php $i++;} ?>
                
                </tbody>
                <?php
                }
                else{?>
                 <tr>No data available</tr>
                 <?php }?>
            </table>
        </div>
    </div>
</div>


@endsection('content')
