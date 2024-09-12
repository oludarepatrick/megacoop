<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class=" col-lg-2">
        
    </div>
    <div class=" col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card cta-box bg-primary text-white">
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="m-0 font-weight-normal cta-box-title"><?= lang('trial_balance')?></h3>

                            <img class="my-3" src="<?=$assets?>images/trial_bal.svg" width="150" alt="Generic placeholder image">

                            <br/>

                            <a data-fancy href="<?= base_url('accounting/trial_balance')?>" class="btn btn-sm btn-light text-primary "><i class=" mdi mdi-book-open-page-variant mr-2"></i> <?= lang('report')?></a>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="card cta-box bg-primary text-white">
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="m-0 font-weight-normal cta-box-title"><?= lang('balance_sheet')?></h3>

                            <img class="my-3" src="<?=$assets?>images/balance_sheet.svg" width="180" alt="Generic placeholder image">

                            <br/>
                            <a data-fancy href="<?= base_url('accounting/balance_sheet')?>" class="btn btn-sm btn-light text-primary "><i class=" mdi mdi-book-open-page-variant mr-2"></i> <?= lang('report')?></a>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="card cta-box bg-primary text-white">
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="m-0 font-weight-normal cta-box-title"><?= lang('income_statement')?></h3>

                            <img class="my-3" src="<?=$assets?>images/income.svg" width="160" alt="Generic placeholder image">

                            <br/>
                            <a data-fancy href="<?= base_url('accounting/income_statement')?>" class="btn btn-sm btn-light text-primary "><i class=" mdi mdi-book-open-page-variant mr-2"></i> <?= lang('report')?></a>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="card cta-box bg-primary text-white">
                    <div class="card-body">
                        <div class="text-center">
                            <h3 class="m-0 font-weight-normal cta-box-title"><?= lang('cash_flow')?></h3>

                            <img class="my-3" src="<?=$assets?>images/cashflow.svg" width="160" alt="Generic placeholder image">

                            <br/>
                            <a data-fancy href="<?= base_url('accounting/cash_flow')?>" class="btn btn-sm btn-light text-primary"><i class=" mdi mdi-book-open-page-variant mr-2"></i> <?= lang('report')?></a>
                        </div>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
        </div>
    </div>
</div>