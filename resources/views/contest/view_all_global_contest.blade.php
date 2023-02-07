@extends('main')

@section('heading')
    Contest Manager
@endsection('heading')

@section('sub-heading')
    View all global contests
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('ContestController@create_global') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary text-uppercase" data-toggle="tooltip" title="Add New Global Contest"><i class="fa fa-plus"></i>&nbsp; Add</a>
@endsection('card-heading-btn')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <div class="row w-100 align-items-center mx-0">
            <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">Global Contest</div>
            <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                <button type="submit" class="btn btn-sm btn-danger text-uppercase rounded-pill" id="check" onclick="muldelete()" font-weight-600 disabled><i class="fad fa-trash-alt"></i>&nbsp; Delete</a></button>
            </div>
        </div>
    </div>
    <div class="card-body">
        
        @include('alert_msg')
        
        <div class="datatable table-responsive">
            <table class="table table-bordered table-hover text-nowrap w-100" id="global_index_datatable" cellspacing="0">
                <thead>
                <tr>

              <th class="my_tabl" ><div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input sel_all" id="select_all">
                <label class="custom-control-label" for="select_all"></label>
              </div></th>
                    <th data-toggle="tooltip" title="S. No.">#</th>
                    <th data-toggle="tooltip" title="Fantasy Type">Fantasy Type</th>
                    <th data-toggle="tooltip" title="Contest Category">C. C.</th>
                    <th data-toggle="tooltip" title="Entry Fee">E. Fee</th>
                    <th data-toggle="tooltip" title="Winning  Amount">W. Amt.</th>
                    <th data-toggle="tooltip" title="Max  Users">M. Users</th>
                    <th data-toggle="tooltip" title="Multi  Entry">M. Entry</th>
                    <th data-toggle="tooltip" title="Running">R.</th>
                    <th data-toggle="tooltip" title="Confirm">C.</th>
                    <th data-toggle="tooltip" title="Bonus">B.</th>
                    <th data-toggle="tooltip" title="C Type"> C. T.</th>
                    <th data-toggle="tooltip" title="Price  Card">P. Card</th>
                    <th data-toggle="tooltip" title="Action">Action</th>
                </tr>
                </thead>
             <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="my_tabl" ><div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input sel_all" id="select_all2">
                          <label class="custom-control-label" for="select_all2"></label>
                        </div></th>
                        <th data-toggle="tooltip" title="S. No.">#</th>
                        <th data-toggle="tooltip" title="Fantasy Type">Fantasy Type</th>
                        <th data-toggle="tooltip" title="Contest Category">C. C.</th>
                        <th data-toggle="tooltip" title="Entry Fee">E. Fee</th>
                        <th data-toggle="tooltip" title="Winning  Amount">W. Amt.</th>
                        <th data-toggle="tooltip" title="Max  Users">M. Users</th>
                        <th data-toggle="tooltip" title="Multi  Entry">M. Entry</th>
                        <th data-toggle="tooltip" title="Running">R.</th>
                        <th data-toggle="tooltip" title="Confirm">C.</th>
                        <th data-toggle="tooltip" title="Bonus">B.</th>
                        <th data-toggle="tooltip" title="C Type"> C. T.</th>
                        <th data-toggle="tooltip" title="Price  Card">P. Card</th>
                        <th data-toggle="tooltip" title="Action">Action</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var fantasy_type = $('#fantasy_type').val();
    $.fn.dataTable.ext.errMode = 'none';
        $('#global_index_datatable').DataTable({
            'bFilter':false,
        "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": '<?php echo URL::asset('my-admin/global_index_datatable');?>',
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}",
                                fantasy_type: fantasy_type
                     }
                   },
            "columns": [
                { "data": "s_no",orderable:false },
                { "data": "id" },
                { "data": "fantasy_type" },
                { "data": "cat" },
                { "data": "entryfee" },
                { "data": "win_amount" },
                { "data": "maximum_user" },
                { "data": "multi_entry" },
                { "data": "is_running" },
                { "data": "confirmed_challenge" },
                { "data": "is_bonus" },
                { "data": "contest_type" },
                { "data": "edit" },
                { "data": "action" }
            ]

        });

});
</script>

<script type="text/javascript">
    $(document).ready(function(){
$('#select_all, #select_all2').on('click',function(){
    if(this.checked){
        $('.checkbox').each(function(){
            this.checked = true;
        });
    }else{
         $('.checkbox').each(function(){
            this.checked = false;
        });
    }
});

$('.checkbox').on('click',function(){
    if($('.checkbox:checked').length == $('.checkbox').length){
        $('#select_all, #select_all2').prop('checked',true);
    }else{
        $('#select_all, #select_all2').prop('checked',false);
    }
});
});
</script>
<script>
    function muldelete() {
      var p=[];
      $.each($("input[name='checkCat']:checked"), function(){
      p.push($(this).val());
      });
      if(p!=""){
        var datavar = '_token=<?php echo csrf_token();?>&hg_cart='+p;

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

            $.ajax({
                  type:'POST',
                  url:'<?php echo asset('my-admin/globalcat_muldelete');?>',
                  data:datavar,
                success:function(data){
                    if(data==1){
                        
                        swalWithBootstrapButtons.fire(
                        'Deleted!',
                        'Contest deleted successfully.',
                        'success'
                        )

                        window.location.reload();
                    
                    }
                }
            });
            
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
            'Cancelled',
            'Cancelled successfully :)',
            'error'
            )
        }
        });

        }
      else{
        Swal.fire('Please select atlease one contest to delete');
        }
    }
</script>


<script>
    function change_fantasy() {
        $('#change_fantasy').submit();
    }
</script>

<script>
    $(document).on('click', 'input[name="checkCat"], #select_all, #select_all2', function() {
        var total_checked_checkbox = $('input[name="checkCat"]:checked').length;

        if(total_checked_checkbox > 0) {
            $('#check').prop('disabled', false);
        } else {            
            $('#check').prop('disabled', true);
        }
    });
</script>

@endsection('content')
