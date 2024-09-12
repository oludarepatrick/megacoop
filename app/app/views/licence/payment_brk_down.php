<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="payment_break_down" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <?php if (!$active_licence) { ?>
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel"><?= lang('payment_break_down') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="card-body rounded bg-primary cta-box text-white">
                        <div class="float-left">
                            <h5><?= lang('start_date') ?></h5>
                            <span class="font-16"> <i class="uil-calendar-alt"> </i> <?= $start_date ?> </span>
                        </div>
                        <div class="float-right">
                            <h5><?= lang('end_date') ?></h5>
                            <span class="font-16"> <i class="uil-calendar-alt"> </i> <?= $end_date ?> </span>
                        </div>
                        <span class=" clearfix"></span>
                    </div>
                    <div class="card-body">
                        <div class="">
                            <span class="font-18 float-left"> <?= lang('total_members') ?> </span>
                            <h4 class="float-right"><?= $total_member ?></h4>
                            <span class=" clearfix"></span>
                        </div>
                        <div class="">
                            <span class="font-18 float-left"><?= lang('rate') ?> </span>
                            <h4 class="float-right"><?= number_format($rate, 2) ?></h4>
                            <span class=" clearfix"></span>
                        </div>
                        <div class="">
                            <span class="font-18 float-left"> <?= lang('tranx_fee') ?> </span>
                            <h4 class="float-right"><?= number_format($fee, 2) ?></h4>
                            <span class=" clearfix"></span>
                        </div>
                        <hr>
                        <div class=" mb-2">
                            <span class="font-18 float-left"><?= lang('totals') ?> </span>
                            <h4 class="float-right"><?= number_format($total, 2) ?></h4>
                            <span class=" clearfix"></span>
                        </div>
                        <a data-fancy href="<?= base_url("licence/pay_with_paystack/" . $this->utility->mask($licence_cat_id)) ?>" class="btn btn-primary btn-block">Proceed</a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">Increase Member</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <?= form_open('licence/extended', '') ?>
                <div class="modal-body">
                    <div class="card-body rounded bg-primary cta-box text-white">
                        <div class="">
                            <span class="font-18 float-left"> <?= lang('calc_months') ?> </span>
                            <h4 class="float-right" id="months"><?= $calc_month ?> months</h4>
                            <span class=" clearfix"></span>
                        </div>
                        <div class="">
                            <span class="font-18 float-left"> <?= lang('amount') ?> per member</span>
                            <h4 class="float-right"><?= $licence[0]->amount ?></h4>
                            <span class=" clearfix"></span>
                        </div>

                        <div class="">
                            <span class="font-18 float-left"> <?= lang('total') . ' ' . lang('member') ?></span>
                            <h4 class="float-right" id="total_member">0</h4>
                            <span class=" clearfix"></span>
                        </div>
                        <div class="">
                            <span class="font-18 float-left"> <?= lang('total') ?></span>
                            <h4 class="float-right" id="total">0 x <?= $calc_month ?> x <?= $licence[0]->amount ?></h4>
                            <span class=" clearfix"></span>
                        </div>
                        <hr>
                        <div class="">
                            <h3 class=" float-left"> <?= lang('total').' '. lang('amount') ?></h3>
                            <h3 class="float-right" id="total-two">0.00</h3>
                            <span class=" clearfix"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class=" col-md-12 form-group">
                            <label for="<?= lang('member') ?>"><?= lang('how_many_member') ?> </label>
                            <input type="number" min="1" oninput="extend_licence(this, '<?= $calc_month ?>', '<?= $licence[0]->amount ?>' )" name="member" id="member" class="form-control text-center font-24" value="<?= set_value('member') ?>" required>
                            <input type="hidden" name="licence_cat_id" value="<?= $licence_cat_id ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-block"><i class="mdi mdi-check mr-1"></i><?= lang('extend') ?></button>
                </div>
                <?= form_close() ?>
            <?php } ?>
        </div>
    </div>
</div>