@extends('main')

@section('heading')
    Result Manager
@endsection('heading')

@section('sub-heading')
    Series Listing - All Series
@endsection('sub-heading')

@section('content')

@include('alert_msg')



<div class="card mb-4">
    <div class="card-header">Series Listing - All Series</div>
    <div class="card-body">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        <th class="text-center">No. Of Matches</th>
                        <th class="text-center">Winner Declare Require</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Series Name</th>
                        <th class="text-center">No. Of Matches</th>
                        <th class="text-center">Winner Declare Require</th>
                        <th class="text-center">Date</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                      $s_no=1;
                      foreach($findalllistmatches as $value){
                    ?>
                    <tr>
                      <td><?php echo $s_no;?></td>
                      <td class="sorting_1"><u><a class="text-danger font-weight-600" href="<?php echo action('ResultController@match_detail',$value->series_id)?>"><?php echo $value->series_name; ?></a></u></td>
                      <?php
                          $totalmatch = DB::table('listmatches')->where('series',$value->series_id)->count();
                        ?>
                      <td class="text-center"><?php echo $totalmatch; ?></td>
                      <td class="text-center">
                      <?php
                        $findalllistmatches = DB::table('listmatches')->join('matchchallenges','matchchallenges.matchkey','=','listmatches.matchkey')->where('listmatches.series',$value->series_id)->where('listmatches.final_status','!=','winnerdeclared')->select('listmatches.id as listmatches_id')->groupBy('matchchallenges.matchkey')->get();
                        echo count($findalllistmatches);
                      ?>
                      </td>
                      <td class="text-center">
                      <?php
                        echo ' <span class="font-weight-600 px-2"> From </span> <span class="text-warning"> '.date('d/m/y',strtotime($value->created_at)).'</span>&nbsp; <span class="text-success"> '.date(' h:i:s a',strtotime($value->created_at)).'</span> <span class="font-weight-600 px-2"> to </span> <span class="text-warning"> '.date('d/m/y',strtotime($value->end_date)). '</span>&nbsp; <span class="text-success"> '.date(' h:i:s a',strtotime($value->end_date)). '</span>';
                      ?>
                      </td>
                    </tr>
                    <?php $s_no++;}?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
$.ajaxSetup({
headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});
});

function muldelete() {
var p=[];
$.each($("input[name='checkCat']:checked"), function(){
p.push($(this).val());
});

if(p!=""){
var datavar = '_token=<?php echo csrf_token();?>&hg_cart='+p;
var ok=confirm('Are you you want to delete this data');
if(ok){
$.ajax({
          type:'POST',
          url:'<?php echo asset('my-admin/muldelete');?>',
          data:datavar,
success:function(data){
if(data==1){
window.location.reload();
}
          }
       });
}
}
else{
Swal.fire('Please Select Series to delete');
}
}
</script>
@endsection('content')
