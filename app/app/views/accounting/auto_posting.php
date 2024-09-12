<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">

    <div class=" col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Enable <?= lang('auto_posting') ?></h4>
                    <p>This section allow you to configure savings, withdrawal, loan disbursement (principal and
                        interest)
                        ,loan repayment (principal and interest), credit sales (principal and interest),
                        credit sales repayment (principal and interest) to automatically report in to
                        general ledger(GL) for proper accounting.
                    </p>
                    <ul>
                        <li>Before you enable auto posting, click on each of the item below to configure it
                            appropriately</li>
                        <li>You must have configured your cooperative Chart of Account before proceeding with this</li>
                    </ul>
                    <hr>
                    <?php if ($this->coop->ledger_auto_post == 'true') { ?>
                        <input type="checkbox" id="switch1" onclick="ajax_enable_auto_posting('true', this)" checked data-switch="bool" />
                    <?php } else { ?>
                        <input type="checkbox" id="switch1" onclick="ajax_enable_auto_posting('false', this)" data-switch="bool" />
                    <?php } ?>
                    <label for="switch1" data-on-label="Yes" data-off-label="No"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <a data-fancy href="<?= base_url('accounting/auto_post_options/savings') ?>" class="btn text-left">
                        <div class="card-body">
                            <div class="media">
                                <span class="d-flex align-self-start rounded mr-3"><i class="mdi mdi-post widget-icon"></i> </span>
                                <div class="media-body">
                                    <div>
                                        <h5 class="mt-0 float-left ">Savings</h5>
                                        <?= $savings_tracker ? '<span class="float-right badge-primary-lighten badge">configured</span>' : '<span class="float-right badge-danger-lighten badge">not configured</span>' ?>
                                        <span class="clearfix"></span>
                                    </div>
                                    <p class="mb-0">Set automatic posting of member savings to general ledgal</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class=" col-md-6">
                <div class="card">
                    <a data-fancy href="<?= base_url('accounting/auto_post_options/withdrawal') ?>" class="btn text-left">
                        <div class="card-body">
                            <div class="media">
                                <span class="d-flex align-self-start rounded mr-3"><i class="mdi mdi-download widget-icon bg-primary-lighten text-primary"></i> </span>
                                <div class="media-body">
                                    <div>
                                        <h5 class="mt-0 float-left ">Withdrawals</h5>
                                        <?= $withdrawal_tracker ? '<span class="float-right badge-primary-lighten badge">configured</span>' : '<span class="float-right badge-danger-lighten badge">not configured</span>' ?>
                                        <span class="clearfix"></span>
                                    </div>
                                    <p class="mb-0">Set automatic posting of member withdrawal to general ledgal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <a data-fancy href="<?= base_url('accounting/auto_post_options/loan') ?>" class="btn text-left">
                        <div class="card-body">
                            <div class="media">
                                <span class="d-flex align-self-start rounded mr-3"><i class="mdi mdi-briefcase widget-icon bg-primary-lighten text-primary"></i> </span>
                                <div class="media-body">
                                    <div>
                                        <h5 class="mt-0 float-left ">Loan</h5>
                                        <?= $loan_tracker ? '<span class="float-right badge-primary-lighten badge">configured</span>' : '<span class="float-right badge-danger-lighten badge">not configured</span>' ?>
                                        <span class="clearfix"></span>
                                    </div>
                                    <p class="mb-0">Set automatic posting of member loan in to general ledgal</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <a data-fancy href="<?= base_url('accounting/auto_post_options/loan_repayment') ?>" class="btn text-left">
                        <div class="card-body">
                            <div class="media">
                                <span class="d-flex align-self-start rounded mr-3"><i class="mdi mdi-briefcase-minus widget-icon "></i> </span>
                                <div class="media-body">
                                    <div>
                                        <h5 class="mt-0 float-left ">Loan Repayment</h5>
                                        <?= $loan_repayment_tracker ? '<span class="float-right badge-primary-lighten badge">configured</span>' : '<span class="float-right badge-danger-lighten badge">not configured</span>' ?>
                                        <span class="clearfix"></span>
                                    </div>
                                    <p class="mb-0">Set automatic posting of member loan repayment in to general ledgal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <a data-fancy href="<?= base_url('accounting/auto_post_options/credit_sales') ?>" class="btn text-left">
                        <div class="card-body">
                            <div class="media">
                                <span class="d-flex align-self-start rounded mr-3"><i class="mdi mdi-cart widget-icon bg-primary-lighten text-primary"></i> </span>
                                <div class="media-body">
                                    <div>
                                        <h5 class="mt-0 float-left ">Credit Sales</h5>
                                        <?= $credit_sales_tracker ? '<span class="float-right badge-primary-lighten badge">configured</span>' : '<span class="float-right badge-danger-lighten badge">not configured</span>' ?>
                                        <span class="clearfix"></span>
                                    </div>
                                    <p class="mb-0">Set automatic posting of member credit sales in to general ledgal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <a data-fancy href="<?= base_url('accounting/auto_post_options/credit_sales_repayment') ?>" class="btn text-left">
                        <div class="card-body">
                            <div class="media">
                                <span class="d-flex align-self-start rounded mr-3"><i class="mdi mdi-cart-minus widget-icon "></i> </span>
                                <div class="media-body">
                                    <div>
                                        <h5 class="mt-0 float-left ">Credit Sales Repayment</h5>
                                        <?= $credit_sales_repayment_tracker ? '<span class="float-right badge-primary-lighten badge">configured</span>' : '<span class="float-right badge-danger-lighten badge">not configured</span>' ?>
                                        <span class="clearfix"></span>
                                    </div>
                                    <p class="mb-0">Set automatic posting of member credit sales repayment in to general
                                        ledgal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>