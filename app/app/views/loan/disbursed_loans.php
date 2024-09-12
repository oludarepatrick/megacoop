<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-suitcase text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $this->country->currency ?> <?= number_format($principal, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('principal') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-plus text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= $this->country->currency ?> <?= number_format($interest, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('interest') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-multiple" style="font-size: 24px;"></i>
                                <h3><span class=""><?= $this->country->currency ?> <?= number_format($principal + $interest, 2) ?></span></h3>
                                <p class=" font-15 mb-0"><?= lang('total_due') ?></p>
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
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('principal') ?></th>
                            <th><?= lang('total_due') ?></th>
                            <th><?= lang('total_remain') ?></th>
                            <th><?= lang('monthly_due') ?></th>
                            <th><?= lang('loan_type') ?></th>
                            <th><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= $st->username ?></td>
                                <td><?= ucfirst($st->full_name) ?></td>
                                <td><?= number_format($st->principal, 2) ?></td>
                                <td><?= number_format($st->total_due, 2) ?></td>
                                <td><?= number_format($st->total_remain, 2) ?></td>
                                <td><?= number_format($st->monthly_due, 2) ?></td>
                                <td><?= ucfirst($st->loan_type) ?></td>
                                <td>
                                    <a data-fancy href="<?= base_url('loanrepayment/add/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('add_repayment') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-cash-plus"></i> </a>
                                    <a data-fancy href="<?= base_url('loanrepayment/refinance/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('refinance') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-cash"></i> </a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
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
                    <?= form_open('loan/disbursed_loans', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?= form_dropdown('loan_type', $loan_types, set_value('loan_type'), ' "class="form-control select2" data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-12 form-group">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" value="<?= set_value('start_date') ?>" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class=" col-md-12 form-group">
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