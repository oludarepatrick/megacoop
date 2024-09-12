<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="" data-toggle="modal" data-target="#savings-type-modal" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('add') . ' ' . lang('product_type') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="basic-datatable">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('updated_on') ?></th>
                            <th><?= lang('name') ?></th>
                            <th><?= lang('rate') ?></th>
                            <th><?= lang('calc_method') ?></th>
                            <th><?= lang('guarantor') ?>(s)</th>
                            <th><?= lang('min_month') ?>(s)</th>
                            <th><?= lang('max_month') ?>(s)</th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loan_types as $lt) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($lt->created_on) ?></td>
                                <td><?= $this->utility->just_date($lt->updated_on) ?></td>
                                <td><?= ucwords($lt->name) ?></td>
                                <td><?= $lt->rate ?></td>
                                <td><?= ucwords($lt->calc_method) ?></td>
                                <td><?= $lt->guarantor ?></td>
                                <td><?= $lt->min_month ?></td>
                                <td><?= $lt->max_month ?></td>
                                <td>
                                    <a href="" onclick="edit_product_type(<?= $lt->id ?>)" data-toggle="modal" data-target="#edit-modal" data-toggle="tooltip" title="<?= lang('edit') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <a data-fancy href="<?= base_url('categories/delete_product_type/' . $this->utility->mask($lt->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div id="savings-type-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('add') . ' ' . lang('product_type') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('categories/add_product_type', 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                    <input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" required>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?= lang('rate') ?></label>
                        <input name="rate" required data-bts-max="500" data-toggle="touchspin" value="0.0" type="text" data-step="0.01" data-decimals="2">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?= lang('guarantor') ?>(s)</label>
                        <input name="guarantor" required data-toggle="touchspin" value="0" type="text" data-step="1" data-decimals="0">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?= lang('min_month') ?></label>
                        <input name="min_month" required data-toggle="touchspin" value="1" type="text" data-step="1" data-decimals="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?= lang('max_month') ?>(s)</label>
                        <input name="max_month" required data-toggle="touchspin" value="6" type="text" data-step="1" data-decimals="0">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="calc_method"><?= lang('calc_method') ?></label><br>
                        <small>Method for calculatig repayment</small>
                        <?php ?>
                        <?= form_dropdown('calc_method', $calc_method, '', '"class="form-control select2" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label><?= lang('description') ?></label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="form-group border rounded bg-white px-2 py-1">
                    <h6 class="font-15 mt-3">This section is only for market hub</h6>
                    <div class="custom-control custom-switch custom-control-inline">
                        <input type="checkbox" name="is_market_product" value="yes" class="custom-control-input" id="customSwitch1">
                        <label class="custom-control-label" for="customSwitch1">For market hub ?</label>
                    </div>
                    <div class="custom-control custom-switch custom-control-inline">
                        <input type="checkbox" name="auto_approval" value="yes" class="custom-control-input" id="customSwitch2">
                        <label class="custom-control-label" for="customSwitch2">Automatic Approval</label>
                    </div>
                    <div class="form-group mt-2">
                        <label><?= lang('max_purchaseable') ?></label><br>
                        <small>(e.g 200 mean 200% of total saving balance).</small>
                        <input name="max_purchaseable" data-bts-max="500" required data-toggle="touchspin" value="0.0" type="text" data-step="0.01" data-decimals="2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('edit') . ' ' . lang('product_type') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <?= form_open('categories/edit_product_type', 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <input type="hidden" name="id" id="id" class="form-control">
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?= lang('rate') ?></label>
                        <input name="rate" id="rate" required data-toggle="touchspin" value="0.0" type="text" data-step="0.01" data-decimals="2">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?= lang('guarantor') ?>(s)</label>
                        <input name="guarantor" id="guarantor" required data-toggle="touchspin" value="0" type="text" data-step="1" data-decimals="0">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?= lang('min_month') ?></label>
                        <input name="min_month" required id="min_month" data-toggle="touchspin" value="1" type="text" data-step="1" data-decimals="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?= lang('max_month') ?>(s)</label>
                        <input name="max_month" id="max_month" required data-toggle="touchspin" value="6" type="text" data-step="1" data-decimals="0">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="calc_method"><?= lang('calc_method') ?></label><br>
                        <small>Method of calculatig loan repayment</small>
                        <?php ?>
                        <?= form_dropdown('calc_method', $calc_method, '', ' id="calc_method" "class="form-control select2" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label><?= lang('description') ?></label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>
                <div class="form-group border rounded bg-white px-2 py-1">
                    <h6 class="font-15 mt-3">This section is only for market hub</h6>
                    <div class="custom-control custom-switch custom-control-inline">
                        <input type="checkbox" name="is_market_product" id="is_market_product" value="yes" class="custom-control-input">
                        <label class="custom-control-label" for="is_market_product">For market hub ?</label>
                    </div>
                    <div class="custom-control custom-switch custom-control-inline">
                        <input type="checkbox" name="auto_approval" id="auto_approval" value="yes" class="custom-control-input" id="auto_approval">
                        <label class="custom-control-label" for="auto_approval">Automatic Approval</label>
                    </div>
                    <div class="form-group mt-2">
                        <label><?= lang('max_purchaseable') ?></label><br>
                        <small>(e.g 200 mean 200% of total saving balance).</small>
                        <input name="max_purchaseable" id="max_purchaseable" data-bts-max="500" required data-toggle="touchspin" value="0.0" type="text" data-step="0.01" data-decimals="2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>