<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-6">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-user-group text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $this->country->currency ?> <?= number_format($settled->price, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total') . ' ' . lang('settled') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-6">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-exit text-danger" style="font-size: 24px;"></i>
                                <h3><span class="text-danger"><?= $this->country->currency ?> <?= number_format($unsettled->price, 2) ?></span></h3>
                                <p class="text-danger font-15 mb-0"><?= lang('total') . ' ' .  lang('unsettled') ?></p>
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
                        <a href="" data-toggle="modal" data-target="#settle-sms-fee" class="btn btn-primary mb-2"><i class="mdi mdi-check-all mr-2"></i><?= lang('settle').' '. lang('sms_fee') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('unit') ?></th>
                            <th><?= lang('price') ?></th>
                            <th><?= lang('payment') ?></th>
                            <!-- <th style="width: 80px"><?= lang('actions') ?></th> -->
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($sms as $m) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($m->date) ?></td>
                                <td><?= $m->username ?></td>
                                <td><?= ucwords($m->first_name . ' ' . $m->last_name) ?></td>
                                <td><?= number_format($m->unit, 2) ?></td>
                                <td><?= number_format($m->price, 2) ?></td>
                                <td>
                                    <?php if ($m->payment == 'settled') { ?>
                                        <span class="badge badge-success"><?= $m->payment ?></span>
                                    <?php } ?>
                                    <?php if ($m->payment == 'unsettled') { ?>
                                        <span class="badge badge-primary"><?= $m->payment ?></span>
                                    <?php } ?>
                                </td>
                                <!-- <td>
                                    <a data-fancy href="<?= base_url('registration/profile/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="<?= lang('profile') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-account-box"></i> </a>
                                    <a onclick="return confirm('Are you sure you want to delete')" data-fancy href="<?= base_url('registration/delete_member/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="<?= lang('delete') ?>" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
                                </td> -->
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
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <?= lang('filter') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="settle-sms-fee" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('settle') . ' ' . lang('sms_fee') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('communication/settle_sms_fee', 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="savings_type"><?= lang('savings_type') ?></label>
                    <p class="text-muted mt-0">The saving account from which the fee should be settled</p>
                    <?php
                    $stt[""] = lang('select') . ' ' . lang('savings_type');
                    foreach ($savings_type as $st) {
                        $stt[$st->id] = $st->name;
                    }
                    ?>
                    <?= form_dropdown('savings_type', $stt, set_value('savings_type'), ' "class="form-control select2" id="savings_type" data-toggle="select2"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-check-all mr-1"></i><?= lang('settle') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>