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
                                <p class="text-success font-15 mb-0"><?= lang('total') . ' ' . lang('approved_loans') ?></p>
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
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                        <button type="button" class="btn btn-outline-primary float-right mr-1" data-toggle="modal" data-target="#batchdisburse"><?= lang('batch_disbursement') ?></button>
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
                                    <a href="#" onclick="disburse_option(<?= $st->id ?>)" data-toggle="tooltip" title="<?= lang('disburse') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-cash-plus"></i> </a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scrollable modal -->
<div class="modal fade" id="disburse_option" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle"><?= lang('disbursement_options') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <a href="#" id="manual_disbursement" onclick="confirm('Are you sure you want to proceed')" class="btn-primary border rounded">
                        <div class="card-body">
                            <h5 class="card-title"><?= lang('manual_disbursement') ?></h5>
                            <p class="card-text">Select this option if you have sent (or will send) the loan to member</p>
                        </div>
                    </a>
                </div>

                <div class="card">
                    <a href="#" id="direct_disbursement" onclick="alert('Coming soon!')" class="btn-light border border-primary rounded">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= lang('direct_disbursement') ?></h5>
                            <p class="card-text text-primary">Select this option to deposit the loan in to member wallet </p>
                        </div>
                    </a>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
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
                    <?= form_open('loan/approved_loans', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?php
                            $scc[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_types as $sc) {
                                $scc[$sc->id] = $sc->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type', $scc, set_value('loan_type'), 'class="form-control select2"  data-toggle="select2"'); ?>
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

<div class="modal fade" id="batchdisburse" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel"><?= lang('batch_disbursement') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="card-body bg-light rounded">
                    <h5 class="mb-2 font-16"><?= lang('instruction_batch_upload') ?></h5>
                    <p><?= lang('how_to_upload_batch1') ?></p>
                    <p><?= lang('how_to_upload_batch2') ?></p>
                    <p><?= lang('how_to_upload_batch3') ?></p>
                    <p><?= lang('how_to_upload_batch4') ?></p>
                    <div class="form-row">
                        <div class="col-md-8 form-group">
                            <?php
                            $stt[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_types as $st) {
                                $stt[$st->id] = $st->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type_1', $stt, set_value('loan_type_1'), 'class="form-control select2" name="loan_type_1"  id="loan_type_1" data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-4 form-group">
                            <a id="hide_generate_savin_btn" data-toggle="tooltip" data-placement="bottom" title="Download" class="btn btn-primary text-white mb-2" onclick="generate_loan_disbursement_template()">
                                <i class='uil uil-notes mr-1'></i> <?= lang('generate_template') ?>
                            </a>
                            <button id="wait_generate_savin_btn" style="display: none" class="btn btn-primary mb-2" type="button" disabled>
                                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                <span class="mr-1 ml-1">Please wait a bit...</span>
                            </button>
                        </div>
                    </div>

                    <div class="card mb-2 mt-2 shadow-none border border-danger" style="display: none" id="savings_template_download">
                        <div class="p-1">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded">
                                            .XLS
                                        </span>
                                    </div>
                                </div>
                                <div class="col pl-0 d-none d-sm-block">
                                    <a class="text-muted font-weight-bold">loan-disbursement-template.xls</a>
                                    <!--<p class="mb-0">4.50 kb</p>-->
                                </div>
                                <div class="col-auto">
                                    <a id="savings_template_download_btn" class="btn btn-primary btn-sm text-white">
                                        <i class='uil uil-cloud-download mr-1'></i> <?= lang('download') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border rounded mt-2">
                    <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <i class="dripicons-wrong mr-2"></i> <span id="error"></span>
                    </div>
                    <h5 class="mb-3 font-16"><?= lang('upload') . ' ' . lang('batch_disbursement') ?></h5>
                    <?= form_open_multipart('loan/upload_batch_disbursement', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-6 form-group">
                            <label>Maximum upload size, 900kb</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                                    <label class="custom-file-label" for="inputGroupFile04" id="file_label"> </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?php
                            $stt[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_types as $st) {
                                $stt[$st->id] = $st->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type', $stt, set_value('loan_type'), 'class="form-control select2" name="loan_type"  id="loan_type" data-toggle="select2"'); ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-right"><i class="uil uil-cloud-upload mr-1"></i><?= lang('upload') ?></button>
                    <div class="clearfix"></div>
                    <?= form_close() ?>
                </div>

            </div>
        </div>
    </div>
</div>