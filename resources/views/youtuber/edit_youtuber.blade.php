@extends('main')

@section('heading')
    Youtuber Manager
@endsection('heading')

@section('sub-heading')
    Edit Youtuber
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('YoutuberController@view_youtuber') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right" data-toggle="tooltip" title="View All Youtuber"><i class="fad fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')


<div class="card">
    <div class="card-header">Edit Youtuber</div>
    <?php $youtuber_id = $data->id; ?>
      <form class="card-body" action="<?php echo action('YoutuberController@edit_youtuber', $youtuber_id)?>" method="post">
      {{csrf_field()}}

        @include('alert_msg')
        
        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-12">
                        <div class="form-group my-3">
                            <label for="username">Name*</label>
                            <input class="form-control form-control-solid" id="username" type="text" placeholder="Please Enter Username" name="username" required value="{{$data->username}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="email">Email*</label>
                            <input class="form-control form-control-solid" id="email" type="email" placeholder="Please Enter Email" name="email" required value="{{$data->email}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="mobile">Mobile*</label>
                            <input class="form-control form-control-solid" id="mobile" type="text" placeholder="Please Enter Mobile No." name="mobile" required  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                    maxlength="10" pattern="[1-9]{1}[0-9]{9}" value="{{$data->mobile}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="refer_code">Refer Code*</label>
                            <input class="form-control form-control-solid" id="refer_code" type="text" placeholder="Please Enter Refer Code" name="refer_code" required value="{{$data->refer_code}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group my-3">
                            <label for="Percentage">Percentage*</label>
                            <input class="form-control form-control-solid" id="Percentage" type="text" placeholder="Please Enter Percentage" name="percentage" required oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                    maxlength="3" value="{{$data->percentage}}">
                        </div>
                    </div>

                    <div class="col-12 text-right mt-4 mb-2">
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
     </form>
</div>

@endsection('content')
