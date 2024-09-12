<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('add') . ' ' . lang('credit_sales') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('creditsales/add', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12 form-group">
                        <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                        <div class="input-group input-group-merge">
                            <input type="text" autofocus readonly name="member_id" value="<?= set_value('member_id', $order_details->username) ?>" id="member_id" class="form-control" required>
                            <input type="hidden" value="<?=$order_details->order_details?>" name="order_details" id="order_details">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="get_credit_sales_info()"><i class="mdi mdi-details mr-1"></i><?= lang('details') ?></button>
                            </div>
                        </div>
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
                        <h5 class="ml-2"><?= lang('existing_credit_sales') ?></h5>
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
                        <input type="text" readonly name="amount" id="amount" class="form-control" required value="<?= set_value('amount', $order_details->amount) ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="loan_type"><?= lang('product_type') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('product_type');
                        foreach ($product_types as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('product_type', $scc, set_value('product_type', $order_details->product_type_id), 'class="form-control select2" disabled   id="product_type" data-toggle="select2"'); ?>
                        <input type="hidden" name="product_type" value="<?= $order_details->product_type_id ?>">
                    </div>
                </div>
                <div class="mt-2 mb-2">
                    <h6 class="font-15"><?= lang('detect_guarantor') ?></h6>
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input name="detect_guarantor" onclick="generate_credit_sales_guarantor_field()" type="checkbox" class="custom-control-input" id="detect_guarantor">
                        <label class="custom-control-label" for="detect_guarantor"> <?= lang('detect_guarantor_text') ?></label>
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
                        <button id="preview_loan_schedule_hide" type="button" class="btn btn-primary btn-block mt-3" onclick="preview_credit_ssales_schedule()"><i class="mdi mdi-calendar-range mr-1"></i><?= lang('previw_schedule') ?></button>
                        <button class="btn btn-primary btn-block mt-3" type="button" id="preview_loan_schedule_show" disabled style="display: none;">
                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('description') ?>"><?= lang('description') ?></label>
                    <textarea class="form-control" value="<?= set_value('description') ?>" id="description" name="description" required=""></textarea>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>

        </div>
    </div>
</div>

<button type="button" id="display_schedule" style="display: none;" class="btn btn-secondary" data-toggle="modal" data-target="#bottom-modal">Bottom Modal</button>
<div id="bottom-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-bottom">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title" id="bottomModalLabel"><?= lang('credit_sales') . ' ' . lang('schedule') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="card-body rounded bg-primary cta-box text-white">
                    <div class="float-left">
                        <h5><?= lang('product_type') ?></h5>
                        <span class="font-16"> <i class="uil-briefcase"> </i> <span id="product_type_name"></span> </span>
                    </div>
                    <div class="float-right">
                        <h5><?= lang('tenure') ?></h5>
                        <span class="font-16"> <i class="uil-calendar-alt"> </i> <span id="tenure1"></span> </span>
                    </div>
                    <span class=" clearfix"></span>
                </div>
                <div class="px-3 py-1 rounded bg-light mt-1">
                    <div class="">
                        <span class="font-18 float-left"> <?= lang('principal') ?> </span>
                        <h4 class="float-right" id="principal"></h4>
                        <span class=" clearfix"></span>
                    </div>
                    <div class="">
                        <span class="font-18 float-left"><?= lang('interest') ?> </span>
                        <h4 class="float-right" id="interest"></h4>
                        <span class=" clearfix"></span>
                    </div>
                    <div class="">
                        <span class="font-18 float-left"><?= lang('total_due') ?> </span>
                        <h4 class="float-right" id="total_due"></h4>
                        <span class=" clearfix"></span>
                    </div>
                </div>
                <div class="px-3 py-1 rounded bg-light  mt-1">
                    <h4 class="text-primary"><?= lang('repayment') ?></h4>
                    <div class="">
                        <span class="font-18 float-left"> <?= lang('principal') ?> </span>
                        <h4 class="float-right" id="principal_due"></h4>
                        <span class=" clearfix"></span>
                    </div>
                    <div class="">
                        <span class="font-18 float-left"> <?= lang('interest') ?> </span>
                        <h4 class="float-right" id="interest_due"></h4>
                        <span class=" clearfix"></span>
                    </div>
                    <div class="">
                        <span class="font-18 float-left"> <?= lang('total') . ' ' . lang('monthly_due') ?> </span>
                        <h4 class="float-right" id="monthly_due"></h4>
                        <span class=" clearfix"></span>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-block mt-1" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>