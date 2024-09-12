<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row justify-content-center ">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h4><?= lang($name) ?>
                    <a href="<?= base_url('accounting/auto_postings') ?>" class="float-right btn btn-secondary"><i class="dripicons-arrow-left mr-2"></i> <?= lang('back') ?></a>
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary float-right mr-2" data-toggle="modal" data-target="#top-modal-sub">
                            <i class="mdi mdi-pen mr-1"></i><?= lang('update') ?>
                        </button>
                    </div>
                </div>
                <table class="table  dt-responsive nowrap w-100" id="basic-datatable-account">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('loan_type') ?></th>
                            <th><?= lang('principal') ?> <?= lang('cr') ?> </th>
                            <th><?= lang('principal') ?> <?= lang('dr') ?> </th>
                            <th><?= lang('interest') ?> <?= lang('cr') ?> </th>
                            <th><?= lang('interest') ?> <?= lang('dr') ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tracker as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= $st->loan_type ?></td>
                                <td><?= $st->principal_cr ?></td>
                                <td><?= $st->principal_dr ?></td>
                                <td><?= $st->interest_cr ?></td>
                                <td><?= $st->interest_dr ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="top-modal-sub" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-center modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="topModalLabel"><?= lang('update') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('accounting/update_auto_post_loan', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <?php if ($name == 'loan' or $name == 'loan_repayment') { ?>
                        <div class="col-md-12 form-group">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?php
                            $stt[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_type as $st) {
                                $stt[$st->id] = $st->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type', $stt, set_value('loan_type'), 'class="form-control select2" name="loan_type"  id="savings_type" data-toggle="select2"'); ?>
                        </div>
                    <?php } else { ?>
                        <div class="col-md-12 form-group">
                            <label for="product_type"><?= lang('product_type') ?></label>
                            <?php
                            $stt[""] = lang('select') . ' ' . lang('product_type');
                            foreach ($product_type as $st) {
                                $stt[$st->id] = $st->name;
                            }
                            ?>
                            <?= form_dropdown('product_type', $stt, set_value('product_type'), 'class="form-control select2" name="loan_type"  id="savings_type" data-toggle="select2"'); ?>
                        </div>
                    <?php } ?>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('dr') ?>"><?= lang('principal') . ' ' . lang('dr') . ' ' . lang('account') ?></label>
                        <select class="form-control select2" data-toggle="select2" name="principal_dr">
                            <?php foreach ($acc_data as $key_1 => $val_1) { ?>
                                <optgroup label="<?= $key_1 ?>">
                                    <?php foreach ($val_1 as $key_2 => $val_2) { ?>
                                <optgroup label="&nbsp; &nbsp; &nbsp;<?= $key_2 ?>">
                                    <?php foreach ($val_2 as $val_3) { ?>
                                        <option value="<?= $val_3->id ?>">&nbsp; &nbsp;&nbsp; &nbsp;<?= $val_3->code . ' - ' . $val_3->name ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                            </optgroup>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('cr') ?>"><?= lang('principal') . ' ' . lang('cr') . ' ' . lang('account') ?></label>
                        <select class="form-control select2" data-toggle="select2" name="principal_cr">
                            <?php foreach ($acc_data as $key_1 => $val_1) { ?>
                                <optgroup label="<?= $key_1 ?>">
                                    <?php foreach ($val_1 as $key_2 => $val_2) { ?>
                                <optgroup label="&nbsp; &nbsp; &nbsp;<?= $key_2 ?>">
                                    <?php foreach ($val_2 as $val_3) { ?>
                                        <option value="<?= $val_3->id ?>">&nbsp; &nbsp;&nbsp; &nbsp;<?= $val_3->code . ' - ' . $val_3->name ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                            </optgroup>
                        <?php } ?>
                        </select>
                        <input type="hidden" name="ledger_type" value="<?= $name ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('dr') ?>"><?= lang('interest') . ' ' . lang('dr') . ' ' . lang('account') ?></label>
                        <select class="form-control select2" data-toggle="select2" name="interest_dr">
                            <?php foreach ($acc_data as $key_1 => $val_1) { ?>
                                <optgroup label="<?= $key_1 ?>">
                                    <?php foreach ($val_1 as $key_2 => $val_2) { ?>
                                <optgroup label="&nbsp; &nbsp; &nbsp;<?= $key_2 ?>">
                                    <?php foreach ($val_2 as $val_3) { ?>
                                        <option value="<?= $val_3->id ?>">&nbsp; &nbsp;&nbsp; &nbsp;<?= $val_3->code . ' - ' . $val_3->name ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                            </optgroup>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('cr') ?>"><?= lang('interest') . ' ' . lang('cr') . ' ' . lang('account') ?></label>
                        <select class="form-control select2" data-toggle="select2" name="interest_cr">
                            <?php foreach ($acc_data as $key_1 => $val_1) { ?>
                                <optgroup label="<?= $key_1 ?>">
                                    <?php foreach ($val_1 as $key_2 => $val_2) { ?>
                                <optgroup label="&nbsp; &nbsp; &nbsp;<?= $key_2 ?>">
                                    <?php foreach ($val_2 as $val_3) { ?>
                                        <option value="<?= $val_3->id ?>">&nbsp; &nbsp;&nbsp; &nbsp;<?= $val_3->code . ' - ' . $val_3->name ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                            </optgroup>
                        <?php } ?>
                        </select>
                        <input type="hidden" name="ledger_type" value="<?= $name ?>">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                    <div class="clearfix"></div>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>