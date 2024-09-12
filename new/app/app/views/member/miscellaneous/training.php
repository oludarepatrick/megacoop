<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                    </div>
                </div>

                <table class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-member">
                    <thead>
                        <tr>
                            <th>
                                <span></span><?= lang('cooperative') ?> <?= lang('training') ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($training as $st) { ?>
                            <tr>
                                <td>
                                    <div>
                                        <span class="float-right "> <?= $this->utility->just_date($st->created_on, FALSE) ?></span>
                                        <span class=" font-weight-bolder"><?= ucwords($st->title) ?></span>
                                    </div>
                                    <p><?= ucfirst($st->description) ?></p>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('add') ?> <?= lang('training') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('miscellaneous/add_training', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                        <input type="text" name="member_id" onblur="member_info()" id="member_id" class="form-control" value="<?= set_value('member_id') ?>" required>
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                        <input type="text" name="name" id="name" readonly class="form-control" value="<?= set_value('name') ?>" required>
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('title') ?>"><?= lang('training') . ' ' . lang('title') ?></label>
                        <input type="text" name="title" class="form-control" value="<?= set_value('title') ?>" required>
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