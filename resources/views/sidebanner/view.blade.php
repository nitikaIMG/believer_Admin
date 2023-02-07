@extends('main')

@section('heading')
    Banner Manager
@endsection('heading')

@section('sub-heading')
    View All Banners
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('SidebannerController@sidebanner') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right" data-toggle="tooltip" title="Add New Banner"><i class="fa fa-plus"></i>&nbsp; Add</a>
@endsection('card-heading-btn')

@section('content')



<div class="card mb-4">
    <div class="card-header">View All Banners</div>
    <div class="card-body">
        <div class="datatable table-responsive">
            <table class="table table-bordered table-striped table-hover text-center text-nowrap" id="datatabledd" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Type</th>
                        <th>Image</th>
                        <th>Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Sno.</th>
                        <th>Type</th>
                        <th>Image</th>
                        <th>Link</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';


        $('#datatabledd').DataTable({
             "processing": true,
             "sAjaxSource":'<?php echo asset('my-admin/view_sidebanner_table');?>?',
             "dom": 'lBfrtip',
             "buttons": [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ]
        });
            $("#datatabledd_filter").hide();

});
</script>

@endsection('content')
