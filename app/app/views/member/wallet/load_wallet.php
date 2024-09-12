<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="card ">
            <div class="card-header bg-gen">
                <h4><?= lang('load') . ' ' . lang('wallet') ?></h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="dripicons-wrong mr-2"></i> <span id="error"></span>
                </div>
                <?= form_open('member/wallet/load_wallet', 'class="needs-validation" novalidate') ?>
                <div class="form-group mb">
                    <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                    <input type="text" name="amount" maxlength="15"  class="form-control" id="amount" required value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                </div>
                <div class="card widget-flat bg-primary cta-box text-white" id="payment_breakdown" style="display: none;">
                    <div id="spinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="card-body">
                        <div class=" d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge badge-light">Amount</span><br>
                                <strong id="amount-one"></strong>
                            </span>
                            <span>
                                <span class="badge badge-light">Fee</span><br>
                                <strong id="fee"></strong>
                            </span>
                            <span></span>
                        </div>
                        <div class=" d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <h5 class="font-weight-normal mt-0" title="Revenue">Total</h5>
                                <h3 class=" mt-0 text-white" id="total"></h3>
                            </div>
                            <i class="mdi mdi-thumb-up widget-icon bg-light-lighten rounded-circle text-white"></i>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mt-3">
                        <div class="card-body border rounded border-primary">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="paystack" onclick="get_payment_break_down(this)" name="gate_way" value="1" class="custom-control-input">
                                <label class="custom-control-label" for="paystack"> Pay with <?= lang('paystack') ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-3">
                        <div class="card-body border rounded border-primary">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="flutter" onclick="get_payment_break_down(this)" name="gate_way" class="custom-control-input" value="2">
                                <label class="custom-control-label" for="flutter"> Pay with <?= lang('flutter_wave') ?></label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('load') . ' ' . lang('wallet') ?></button>
            </div>
            <?= form_close() ?>
            <!-- end row -->
        </div>
    </div>
</div>