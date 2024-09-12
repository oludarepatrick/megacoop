<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('edit_withdrawal') ?></h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="dripicons-wrong mr-2"></i> <span id="error"></span>
                </div>
                <?= form_open('withdrawal/edit/' . $this->utility->mask($withdrawal->id), 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('search_member_by') ?>"><?= lang('search_member_by') ?></label>
                        <div style="display: none;" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <input type="text" name="member_id" value="<?= set_value('member_id') ?>" required oninput="member_info_live_search()" id="member_id" class="form-control">
                        <div id="result"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="savings_type"><?= lang('withdraw_from') ?></label>
                        <?php
                        $stt[""] = lang('select') . ' ' . lang('savings_type');
                        foreach ($savings_type as $st) {
                            $stt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('savings_type', $stt, set_value('savings_type', $withdrawal->savings_type), ' onchange="get_withdrawal_info()"class="form-control select2"  id="savings_type" data-toggle="select2"'); ?>
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
                    <div class="form-group col-md-12">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" class="form-control" value="<?= set_value('amount', $withdrawal->amount) ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('narration') ?>"><?= lang('narration') ?></label>
                    <textarea class="form-control" name="narration" id="description" required=""><?= $withdrawal->narration ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
            <!-- end row -->

        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end card-->
<!-- end row-->