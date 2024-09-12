<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('add_savings') ?></h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="dripicons-wrong mr-2"></i> <span id="error"></span>
                </div>
                <?= form_open('member/savings/add', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                        <input type="text" name="member_id" value="<?= set_value('member_id', $this->user->username) ?>" readonly id="member_id" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="savings_type"><?= lang('savings_type') ?></label>
                        <?php
                        $stt[""] = lang('select') . ' ' . lang('savings_type');
                        foreach ($savings_type as $st) {
                            $stt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('savings_type', $stt, set_value('savings_type'), ' onchange="get_savings_info()"class="form-control select2" name="savings_type"  id="savings_type" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div id="saving-history" style="display: none;">
                    <div id="spinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="row ">
                        <div class="col-md-6 mt-2">
                            <div class="border pl-2 rounded bg-primary text-white">
                                <h5><?= lang('full_name') ?></h5>
                                <p class="font-18 font-weight-bolder" id="full_name"> </p>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="border pl-2 rounded bg-primary text-white">
                                <h5><?= lang('wallet_bal') ?></h5>
                                <p class="font-18 font-weight-bolder" id="wallet_bal">l</p>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded mt-2 bg-primary text-white">
                        <h5 class="ml-2"><?= lang('last_savings') ?></h5>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('month') ?></th>
                                        <th><?= lang('year') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-white" id="amount_1"></td>
                                        <td class="text-white" id="month_1"></td>
                                        <td class="text-white" id="year_1"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" class="form-control" value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
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
                    <textarea class="form-control" name="narration" value="<?= set_value('narration') ?>" placeholder="<?= date('F Y') ?>  Savings" required=""></textarea>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
            <!-- end row -->

        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end card-->
<!-- end row-->