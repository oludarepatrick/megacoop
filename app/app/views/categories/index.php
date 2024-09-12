<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="" data-toggle="modal" data-target="#savings-type-modal" class="btn btn-primary mb-2"><i class="mdi mdi-plus mr-1"></i><?= lang('add_savings_type') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="basic-datatable">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('name') ?></th>
                            <th><?= lang('max_withdrawal') ?> </th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($savings_types as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= ucwords($st->name) ?></td>
                                <td><?= ucfirst($st->max_withdrawal) ?> %</td>
                                <td>
                                    <a href="" onclick="edit_savings_type(<?= $st->id ?>)" data-toggle="modal" data-target="#edit-savings-type-modal" data-toggle="tooltip" title="<?= lang('edit') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <a data-fancy href="<?= base_url('categories/delete_savings_type/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
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
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('add_savings_type') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('categories/add_savings_type', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12">
                        <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                        <input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" required>
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('max_withdrawal') ?>"><?= lang('max_withdrawal') ?> %</label>
                        <input type="text" name="max_withdrawal" class="form-control" value="<?= set_value('max_withdrawal') ?>" required>
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('description') ?>"><?= lang('description') ?></label>
                        <textarea class="form-control" name="description" required=""></textarea>
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

<div id="edit-savings-type-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('edit_savings_type') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <?= form_open('categories/edit_savings_type', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12">
                        <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <input type="hidden" name="id" id="id" class="form-control">
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('max_withdrawal') ?>"><?= lang('max_withdrawal') ?> %</label>
                        <input id="max_withdrawal" type="text" name="max_withdrawal" class="form-control" value="<?= set_value('max_withdrawal') ?>" required>
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('description') ?>"><?= lang('description') ?></label>
                        <textarea class="form-control" id="description" name="description" required=""></textarea>
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