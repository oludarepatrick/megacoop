<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var od = <?= $overview_data ?>;
</script>
<?php if ($pass_expired) { ?>
    <div class="row justify-content-center">
        <div class="col-lg-8">
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

<div class="row justify-content-center">
    <div class="col-lg-4">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-right">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('wallet_bal') ?>"><?= lang('wallet_bal') ?></h5>
                <h3 class="mt-3 mb-3"><?= number_format($wallet_bal, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-right">
                    <i class="mdi mdi-cart-plus widget-icon bg-success-lighten text-success"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('savings') ?>"><?= lang('savings') ?></h5>
                <h3 class="mt-3 mb-3"><?= number_format($total_savings->amount, 2) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="row">
            <div class="col">
                <a class="dropdown-icon-item bg-white rounded shadow-sm" data-fancy href="<?= base_url('agency/registration/add_member') ?>">
                    <i class="widget-icon mdi mdi-account-plus"></i>
                    <span><?= lang('add_member') ?></span>
                </a>
            </div>
            <div class="col">
                <a class="dropdown-icon-item bg-white rounded shadow-sm" data-fancy href="<?= base_url('agency/wallet') ?>">
                    <i class="widget-icon mdi mdi-chart-bar"></i>
                    <span>Fund Wallet</span>
                </a>
            </div>
            <div class="col">
                <a class="dropdown-icon-item bg-white rounded shadow-sm" data-fancy href="<?= base_url('agency/savings/add') ?>">
                    <i class="widget-icon  mdi mdi-wallet-plus"></i>
                    <span><?= lang('add_savings') ?></span>
                </a>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <a class="dropdown-icon-item bg-white rounded shadow-sm" data-fancy href="<?= base_url('agency/savings') ?>">
                    <i class="widget-icon mdi mdi-wallet"></i>
                    <span><?= lang('savings') ?> History</span>
                </a>
            </div>

            <div class="col">
                <a class="dropdown-icon-item bg-white rounded shadow-sm" data-fancy href="<?= base_url('agency/registration/') ?>">
                    <i class="widget-icon  mdi mdi-account-multiple-check"></i>
                    <span><?= lang('members') ?></span>
                </a>
            </div>

            <div class="col">
                <a class="dropdown-icon-item bg-white rounded shadow-sm" data-fancy href="<?= base_url('agency/loan') ?>">
                    <i class="widget-icon mdi mdi-account"></i>
                    <span><?= lang('loan') ?></span>
                </a>
            </div>
        </div>
    </div>
</div>