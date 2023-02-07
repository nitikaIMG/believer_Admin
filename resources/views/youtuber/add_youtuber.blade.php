@extends('main')

@section('heading')
    Youtuber Manager
@endsection('heading')

@section('sub-heading')
    Add New Youtuber
@endsection('sub-heading')

@section('card-heading-btn')
<a  href="<?php echo action('YoutuberController@view_youtuber') ?>" class="btn btn-sm btn-light font-weight-bold text-uppercase text-primary float-right" data-toggle="tooltip" title="View All Youtubers"><i class="fad fa-eye"></i>&nbsp; View</a>
@endsection('card-heading-btn')

@section('content')
<div class="card">
    <div class="card-header">Add Youtuber</div>
      {{ Form::open(array('url' => action('YoutuberController@add_youtuber'), 'method' => 'post','id' => 'j-forms','class'=>'j-forms' ))}}
      {{csrf_field()}}

        @include('alert_msg')

        <div class="sbp-preview">
            <div class="sbp-preview-content">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group my-3">
                          <label  for="first-name">Name*</label>
                          <input name="username" class="form-control form-control-solid" type="text" placeholder="Please Enter name" required="" id='first-name'>
                        </div>
                    </div>
                  <div class="col-md-6">
                        <div class="form-group my-3">
                          <label for="email">Email*</label>
                          <input name="email" class="form-control form-control-solid" type="email" placeholder="Please Enter Email" required="" id='email'>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                          <label for="mobile">Mobile*</label>
                          <input name="mobile" class="form-control form-control-solid" type="text" placeholder="Please Enter Mobile" id="mobile" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" pattern="[1-9]{1}[0-9]{9}" required="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                          <label for="password">Password*</label>
                          <input name="password" class="form-control form-control-solid" id="password" type="text" placeholder="Please Enter Password" required="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                          <label for="refer_code">Refer Code*</label>
                          <input name="refer_code" class="form-control form-control-solid" type="text" placeholder="Please Enter Refer Code" id="refer_code" required="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                          <label for="percentage">Percentage*</label>
                          <input name="percentage" class="form-control form-control-solid" id="percentage" type="text" placeholder="Please Enter Percentage" required="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="3" >
                        </div>
                    </div>
                    <div class="col-12 text-right mt-4 mb-2">
                      <button class="btn btn-sm btn-success text-uppercase"><i class="far fa-check-circle"></i>&nbsp;Submit</button>
                  </div>
                </div>
            </div>
        </div>
    {{ Form::open() }}
</div>
@endsection('content')
