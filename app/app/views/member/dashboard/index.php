<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var od = <?= $overview_data ?>;
</script>
<?php if ($pass_expired and $this->user->pass_expiry_notice =='true') { ?>
    <div class="row">
        <div class="col">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="spinner-grow text-danger" role="status"></div>
                <h4 class="alert-heading">Password Reset Notice</h4>
                <p>Your password is due for change. Kindly proceed to change your password for security reason</p>
                <a data-fancy href="<?= base_url('member/profile/security') ?>" class="btn btn-danger float-right"><?= lang('change_pass') ?></a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- end row -->
<div class="row">
    <div class="col-xl-5 col-lg-6">
        <div class="row">
            <div class="col-lg-6">
                <div class="card cta-box bg-primary text-white">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="mdi mdi-wallet widget-icon bg-light-lighten text-white"></i>
                        </div>
                        <h5 class="text-white font-weight-normal mt-0" title="<?= lang('wallet_bal') ?>"><?= lang('wallet_bal') ?></h5>
                        <h3 class="mt-3 mb-3"><?= $this->country->currency ?> <?= number_format($wallet_bal, 2) ?></h3>
                        <p class="mb-0 text-white">
                            <!--<span class="text-nowrap"><?= lang('wallet_bal') ?></span>-->
                            <a data-fancy href="<?= base_url('member/wallet/load_wallet') ?>" class="float-right mr-2 btn btn-primary btn-sm shadow"><i class="mdi mdi-dots-vertical mr-2"></i> <?= lang('load') . ' ' . lang('wallet') ?></a>
                        </p>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card widget-flat cta-box bg-dark">
                    <div class="card-body ">
                        <div class="float-right">
                            <i class="mdi mdi-cart-plus widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-success font-weight-normal mt-0" title="<?= lang('savings') ?>"><?= lang('savings') ?></h5>
                        <h3 class="mt-3 mb-3 text-success"><?= $this->country->currency ?> <?= number_format($savings_bal, 2) ?></h3>
                        <p class="mb-0 text-muted">
                            <a data-fancy href="<?= base_url('member/savings/add') ?>" class="mr-2 btn btn-outline-success btn-sm shadow float-right"><i class="mdi mdi-plus mr-2"></i><?= lang('add_savings') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card widget-flat bg-gen">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="mdi mdi-briefcase widget-icon bg-primary-lighten text-primary"></i>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('loans') ?>"><?= lang('loans') ?></h5>
                        <h3 class="mt-3 mb-3"><?= $this->country->currency ?> <?= number_format($loan_bal, 2) ?></h3>
                        <p class="mb-0 text-muted">
                            <a data-fancy href="<?= base_url('member/loan/request') ?>" class="mr-1 btn btn-outline-primary btn-sm shadow"><i class="mdi mdi-dots-vertical mr-1"></i><?= lang('request_loan') ?></a>
                            <a data-fancy href="<?= base_url('member/loan') ?>" class=" btn btn-primary btn-sm shadow"><i class="mdi mdi-dots-vertical mr-1"></i><?= lang('repay_loan') ?></a>
                        </p>
                    </div>
                </div>
                <div class="card widget-flat bg-gen">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="mdi mdi-cart widget-icon bg-primary-lighten text-primary"></i>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('credit_sales') ?>"><?= lang('credit_sales') ?></h5>
                        <h3 class="mt-3 mb-3"><?= $this->country->currency ?> <?= number_format($credit_sales_bal, 2) ?></h3>
                        <p class="mb-0 text-muted">
                            <a data-fancy href="<?= base_url('member/products') ?>" class="mr-1 btn btn-outline-primary btn-sm shadow"><i class="mdi mdi-dots-vertical mr-1"></i><?= lang('request') ?></a>
                            <a data-fancy href="<?= base_url('member/creditsales') ?>" class=" btn btn-primary btn-sm shadow"><i class="mdi mdi-dots-vertical mr-1"></i><?= lang('repay_credit_sales') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-7  col-lg-6">
        <div class="card bg-gen">
            <div class="card-body">
                <h4 class="header-title"><?= lang('acc_overview') ?></h4>
                <div id="gradient-donut" class="apex-charts" data-colors="#35b8e0,#10c469,#ff5b5b,#323A46"></div>
                <hr>
                <h4 class="mt-3" title="<?= lang('credit_rating') ?>"><?= lang('credit_rating') ?></h5>
                    <div class="rateit rateit-mdi" data-rateit-mode="font" data-rateit-icon="" data-rateit-value="<?= $credit_worthines?>" data-rateit-ispreset="true" data-rateit-readonly="true">
                    </div>
                    <p class="">Improve your credit ratings by being punctual in your savings and loan repayment. This will also speed up your loan processing
                        whenever you requested for a loan</p>
            </div>
        </div>

    </div>