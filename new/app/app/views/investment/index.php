<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="" data-toggle="modal" data-target="#investment-type-modal" class="btn btn-primary mb-2"><i class="mdi mdi-plus mr-1"></i><?= lang('add') . ' ' . lang('investment') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="basic-datatable">
                    <thead>
                        <tr>
                            <th><?= lang('name') ?></th>
                            <th><?= lang('description') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('roi') ?></th>
                            <th><?= lang('start_date') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($investment as $st) { ?>
                            <tr>

                                <td><?= ucfirst($st->name) ?></td>
                                <td><?= ucfirst($st->description) ?></td>
                                <td><?= number_format($st->amount, 2) ?></td>
                                <td><?= number_format($st->roi, 2) ?></td>
                                <td><?= $this->utility->just_date($st->start_date, FALSE) ?></td>
                                <td><?= $this->utility->just_date($st->end_date, FALSE) ?></td>
                                <td>
                                    <a href="" onclick="edit_investment(<?= $st->id ?>)" data-toggle="modal" data-target="#edit-investment-modal" data-toggle="tooltip" title="<?= lang('edit') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <a data-fancy href="<?= base_url('investment/delete/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
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
<div id="investment-type-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title"><?= lang('add') . ' ' . lang('investment') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('investment/add', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label for="investment_type"><?= lang('investment_type') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('investment_type');
                        foreach ($investment_types as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('investment_type', $scc, set_value('investment_type'), 'class="form-control select2" required data-toggle="select2"'); ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" class="form-control" required value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('roi') ?>"><?= lang('roi') ?></label>
                        <input type="text" name="roi" class="form-control" required value="<?= set_value('roi') ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('rate') ?>"><?= lang('rate') ?></label>
                        <input data-toggle="touchspin" name="rate" value="<?= set_value('rate') ?>" type="text" data-step="0.01" data-decimals="2" data-bts-postfix="%">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('maturity_year') ?>"><?= lang('maturity_year') ?></label>
                        <input type="text" name="maturity_year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                        <input type="text" name="start_date" class="form-control" data-provide="datepicker" data-single-date-picker="true" data-date-format="yyyy-mm-d">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                        <input type="text" name="end_date" class="form-control" data-provide="datepicker" data-single-date-picker="true" data-date-format="yyyy-mm-d">
                    </div>
                    <div class="form-group col-lg-12">
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

<div id="edit-investment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('edit') . ' ' . lang('investment') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <?= form_open('investment/edit', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label for="investment_type"><?= lang('investment_type') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('investment_type');
                        foreach ($investment_types as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('investment_type', $scc, set_value('investment_type'), 'class="form-control select2" required   id="investment_type" data-toggle="select2"'); ?>
                        <input type="hidden" name="id" id="id" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('roi') ?>"><?= lang('roi') ?></label>
                        <input type="text" name="roi" id="roi" class="form-control" required value="<?= set_value('roi') ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('rate') ?>"><?= lang('rate') ?></label>
                        <input data-toggle="touchspin" id="rate" name="rate" value="<?= set_value('rate') ?>" type="text" data-step="0.01" data-decimals="2" data-bts-postfix="%">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('maturity_year') ?>"><?= lang('maturity_year') ?></label>
                        <input type="text" id="maturity_year" name="maturity_year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                        <input type="text" id="start_date" name="start_date" class="form-control" data-provide="datepicker" data-single-date-picker="true" data-date-format="yyyy-mm-d">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                        <input type="text" id="end_date" name="end_date" class="form-control" data-provide="datepicker" data-single-date-picker="true" data-date-format="yyyy-mm-d">
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="<?= lang('description') ?>"><?= lang('description') ?></label>
                        <textarea class="form-control" name="description" required id="description"></textarea>
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