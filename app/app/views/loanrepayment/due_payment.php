<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-header">
                <h5><i class="mdi mdi-timer text-primary"> </i></span><?= lang('due_payment') . ' ' . lang('btw') ?> <?= $this->utility->just_date($start_date) . ' and ' . $this->utility->just_date($end_date) ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-suitcase text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $this->country->currency ?> <?= number_format($total_loans->principal, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total_loans') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-graph-pie text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= $this->country->currency ?> <?= number_format($total_remain, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"> <?= lang('total_remain') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-graph-pie text-danger" style="font-size: 24px;"></i>
                                <h3><span class="text-danger"><?= $this->country->currency ?> <?= number_format($monthly_due, 2) ?></span></h3>
                                <p class="text-danger font-15 mb-0"><?= lang('monthly_due') ?></p>
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
                            <th><?= lang('due_on') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('phone') ?></th>
                            <th><?= lang('email') ?></th>
                            <th><?= lang('principal') ?></th>
                            <th><?= lang('total_due') ?></th>
                            <th><?= lang('interest') ?></th>
                            <th><?= lang('monthly_due') ?></th>
                            <th><?= lang('loan_type') ?></th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->next_payment_date) ?></td>
                                <td><?= $st->username ?></td>
                                <td><?= ucfirst($st->full_name) ?></td>
                                <td><?= $st->phone ?></td>
                                <td><?= $st->email ?></td>
                                <td><?= number_format($st->principal, 2) ?></td>
                                <td><?= number_format($st->total_due, 2) ?></td>
                                <td><?= number_format($st->interest, 2) ?></td>
                                <td><?= number_format($st->monthly_due, 2) ?></td>
                                <td><?= ucfirst($st->loan_type) ?></td>
                                <td>
                                    <a data-fancy href="<?= base_url('loan/preview/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('preview') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-dots-vertical"></i> </a>
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
                    <?= form_open('loanrepayment/due_payment', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?php
                            $scc[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_type as $sc) {
                                $scc[$sc->id] = $sc->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type', $scc, set_value('loan_type'), 'class="form-control select2" required onchange="generate_loan_guarantor_field()"   id="loan_type" data-toggle="select2"'); ?>
                        </div>
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