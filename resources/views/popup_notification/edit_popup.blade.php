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
            <li class="breadcrumb-item active" aria-current="page">Edit Popup</li>
         </ol>
     </div>
     </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
           <div class="card-body">
            <a  href="<?php echo action('PopupController@popup') ?>" class="btn btn-info"  style="float: right;"><i class="fa fa-eye"></i> View All Popup</a>
           </div>
         </div> 
       </div>
     </div>
   
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
           <div class="card-body">
           <div class="card-title">Edit Popup</div>
           <hr>
             <form enctype="multipart/form-data" class="form-horizontal form-label-left" action="<?php echo action('PopupController@edit_popup_notification',$sidebanner->id)?>" method="post">
                {{csrf_field()}}
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
            
                        @if ($errors->any())
                           <div class="alert alert-danger">
                               <ul>
                                   @foreach ($errors->all() as $error)
                                       <li>{{ $error }}</li>
                                   @endforeach
                               </ul>
                           </div>
                        @endif
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                          <label for="title" class="">Title</label>
                          <input name="title" id="title" placeholder="Enter Title" value="{{$sidebanner->title}}" type="text" class="form-control" required>
                        </div>
                    </div>
                </div>
           <div class="form-group">
           {{ Form::label('Image*','Image*',array('class'=>'control-label text-bold'))}}<br>
           <img src="{{asset('public/popup_notify/'.$sidebanner->image)}}" style="width: 130px; height: 110px;" onerror="this.src='{{ asset('public/'.auth()->user()->settings()->logo ?? '')}}'"><br><br>
           <input name="image" class="" type="file">
           </div>
           <div class="form-group">
            <button type="submit" class=" btn text-white px-5" style="margin-left:7px;background: #1e347e;"><i class="fa fa-sign-in" ></i>&nbsp;   Update</button>
          </div>
          </form>
         </div>
         </div>
      </div>
    </div>
   </div>
    </div>
   </div>
    </div>
  </div>
</div>
@endsection