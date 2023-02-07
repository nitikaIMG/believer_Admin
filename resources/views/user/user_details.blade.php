@extends('main')

@section('heading')
User Manager
@endsection('heading')

@section('sub-heading')
User Details
@endsection('sub-heading')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">User Details</div>
            <div class="card-body">
                <div class="sbp-preview position-relative">

                    <div class="row mx-0">
                            <?php if (!empty($userdata)) { ?>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15">
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">User Name -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->username ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Email -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->email; ?></div>
                                </div>

                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Mobile -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->mobile ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">DOB -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->dob ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Gender -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->gender ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Address -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->address ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">City -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->city ?></div>
                                </div>
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">State -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->state ?></div>
                                </div>
                            </div>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15">
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Pincode -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->pincode ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Team -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $userdata->team ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Status -</div>
                                    <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase">
                                        <?php
                                        if ($userdata->status == 'activated')
                                            echo '<font class="text-success"><i class="far fa-check-circle"></i> ' . ucwords($userdata->status) . '</font>';
                                        else
                                            echo '<font class="text-danger"><i class="far fa-times-circle"></i> ' . ucwords($userdata->status) . '</font>';
                                        ?>
                                    </div>
                                </div>
                                <div class="row align-items-center pb-1 pt-4">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Image -</div>
                                    <div class="col-auto fs-md-14 fs-13">
                                        <?php
                                        $ext = pathinfo($userdata->image, PATHINFO_EXTENSION);
                                        if ($ext == 'pdf') {
                                        ?>
                                            <i class="fad fa-file-pdf"></i>
                                        <?php
                                        } else {

                                        ?>
                                            <?php
                                            if (!empty($userdata->image)) {
                                                $img = $userdata->image;
                                            ?>
                                                <a href="<?php echo $img; ?>" target="_blank" class="w-100px h-100px d-block shadow rounded-15" style="background: url(<?php echo $img; ?>);background-size: cover;" id="a_tag"></a>
                                            <?php } else { ?>
                                                <a href="<?php echo asset('public/logo.png') ?>" target="_blank" class="w-100px h-100px d-block shadow rounded-15" style="background: url('<?php echo asset('public/logo.png') ?>');background-size: cover;"></a>
                                        <?php }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                                <div>
                                    <div class="col fs-md-14 fs-13 text-dark font-weight-bold">No Data Available</div>
                                </div>
                            <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row my-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">Pan Card Details</div>
            <div class="card-body">
                <div class="sbp-preview position-relative">

                    <div class="row mx-0">
                            <?php if (!empty($pancard)) { ?>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15">
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Pan Name -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $pancard->pan_name ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Pan No. -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $pancard->pan_number ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Pan D.O.B. -</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $pancard->pan_dob ?></div>
                                </div>
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Pan Status -</div>
                                    <?php if ($pancard->status == 1) { ?>
                                        <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase"><?php echo '<font class="text-success"><i class="far fa-check-circle"></i> Verified</font>'; ?></div>
                                    <?php
                                    } else if ($pancard->status == 0) { ?>
                                        <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase"><?php echo '<font class="text-warning"><i class="far fa-exclamation-circle"></i> Pending</font>'; ?></div>
                                    <?php } else if ($pancard->status == 2) { ?>
                                        <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase"><?php echo '<font class="text-danger"><i class="far fa-exclamation-triangle"></i> Warning</font>'; ?></div>
                                    <?php }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15">
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Pan Image -</div>
                                    <div class="col-auto fs-md-14 fs-13"><a href="<?php echo asset('public/uploads/pancard/' . $pancard->image); ?>" target="_blank" class="w-100px h-100px d-block shadow rounded-15" style="background: url(<?php echo asset('public/uploads/pancard/' . $pancard->image); ?>);background-size: cover;"></a></div>
                                </div>
                            </div>
                            <?php } else { ?>
                                <div class="row align-items-center py-1">
                                    <div class="col-12 fs-md-14 fs-13">No Data Available</div>
                                </div>
                            <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row my-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">Bank Details</div>
            <div class="card-body">
                <div class="sbp-preview position-relative">

                    <div class="row mx-0">
                            <?php if (!empty($bank)) { ?>
                        <div class="col-md col-12 shadow m-3 p-3 rounded-15">
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Account no.</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $bank->accno ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Account Holder name</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $bank->accountholder ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">IFSC</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $bank->ifsc ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Bank Name</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $bank->bankname ?></div>
                                </div>
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Bank Branch</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $bank->bankbranch ?></div>
                                </div>
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">State</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $bank->state ?></div>
                                </div>
                        </div>
                        <div class="col-md col-12 shadow m-3 p-3 rounded-15">
                                <div class="row align-items-center border-bottom py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Bank Status</div>
                                    <?php if ($bank->status == 1) { ?>
                                        <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase"><?php echo '<font class="text-success"><i class="far fa-check-circle"></i> Verified</font>'; ?></div>
                                    <?php
                                    } else if ($bank->status == 0) { ?>
                                        <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase"><?php echo '<font class="text-warning"><i class="far fa-exclamation-circle"></i> Pending</font>'; ?></div>
                                    <?php } else if ($bank->status == 2) { ?>
                                        <div class="col-auto fs-md-14 fs-13 font-weight-900 text-uppercase"><?php echo '<font class="text-danger"><i class="far fa-exclamation-triangle"></i> Warning</font>'; ?></div>
                                    <?php }
                                    ?>
                                </div>
                                <div class="row align-items-center pb-1 pt-4">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Bank Image</div>
                                    <div class="col-auto fs-md-14 fs-13"><a href="<?php echo asset('public/uploads/bank/' . $bank->image); ?>" target="_blank" class="w-100px h-100px d-block shadow rounded-15" style="background: url(<?php echo asset('public/uploads/bank/' . $bank->image); ?>);background-size: cover;"></a></div>
                                </div>
                            </div>
                            <?php } else { ?>
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">No Data Available</div>
                                </div>
                            <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row my-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">User Transactions</div>
            <div class="card-body">
                <div class="sbp-preview position-relative">

                    <div class="row mx-0">
                            <?php
                            if (!empty($transaction)) {
                            ?>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15 justify-content-between">
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Deposited</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $transaction->balance ?></div>
                                </div>
                            </div>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15 justify-content-between">
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Winning Amount</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $transaction->winning ?></div>
                                </div>
                            </div>
                            <div class="col-md col-12 shadow m-3 p-3 rounded-15 justify-content-between">
                                <div class="row align-items-center py-1">
                                    <div class="col fs-md-13 fs-12 text-dark text-uppercase font-weight-900">Bonus Amount</div>
                                    <div class="col-auto fs-md-14 fs-13"><?php echo $transaction->bonus ?></div>
                                </div>
                            </div>
                            <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection('content')