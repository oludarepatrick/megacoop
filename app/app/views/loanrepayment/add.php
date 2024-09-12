<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('add_loan_repayment') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('loanrepayment/add/' . $this->utility->mask($loan->id), 'class="needs-validation" novalidate') ?>
                <div>
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <span class="float-left m-2 mr-4">
                                <img src="<?= $assets ?>/images/users/<?= $member->avatar ?>" style="height: 100px;" alt="" class=" img-thumbnail">
                            </span>
                            <div class="media-body">

                                <h4 class="mt-1 mb-1"><?= ucwords($member->first_name . ' ' . $member->last_name) ?></h4>
                                <p class="font-13"> <?= ucfirst($member->role) ?></p>
                                <ul class="mb-0 list-inline">
                                    <li class="list-inline-item mr-3">
                                        <h5 class="mb-1"><?= $member->username ?></h5>
                                        <p class="mb-0 font-13"><?= lang('member_id') ?></p>
                                    </li>
                                    <li class="list-inline-item mr-3">
                                        <h5 class="mb-1"><?= number_format($wallet_bal, 2) ?></h5>
                                        <p class="mb-0 font-13"><?= lang('wallet_bal') ?></p>
                                    </li>
                                </ul>
                            </div>
                            <!-- end media-body-->
                        </div>
                        <!-- end card-body-->
                    </div>
                    <div class="card rounded mt-2 bg-primary text-white">
                        <h5 class="ml-2"><?= $loan->loan_type ?></h5>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><?= lang('total') ?></th>
                                        <th><?= lang('paid') ?></th>
                                        <th><?= lang('balance') ?></th>
                                    </tr>
                                </thead>
                                <tbody class=" text-white">
                                    <tr>
                                        <td><?= number_format($loan->total_due, 2) ?></td>
                                        <td><?= number_format($loan->total_due - $loan->total_remain, 2) ?></td>
                                        <td><?= number_format($loan->total_remain, 2) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount', $loan->monthly_due) ?>" data-toggle="input-mask" data-mask-format="000.000.000.000,000,000.00" data-reverse="true">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="source"><?= lang('source') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('source');
                        foreach ($savings_source as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('source', $scc, set_value('source'), 'class="form-control select2" name="source"  id="source" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="<?= lang('month') ?>"><?= lang('month') ?></label>
                        <input type="text" name="month" class="form-control" data-provide="datepicker" data-date-format="MM" data-date-min-view-mode="1">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('year') ?>"><?= lang('year') ?></label>
                        <input type="text" name="year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('narration') ?>"><?= lang('narration') ?></label>
                    <textarea class="form-control" name="narration" value="<?= set_value('narration') ?>" id="description" name="description" required=""></textarea>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_phone" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel"><?= lang('edit') . ' ' . lang('phone') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('loan/add', 'class="needs-validation" novalidate') ?>
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
                        <?= form_dropdown('loan_type', $scc, set_value('loan_type'), 'class="form-control select2" required onchange="generate_loan_guarantor_field()"   id="loan_type" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <label for="<?= lang('tenure') ?>"><?= lang('tenure') ?> (In months)</label>
                        <input type="text" name="tenure" class="form-control" id="tenure" required data-bts-max="1000" data-toggle="touchspin" type="text" data-step="1" data-decimals="0">
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
</div>