<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4> <?= lang('req') ?>  <?= lang('withdrawal') ?></h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="dripicons-wrong mr-2"></i> <span id="error"></span>
                </div>
                <?= form_open('member/withdrawal/request', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                        <input type="text" name="member_id" readonly value="<?= set_value('member_id', $this->user->username) ?>" id="member_id" class="form-control"  required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="savings_type"><?= lang('withdraw_from') ?></label>
                        <?php
                        $stt[""] = lang('select') . ' ' . lang('savings_type');
                        foreach ($savings_type as $st) {
                            $stt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('savings_type', $stt, set_value('savings_type'), ' onchange="get_withdrawal_info()"class="form-control select2"  id="savings_type" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="exixting-loans" style="display: none;">
                    <div class="card widget-flat cta-box bg-primary">
                        <div class="card-body ">
                            <div class="float-right">
                                <i class="mdi mdi-cart-plus widget-icon bg-light-lighten text-white"></i>
                            </div>
                            <h5 class="text-white font-weight-normal mt-0" title="<?= lang('savings') ?>"><?= lang('savings') ?></h5>
                            <h3 class="mt-3 mb-3 text-white" id="bal"></h3>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap text-white"><?= lang('total_savings') . ' ' . lang('balance') ?></span>
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" class="form-control" value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="000,000,000,000,000.00" data-reverse="true">
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button  type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('request') ?></button>
            </div>
            <?= form_close() ?>
        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end card-->
<!-- end row-->