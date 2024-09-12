<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body bg-gen">
                        <div class="float-right">
                            <span><img class="rounded-circle shadow-sm" src="<?= $assets ?>images/brands/paystack.jpeg" alt="" height="36"></span>
                        </div>
                        <!--<h5 class="text-muted font-weight-normal mt-0" title="Growth"><?= lang('dr') ?></h5>-->
                        <h3 class="mt-3 mb-2 text-primary"><?= lang('paystack') ?></h3>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= lang('fee')?>
                                <span class=""><?=(number_format($payment->fee, 2))?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= lang('amount')?>
                               <span class=""><?=(number_format($payment->amount, 2))?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= lang('total')?>
                                <span class=""><?=(number_format($payment->total_amount, 2))?></span>
                            </li>
                        </ul>

                    </div>
                        <a data-fancy href="<?= base_url('subscription/pay_with_paystack') ?>" class="btn btn-primary float-right"><i class="uil-check mr-1"></i> <?= lang('pay_with').' '.lang('paystack')?></a>
                </div>
            </div>
           
        </div>
    </div>
</div>