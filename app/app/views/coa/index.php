<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-xl-4 col-lg-4">
        <div class="card widget-flat">
            <div class="card-body bg-gen">
                <div class="float-right">
                    <i class="uil-home widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Growth"><?= lang('dr') ?></h5>
                <h3 class="mt-3 mb-2 text-primary"><?= lang('assets') ?></h3>
                <p class="mb-0 text-muted">
                    <span class=""><?= lang('configure_coa_for_your_need') ?></span>
                </p>
            </div>
            <div class="card-body">
                <a data-fancy href="<?= base_url('coa/accounts/'.$this->utility->mask($acc_title[2]->id)) ?>" class="btn btn-primary float-right"><i class="uil-edit mr-1"></i> <?= lang('configure') . ' ' . lang('assets') ?></a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4">
        <div class="card widget-flat">
            <div class="card-body bg-gen">
                <div class="float-right">
                    <i class="uil-truck widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Growth"><?= lang('cr') ?></h5>
                <h3 class="mt-3 mb-2 text-primary"><?= lang('liability') ?></h3>
                <p class="mb-0 text-muted">
                    <span class=""><?= lang('configure_coa_for_your_need') ?></span>
                </p>
            </div>
            <div class="card-body">
                <a data-fancy href="<?= base_url('coa/accounts/'.$this->utility->mask($acc_title[3]->id)) ?>" class="btn btn-primary float-right"><i class="uil-edit mr-1"></i> <?= lang('configure') . ' ' . lang('liability') ?></a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4">
        <div class="card widget-flat">
            <div class="card-body bg-gen">
                <div class="float-right">
                    <i class="uil-bag-alt widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Growth"><?= lang('cr') ?></h5>
                <h3 class="mt-3 mb-2 text-primary"><?= lang('income') ?></h3>
                <p class="mb-0 text-muted">
                    <span class=""><?= lang('configure_coa_for_your_need') ?></span>
                </p>
            </div>
            <div class="card-body">
                <a data-fancy href="<?= base_url('coa/accounts/'.$this->utility->mask($acc_title[0]->id)) ?>" class="btn btn-primary float-right"><i class="uil-edit mr-1"></i> <?= lang('configure') . ' ' . lang('income') ?></a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4">
        <div class="card widget-flat">
            <div class="card-body bg-gen">
                <div class="float-right">
                    <i class=" widget-icon  uil-money-insert"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Growth"><?= lang('dr') ?></h5>
                <h3 class="mt-3 mb-2 text-primary"><?= lang('expenses') ?></h3>
                <p class="mb-0 text-muted">
                    <span class=""><?= lang('configure_coa_for_your_need') ?></span>
                </p>
            </div>
            <div class="card-body">
                <a data-fancy href="<?= base_url('coa/accounts/'.$this->utility->mask($acc_title[1]->id)) ?>" class="btn btn-primary float-right"><i class="uil-edit mr-1"></i> <?= lang('configure') . ' ' . lang('expenses') ?></a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4">
        <div class="card widget-flat">
            <div class="card-body bg-gen">
                <div class="float-right">
                    <i class="mdi mdi-account-cash-outline widget-icon"></i>
                </div>
                <h5 class="text-muted font-weight-normal mt-0" title="Growth"><?= lang('cr') ?>/<?= lang('dr') ?></h5>
                <h3 class="mt-3 mb-2 text-primary"><?= lang('equity') ?></h3>
                <p class="mb-0 text-muted">
                    <span class=""><?= lang('configure_coa_for_your_need') ?></span>
                </p>
            </div>
            <div class="card-body">
                <a data-fancy href="<?= base_url('coa/accounts/'.$this->utility->mask($acc_title[4]->id)) ?>" class="btn btn-primary float-right"><i class="uil-edit mr-1"></i> <?= lang('configure') . ' ' . lang('equity') ?></a>
            </div>
        </div>
    </div>
</div>