<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">

                <!-- Invoice Logo-->
                <div class="clearfix">
                    <div class="float-left mb-3">
                        <img src="<?= $assets ?>images/logo/coop/<?= $this->coop->logo ?>" alt="" height="40">
                    </div>
                    <div class="float-right">
                        <h5><?= $this->coop->coop_name ?></h5>
                        <address>
                            <?= $this->coop->coop_address ?><br>
                            <?= $this->coop->contact_phone ?>
                        </address>
                    </div>
                </div>

                <!-- Invoice Detail-->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="float-left mt-3">
                            <p><b>Hello, <?= ucwords($user->first_name . ' ' . $user->last_name) ?></b></p>
                            <p><b><?= lang('member_id') ?>: <?= ucwords($user->username) ?></b></p>
                            <p class="font-13"><b><?= lang('date') ?>: </b> <span class="float-right"> <?= date('Y-M-d g:i a') ?> </span></p>
                            <p class="font-13"><b><?= lang('savings_type') ?>: </b> <span class="float-right"><?= $savings_type->name ?></span></p>
                            <div class="chart-widget-list mt-4">
                                <h5 class="text-primary"><?= lang('summary') ?></h5>
                                <p class="mb-2">
                                    <i class="mdi mdi-square text-success"></i> <?= lang('credit') ?>
                                    <span class="float-right"><?= number_format($credit, 2) ?></span>
                                </p>
                                <p class="mb-2">
                                    <i class="mdi mdi-square text-danger"></i> <?= lang('debit') ?>
                                    <span class="float-right">-<?= number_format($debit, 2) ?></span>
                                </p>
                                <p class="mb-2">
                                    <i class="mdi mdi-square text-primary"></i> <?= lang('balance') ?>
                                    <span class="float-right"><?= number_format($credit - $debit, 2) ?></span>
                                </p>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row mt-4">
                    <div class="col text-center">
                        <h5><?= lang('savings') ?> <?= lang('statement') ?> <?= lang('btw') ?> <?= $start_date ?> and <?= $end_date ?></h5>
                    </div> <!-- end col-->
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('narration') ?></th>
                                        <th><?= lang('credit') ?></th>
                                        <th><?= lang('debit') ?></th>
                                        <th><?= lang('balance') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($savings as $st) { ?>
                                        <tr>
                                            <td><?= $this->utility->just_date($st->created_on) ?></td>
                                            <td><?= ucfirst($st->narration) ?> ( <?= $st->month . ' ' . $st->year ?> )</td>
                                            <?php if ($st->tranx_type === 'credit') { ?>
                                                <td><?= number_format($st->amount, 2) ?></td>
                                                <td>0.00</td>
                                            <?php } ?>
                                            <?php if ($st->tranx_type === 'debit') { ?>
                                                <td>0.00</td>
                                                <td><?= number_format($st->amount, 2) ?></td>
                                            <?php } ?>
                                            <td><?= number_format($st->balance, 2) ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if (!$savings) { ?>
                                        <tr>
                                            <td colspan="5" class="text-center"><?= lang('record_empty') ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end col -->
                </div>

                <div class="d-print-none mt-4">
                    <div class="text-right">
                        <a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> <?= lang('print') ?></a>
                        <a data-fancy href="<?= base_url('statement') ?>" class="btn btn-info"><?= lang('close') ?></a>
                    </div>
                </div>
                <!-- end buttons -->

            </div> <!-- end card-body-->
        </div> <!-- end card -->
    </div> <!-- end col-->
</div>