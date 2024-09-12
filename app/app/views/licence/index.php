<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary cta-box text-white">
            <div class="card-body">
                <div class="text-right">
                    <?php if ($status == 'active') { ?>
                        <span class="badge badge-success pr-2 pl-2"><?= $status ?></span>
                    <?php } ?>
                    <?php if ($status == 'due') { ?>
                        <span class="badge badge-danger pr-2 pl-2"><?= $status ?></span>
                    <?php } ?>
                </div>
                <div class="text-center">
                    <img class="my-3" src="<?= $assets ?>/images/cashflow.svg" width="160" alt="Generic placeholder image">
                    <br />
                </div>
                <h4 class="mb-0"><?= $licence_cat ?> Licence</h4>
                <p class="mb-0">Unlimited access for <?= $month ?> month(s)</p>

                <h4 class="mb-0 mt-3">Licence Duration</h4>
                <p class="">Your licence expires in <?= $remain ?> day(s)</p>
                <div class="float-left">
                    <p class="mb-0 font-weight-bold"><?= lang('start_date') ?></p>
                    <span> <i class="uil-calendar-alt"> </i> <?= $start_date ?> </span>
                </div>
                <div class="float-right">
                    <p class="mb-0 font-weight-bold"><?= lang('start_date') ?></p>
                    <span> <i class="uil-calendar-alt"> </i> <?= $end_date ?> </span>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php if ($status == 'active') { ?>
                <hr class="m-0">
                <div class="card-body">
                    <h4 class="mb-0 mt-0"><?= $licence_cat ?> Licence</h4>
                    <p>Activated for <?= $unit ?> member(s)</p>
                    <a href="javascript:licence_component('<?= $this->utility->mask($licence_cat_id) ?>')" class="btn btn-light">
                        Increase Member
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row">
            <?php foreach ($licence as $l) { ?>
                <div class="col-md-6">
                    <div class="card card-pricing text-center">
                        <h4 class="card-header text-uppercase"><?= $l->name ?></h4>
                        <div class="card-body">
                            <h2 class=""><?= number_format($l->amount, 2) ?> <small>/ <?= lang('member') ?></small></h2>
                            <p>Unlimited Access for <?= $l->month ?> Month(s)</p>
                            <p> <?= $l->min_member ?> members and above</p>
                            <?php if ($status == 'due') { ?>
                                <a href="javascript:licence_component('<?= $this->utility->mask($l->id) ?>')" class="btn btn-primary">
                                    <?= lang('renew') ?> <?= lang('licence') ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>