<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var overview_data = <?= $overview_data ?>;
    var cl = <?= $comparison_liquidity ?>;
    var cs = <?= $comparison_savings ?>;
    var sd = <?= $savings_data ?>;
    var wd = <?= $withdrawal_data ?>;
    var ld = <?= $loan_data ?>;
</script>
<div class="row">
    <div class="col-xl-4 col-lg-4">
        <div class="card bg-gen">
            <div class="card-body">
                <h4 class="header-title"><?= lang('coop_overview') ?></h4>

                <div id="average-sales" class="apex-charts mb-4 mt-4" data-colors="#536de6,#10c469,#ff5b5b,#f9c851,#323A46,#35B8E0"></div>

                <div class="chart-widget-list">
                    <p class="mb-2">
                        <i class="mdi mdi-square text-primary"></i> <?= lang('savings') ?>
                        <span class="float-right"> <?= $this->country->currency ?> <?= number_format($savings_bal, 2) ?></span>
                    </p>
                    <p class="mb-1">
                        <i class="mdi mdi-square text-success"></i> <?= lang('loan') ?> <?= lang('interest') ?>
                        <span class="float-right"> <?= $this->country->currency ?> <?= number_format($loan_interest, 2) ?></span>
                    </p>
                    <p class="mb-1">
                        <i class="mdi mdi-square text-warning"></i> <?= lang('credit_sales') ?> <?= lang('interest') ?>
                        <span class="float-right"> <?= $this->country->currency ?> <?= number_format($credit_sales_interest, 2) ?></span>
                    </p>
                    <p class="mb-1">
                        <i class="mdi mdi-square text-danger"></i> <?= lang('reg_fee') ?>
                        <span class="float-right"> <?= $this->country->currency ?> <?= number_format($reg_fee, 2) ?></span>
                    </p>
                    <p class="mb-1">
                        <i class="mdi mdi-square text-dark"></i> <?= lang('loan') ?>
                        <span class="float-right"> <?= $this->country->currency ?> <?= number_format($loan_bal, 2) ?></span>
                    </p>
                    <p class="mb-2">
                        <i class="mdi mdi-square text-info"></i> <?= lang('credit_sales') ?>
                        <span class="float-right"> <?= $this->country->currency ?> <?= number_format($credit_sales_bal, 2) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-8">
        <div class="card bg-gen">
            <div class="card-body">
                <h4 class="header-title mb-3"><?= lang('comparison') . ' ' . $year ?></h4>
                <div class="chart-content-bg">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <p class="text-muted mb-0 mt-3"><?= lang('saving_bal') ?></p>
                            <h2 class="font-weight-normal mb-3">
                                <small class="mdi mdi-checkbox-blank-circle text-primary align-middle mr-1"></small>
                                <span> <?= $this->country->currency ?> <?= number_format($savings_bal, 2) ?></span>
                            </h2>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-0 mt-3"><?= lang('liquidity') ?></p>
                            <h2 class="font-weight-normal mb-3">
                                <small class="mdi mdi-checkbox-blank-circle text-success align-middle mr-1"></small>
                                <span><?= $this->country->currency ?> <?= number_format($liquidity, 2) ?></span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="dash-item-overlay d-none d-md-block ">
                    <h5>Monthly Savings balance and Liquidity:</h5>
                    <p class="text-muted font-13 mb-3 mt-2">based on <b class="text-danger">Savings Balance, Registration Fee
                            Loan Interest, and Credit Sales Interest</b> against <b class="text-danger">Disbursed loans and Supplied Credit Sales</b></p>
                    <a href="javascript: void(0);" class="btn btn-outline-primary" data-toggle="modal" data-target="#centermodal">View By Year
                        <i class="mdi mdi-arrow-right ml-2"></i>
                    </a>
                </div>

                <div id="revenue-chart" class="apex-charts mt-3" data-colors="#536de6,#10c469"></div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-5 col-lg-6">

        <div class="row">
            <div class="col-lg-6">
                <div class="card widget-flat bg-gen">
                    <div class="card-body">
                        <div class="float-right">
                            <a data-fancy href="<?= base_url('registration') ?>" class="mr-2 btn btn-outline-primary btn-sm shadow-sm "><i class="mdi mdi-dots-vertical mr-2"></i><?= lang('more') ?></a>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('members') ?>"><?= lang('members') ?></h5>
                        <h3 class="mt-3 mb-3"><?= $total_member ?></h3>
                        <p class="mb-2 text-muted">
                            <span class=" float-left">
                                <i class="mdi mdi-account-tie bg-success-lighten text-success font-22 px-1 mr-1 rounded"></i> <?= $male ?>
                            </span>
                            <span class=" float-right">
                                <i class="mdi mdi-face-woman-outline bg-primary-lighten text-primary font-22  px-1 mr-1 rounded"></i> <?= $female ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card widget-flat bg-gen">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="mdi mdi-cart-plus widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('savings') ?>"><?= lang('savings') ?></h5>
                        <small class="font-12 mb-0 badge-primary-lighten badge"><?= $this->country->currency ?></small>
                        <h3 class="mt-0 mb-3">

                            <?= number_format($savings_bal, 2) ?>
                        </h3>
                        <p class="mb-0 text-muted">
                            <a href="javascript:savings_bal()" class="mr-2 btn btn-outline-success btn-sm shadow-sm "><i class="mdi mdi-dots-vertical mr-2"></i><?= lang('more') ?></a>
                            <span class="text-nowrap"><?= lang('total_savings') ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card widget-flat bg-gen">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="mdi mdi-currency-usd widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('loans') ?>"><?= lang('loans') ?></h5>
                        <small class="font-12 mb-0 badge-primary-lighten badge"><?= $this->country->currency ?></small>
                        <h3 class="mt-0 mb-3"><?= number_format($loan_bal, 2) ?></h3>
                        <p class="mb-0 text-muted">
                            <a href="javascript:loan_bal()" class="mr-2 btn btn-outline-success btn-sm shadow-sm "><i class="mdi mdi-dots-vertical mr-2"></i><?= lang('more') ?></a>
                            <span class="text-nowrap"><?= lang('total_loans') ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card widget-flat bg-gen">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="mdi mdi-pulse widget-icon"></i>
                        </div>
                        <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('products') ?>"><?= lang('products') ?></h5>
                        <small class="font-12 mb-0 badge-primary-lighten badge"><?= $this->country->currency ?></small>
                        <h3 class="mt-0 mb-3"><?= number_format($product->total_available_stock_price, 2) ?></h3>
                        <p class="mb-0 text-muted">
                            <a data-fancy href="<?= base_url('products') ?>" class="mr-2 btn btn-outline-primary btn-sm shadow-sm "><span class="mr-1"><?= $product->total_available_stock ?></span><?= lang('products') . ' ' . lang('available') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-7  col-lg-6">
        <div class="card bg-gen">
            <div class="card-body">
                <h4 class="header-title mb-3">
                    <span class="mr-3">
                        <small class="mdi mdi-checkbox-blank-circle text-success align-middle mr-1"></small> <?= lang('savings') ?>
                    </span>
                    <span class="mr-3">
                        <small class="mdi mdi-checkbox-blank-circle text-primary align-middle mr-1"></small> <?= lang('loan') ?>
                    </span>
                    <span class="mr-3">
                        <small class="mdi mdi-checkbox-blank-circle text-danger align-middle mr-1"></small> <?= lang('withdrawal') ?>
                    </span>
                </h4>

                <div id="high-performing-product" class="apex-charts" data-colors="#536de6,#10c469,#ff5b5b"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel"><?= lang('filter') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('dashboard') ?>
                <div class="form-group">
                    <label><?= lang('select_year') ?></label>
                    <div class="input-group">
                        <input type="text" name="year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                        <div class="input-group-append">
                            <span data-toggle="tooltip" title="Today" class="input-group-text bg-primary border-primary text-white">
                                <i class="mdi mdi-calendar-range font-13"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary btn-block mt-2" type="submit"><i class="uil-filter mr-2"></i><?= lang('filter') ?></button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>