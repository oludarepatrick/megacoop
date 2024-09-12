<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('add_loan') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('loan/add', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12 form-group">
                        <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                        <input type="text" name="member_id" value="<?= set_value('member_id') ?>" id="member_id" onblur="get_loan_info()" class="form-control" required>
                    </div>
                </div>
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="exixting-loans" style="display: none;">

                    <div class="row ">
                        <div class="col-md-6 mt-2">
                            <div class="border pl-2 rounded bg-primary text-white">
                                <h5><?= lang('full_name') ?></h5>
                                <p class="font-18 font-weight-bolder" id="full_name"> </p>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="border pl-2 rounded bg-primary text-white">
                                <h5><?= lang('savings_bal') ?></h5>
                                <p class="font-18 font-weight-bolder" id="bal"></p>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded mt-2 bg-primary text-white">
                        <h5 class="ml-2"><?= lang('existing_loan') ?></h5>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><?= lang('total') ?></th>
                                        <th><?= lang('paid') ?></th>
                                        <th><?= lang('balance') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-white" id="total"></td>
                                        <td class="text-white" id="paid"></td>
                                        <td class="text-white" id="balance"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="loan_type"><?= lang('loan_type') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('loan_type');
                        foreach ($loan_type as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('loan_type', $scc, set_value('loan_type'), 'class="form-control select2" required onchange="generate_loan_guarantor_field()"  id="loan_type" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div id="spinner1" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div class="row" id="guarantors-wraper">

                </div>

                <div class="row">
                    <div class="form-group col-md-8">
                        <label for="<?= lang('tenure') ?>"><?= lang('tenure') ?> (In months)</label>
                        <input type="text" name="tenure" class="form-control" id="tenure" required data-toggle="touchspin" type="text" data-step="1" data-decimals="0">
                    </div>
                    <div class="form-group col-md-4 p-1">
                        <button id="preview_loan_schedule_hide" type="button" class="btn btn-primary btn-block mt-3" onclick="preview_loan_schedule()"><i class="mdi mdi-calendar-range mr-1"></i><?= lang('previw_schedule') ?></button>
                        <button class="btn btn-primary btn-block mt-3" type="button" id="preview_loan_schedule_show" disabled style="display: none;">
                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
            

        </div> 
    </div> 
    <div class="col-md-2"></div>
</div> <!-- end card-->

<button type="button" id="display_schedule" style="display: none;" class="btn btn-secondary" data-toggle="modal" data-target="#bottom-modal"></button>
<div id="bottom-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-bottom">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title" id="bottomModalLabel"><?= lang('loan_schedule') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-centered table-striped">
                    <tr>
                        <td><b><?= lang('principal') ?></b></td>
                        <td><span id="principal" class="font-18"></span></td>
                    </tr>
                    <tr>
                        <td><b><?= lang('interest') ?></b></td>
                        <td><span id="interest" class="font-18"></span></td>
                    </tr>
                    <tr>
                        <td><b><?= lang('total_due') ?></b></td>
                        <td><span id="total_due" class="font-18"></span></td>
                    </tr>
                    <tr>
                        <td><b><?= lang('monthly_due') ?></b></td>
                        <td><span id="monthly_due" class="font-18"></span></td>
                    </tr>
                    <tr>
                        <td><b><?= lang('tenure') ?></b></td>
                        <td><span id="tenure1" class="font-18"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>