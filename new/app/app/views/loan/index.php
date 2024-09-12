<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-4 ">
                        <div class="card shadow-none m-0">
                            <div class="card-body pb-1">
                                <button class="float-right btn btn-sm btn-light" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant"></i></button>
                                <div>
                                    <span class="font-18 mr-2"><?= lang('loan_type') ?>: </span>
                                    <strong class=" badge badge-primary text-uppercase">
                                        <?php if (isset($loan_type->name)) {
                                            echo $loan_type->name;
                                        } else
                                            echo $loan_type;
                                        ?>
                                    </strong>
                                </div>
                                <div>
                                    <span class="font-18 mr-2"> <?= lang('date') ?>: </span>
                                    <strong class=""><?= $this->utility->just_date($start_date, false) . ' to ' . $this->utility->just_date($end_date, false) ?></strong>
                                </div>
                                <hr>
                                <div>
                                    <div class=" float-left">
                                        <span class="font-18 mr-2"> <?= lang('loan') ?>: </span>
                                        <strong class=""><?= number_format($filter_total_loans->principal, 2) ?></strong>
                                    </div>
                                    <div class="float-right">
                                        <span class="font-18 mr-2"> <?= lang('interest') ?>: </span>
                                        <strong class=""><?= number_format($filter_total_interest->interest, 2) ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-suitcase text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $this->country->currency ?> <?= number_format($total_loans->principal, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total') . ' ' . lang('requested_loans') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class=" dripicons-graph-pie text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= $this->country->currency ?> <?= number_format($total_interest->interest, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('total') . ' ' . lang('interest') ?></p>
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
                        <a data-fancy href="<?= base_url('loan/add') ?>" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('add_loan') ?></a>
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
                            <th><?= lang('interest') ?></th>
                            <th><?= lang('monthly_due') ?></th>
                            <th><?= lang('loan_type') ?></th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
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
                                <td><?= number_format($st->interest, 2) ?></td>
                                <td><?= number_format($st->monthly_due, 2) ?></td>
                                <td><?= ucfirst($st->loan_type) ?></td>
                                <td>
                                    <a data-fancy href="<?= base_url('loan/preview/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('preview') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-dots-vertical"></i> </a>
                                    <a data-fancy href="<?= base_url('loan/edit/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('edit') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <a data-fancy href="<?= base_url('loan/delete/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
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
                    <?= form_open('loan', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?php
                            $scc[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_types as $sc) {
                                $scc[$sc->id] = $sc->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type', $scc, set_value('loan_type'), 'class="form-control select2"  data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-12 mt-2">
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