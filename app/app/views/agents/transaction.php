<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-header">
                <h5><i class="mdi mdi-timer text-primary"> </i></span><?= lang('wallet_tranx_btw') ?><?= $this->utility->just_date($start_date) . ' and ' . $this->utility->just_date($end_date) ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class=" dripicons-download text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= number_format($total_wallet_credit->amount, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total') ?> <?= lang('credit') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-upload text-danger" style="font-size: 24px;"></i>
                                <h3><span class="text-danger"><?= number_format($total_wallet_debit->amount, 2) ?></span></h3>
                                <p class="text-danger font-15 mb-0"><?= lang('total') ?> <?= lang('debit') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-suitcase text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= number_format($total_wallet_credit->amount - $total_wallet_debit->amount, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('total') ?> <?= lang('balance') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="datatable-buttons">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('narration') ?></th>
                            <th><?= lang('tranx') ?></th>
                            <th><?= lang('gate_way') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('status') ?></th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wallet as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= ucfirst($st->full_name) ?></td>
                                <td><?= $st->narration ?></td>
                                <td>
                                    <?php if ($st->tranx_type == 'credit') { ?>
                                        <span class="badge badge-success"><?= $st->tranx_type ?></span>
                                    <?php } ?>
                                    <?php if ($st->tranx_type == 'debit') { ?>
                                        <span class="badge badge-danger"><?= $st->tranx_type ?></span>
                                    <?php } ?>
                                </td>
                                <td><span class="badge badge-primary-lighten"><?= $st->gate_way ?></span></td>
                                <td><?= number_format($st->amount, 2) ?></td>
                                <td>
                                    <?php if ($st->status == 'successful') { ?>
                                        <span class="badge badge-success"><?= $st->status ?></span>
                                    <?php } ?>
                                    <?php if ($st->status == 'processing') { ?>
                                        <span class="badge badge-primary"><?= $st->status ?></span>
                                    <?php } ?>
                                    <?php if ($st->status == 'failed') { ?>
                                        <span class="badge badge-danger"><?= $st->status ?></span>
                                    <?php } ?>

                                </td>
                                <td>
                                    <a data-fancy href="<?= base_url('agents/preview/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('preview') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-dots-vertical"></i> </a>
                                    <?php if ($st->status == 'processing'  and $st->gate_way_id == 3) { ?>
                                        <a data-fancy href="<?= base_url('agents/approve/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('approve') ?>" class="btn btn-success btn-sm"><i class=" mdi mdi-check"></i> </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content ">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="border border-light rounded bg-primary p-2 mb-3 text-white">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="m-0 mb-1"><?= lang('filter') ?></h4>
                        </div>
                    </div>
                    <p><?= lang('filter_note') ?></q>
                    </p>
                </div>
                <div class="">
                    <?= form_open('wallet', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-12">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" value="<?= set_value('start_date') ?>" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                            <input type="text" name="end_date" value="<?= set_value('end_date') ?>" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class="col mt-3">
                            <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant mr-1"></i><?= lang('apply_filter') ?></button>
                        </div>
                    </div>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>