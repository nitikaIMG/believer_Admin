@extends('main')
@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
    <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-12">
        <h4 class="page-title">Popup Manager</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ action('DashboardController@index') }}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="javaScript:void();">Popup Manager</a></li>
            <li class="breadcrumb-item active" aria-current="page">View All Popup</li>
         </ol>
     </div>
     </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
           <div class="card-body">
             <a  href="<?php echo action('PopupController@add_popup') ?>" class="btn btn-info"  style="float: right;"><i class="fa fa-plus"></i> Add Popup</a>
           </div>
         </div>
       </div>
     </div>
   
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
           <div class="card-body">
             <div class="card-title">View All Popup</div>
             <hr>
             <div style="text-align:center">
               @if ($message = Session::get('success'))
                   <div class="alert alert-success">
                       <p>{{ $message }}</p>
                   </div>
               @elseif($message = Session::get('warning'))
                   <div class="alert alert-warning">
                       <p>{{ $message }}</p>
                   </div>
               @elseif($message = Session::get('danger'))
                   <div class="alert alert-danger">
                       <p>{{ $message }}</p>
                   </div>
               @endif      
            </div>
            <table id="view_popup_notification" class="table table-striped table-bordered dataTable no-footer dtr-inline" cellspacing="0" width="100%" role="grid" aria-describedby="demo-dt-basic_info" style="width: 100%;">
                <thead>
                <tr>
                  <div class="fff"></div>
                  <th class="myclass">Sno.</th>
                  <th>Title</th>
                  <th class="myclass2">image</th>
                  <th class="myclass2">Action</th>
                </tr>
                </thead>
                <tbody>
                  
                </tbody>
          </table>
           </div>
         </div>
        </div>
      </div>
      
   </div>
   </div>



    <script type="text/javascript">
            $(document).ready(function() {  
            $.fn.dataTable.ext.errMode = 'none';
                $('#view_popup_notification').DataTable({
              "processing": true,
                  "serverSide": true,
                    "ajax":{
                             "url": '<?php echo URL::asset('/my-admin/view_popup_notification');?>',
                             "dataType": "json",
                             "type": "POST",
                             "data":{ _token: "{{csrf_token()}}"}
                           },
                           'bFilter': false,
                    "columns": [
                        { "data": "s_no" },
                        { "data": "title" },
                        { "data": "image" },
                        { "data": "action" }
                    ]  
        
                });
                
        });
    </script>

@endsection