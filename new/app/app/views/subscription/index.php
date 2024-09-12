<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row justify-content-center">
    <div class="col-xl-10">

        <!-- Pricing Title-->
        <div class="text-center">
            <h3 class="mb-2">Our Plans and Pricing</h3>
            <p class="text-muted w-50 m-auto">
                We have plans and prices that fit your cooperative perfectly. No hidden charges.
            </p>
        </div>

        <!-- Plans -->
        <div class="row mt-sm-5 mt-3 mb-3 justify-content-center">
            <div class="col-md-5">
                <?php if($this->coop->subscription_cat_id == 1) {?>
                    <div class="card card-pricing card-pricing-recommended">
                        <div class="card-body text-center">
                        <div class="card-pricing-plan-tag"><?= lang('active_subs')?></div>
                <?php } else { ?>
                     <div class="card card-pricing">
                        <div class="card-body text-center">
               <?php } ?>
                        <p class="card-pricing-plan-name font-weight-bold text-uppercase"><?=$subscriptions[0]->name?></p>
                        <i class="card-pricing-icon dripicons-user text-primary"></i>
                        <h2 class="card-pricing-price"><?= number_format($subscriptions[0]->amount, 2)?> <span>/ <?=lang('member')?></span></h2>
                        <ul class="card-pricing-features">
                            <li>Add and Manage Members</li>
                            <li>Monitor cooperative health</li>
                            <li>Dedicate multiple roles and access</li>
                            <li>Manage loan requests</li>
                            <li>0-10 Members</li>
                        </ul>
                        <button class="btn btn-primary mt-4 mb-2"><?= lang('choose_plan')?></button>
                    </div>
                </div> 
            </div> 

            <div class="col-md-5">
                <?php if($this->coop->subscription_cat_id == 2) {?>
                    <div class="card card-pricing card-pricing-recommended">
                        <div class="card-body text-center">
                        <div class="card-pricing-plan-tag"><?= lang('active_subs')?></div>
                <?php } else { ?>
                     <div class="card card-pricing">
                        <div class="card-body text-center">
               <?php } ?>
                        <p class="card-pricing-plan-name font-weight-bold text-uppercase"><?=$subscriptions[1]->name?></p>
                        <i class="card-pricing-icon dripicons-user text-primary"></i>
                        <h2 class="card-pricing-price"><?= number_format($subscriptions[1]->amount, 2)?> <span>/ <?=lang('member')?></span></h2>
                        <ul class="card-pricing-features">
                            <li>Add and Manage Members</li>
                            <li>Monitor cooperative health</li>
                            <li>Dedicate multiple roles and access</li>
                            <li>Manage loan requests</li>
                            <li>Unlimited Members</li>
                        </ul>
                        <a href="#" data-toggle="modal" data-target="#payment" class="btn btn-primary mt-4 mb-2"><?= lang('choose_plan')?></a>
                    </div>
                </div> 
            </div>
        </div>
    </div> 
</div>

<div id="payment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-gen">
            <div class="modal-body">
                <div class="text-center mt-2 mb-4">
                    <a href="index.html" class="text-success">
                        <span><img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="40"></span>
                    </a>
                </div>

                <?= form_open('subscription/gateway')?>

                    <div class="form-group">
                        <label for="emailaddress1"><?= lang('how_many_mem')?></label>
                        <input class="form-control form-control-lg" type="number" name="members" id="members" required="" placeholder="e.g 50">
                    </div>

                    <div class="form-group text-center mt-5">
                        <button class="btn btn-primary" type="submit"><?= lang('continue_to_payment')?></button>
                    </div>

                <?= form_close()?>

            </div>
        </div>
    </div>
</div>