<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- end auth-fluid-->

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body p-0">
                <!-- Logo -->
                <div class="auth-brand text-center text-lg-left">
                    <a href="" class="logo-dark">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38"> </span>
                    </a>
                    <a href="" class="logo-light">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38">  </span>
                    </a>
                </div>
                <!-- title-->
                <h4 class="mt-5"><?= lang('register') ?></h4>
                <p class="text-muted mb-4"><?= lang('reg_note') ?></p>

                <!-- form -->
                <?= form_open('auth/register') ?>
                <div class="form-group">
                    <label for="coop_name"><?= lang('coop_name') ?></label>
                    <input name="coop_name" class="form-control" type="text" id="coop_name"  value="<?= set_value('coop_name') ?>" placeholder="<?= lang('coop_name') ?>">
                </div>
                <div class="form-group">
                    <label for="identity"><?= lang('identity') ?></label>
                    <input name="identity" class="form-control" type="text" id="identity"  value="<?= set_value('identity') ?>" placeholder="Your <?= lang('identity') ?> Address">
                </div>
                <div class="form-group">
                    <label for="password"><?= lang('password') ?></label>
                    <div class="input-group input-group-merge">
                        <input type="password" name="password" id="password" class="form-control" placeholder="<?= lang('password') ?>">
                        <div class="input-group-append" data-password="false">
                            <div class="input-group-text">
                                <span class="password-eye"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="coop_type"><?= lang('coop_type') ?></label>
                    <div class="input-group">
                        <select class="custom-select" id="coop_type" name="coop_type">
                            <option  value="" >Choose...</option>
                            <?php foreach ($coop_types as $ct) { ?>
                                <option value="<?= $ct->id ?>" <?= set_select('coop_type', $ct->id) ?>><?= $ct->name ?></option>
                            <?php } ?>
                        </select>
                        <div class="input-group-append">
                            <div class="btn-group mb-2 dropup">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    help
                                </button>
                                <div class="dropdown-menu">
                                    <div class="dropdown-divider"></div>
                                    <?php foreach ($coop_types as $ct2) { ?>
                                        <b class="dropdown-item font-weight-bolder text-primary"><?= $ct2->name ?>:</b>
                                        <a class="dropdown-item"><?= $ct2->description ?></a>
                                        <div class="dropdown-divider"></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label for="referrer_code"><?= lang('referrer_code') ?> <span class="text-muted"> (Optional)</span></label>
                    <input name="referrer_code" class="form-control" type="text" id="referrer_code"  value="<?= set_value('referrer_code') ?>" placeholder="<?= lang('referrer_code') ?>">
                </div>
                <div class="form-group m-0 p-0">
                    <input name="h_pot" class="form-control-sm form-control border-0" type="text" >
                </div>
                <button class="btn btn-primary btn-block" type="submit" id="login" onclick="auth_loader()"><i class=" mdi mdi-account-edit-outline"></i> <?= lang('register') ?> </button>
                <button id="auth" style="display: none" class="btn btn-primary btn-block" type="button" disabled >
                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                    <span class="">Creating Account...</span>
                </button>
                <?= form_close() ?>
                <div class="text-center mt-2">
                    <p class="text-muted">Already have an account?
                        <a data-fancy href="<?= base_url('auth') ?>" ><b><?= lang('login') ?></b></a>
                    </p>
                </div>

            </div> <!-- end .card-body -->
        </div> <!-- end .align-items-center.d-flex.h-100-->
    </div>

    <!-- Auth fluid right content -->
    <div class="auth-fluid-right text-center">
        <div class="auth-user-testimonial">
            <h2 class="mb-3">I love <?= $app_settings->app_name ?>!</h2>
            <p class="lead"><i class="mdi mdi-format-quote-open"></i> It's an elegant cooperative tool. I love it very much! . <i class="mdi mdi-format-quote-close"></i>
            </p>
            <p>
                - Abdulrazaq M Biodun
            </p>
        </div> <!-- end auth-user-testimonial-->
    </div>
    <!-- end Auth fluid right content -->
</div>

<!-- Warning Alert Modal -->
<!--<button  type="button" class="btn btn-warning" data-toggle="modal" data-target="#warning-alert-modal">Warning Alert</button>-->
<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2"><?= lang('important_info') ?></h4>
                    <p class="mt-3"><?= lang('important_info2') ?>  <?= $app_settings->app_name ?>.</p>
                    <a data-fancy href="<?= base_url('account') ?>" class="btn btn-warning my-2" ><i class=" mdi mdi-check-all mr-1"></i> Yes I'm an Exco</a>
                    <button type="button" class="btn btn-outline-warning my-2" data-dismiss="modal">
                        <i class="mdi mdi-window-close"></i> 
                    </button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="fill-primary-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fill-primary-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-filled bg-primary">
            <div class="modal-header">
                <h4 class="modal-title" id="fill-primary-modalLabel"><?= lang('reset_password') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
                <p class="text-white mb-4">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                <form action="#">
                    <div class="form-group mb-3">
                        <label for="emailaddress">Email address</label>
                        <input class="form-control" type="email" id="emailaddress" required="" placeholder="Enter your email">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal"><i class="mdi mdi-window-close"></i> </button>
                <button type="button" class="btn btn-outline-light"> <i class="mdi mdi-lock-reset mr-1"></i> <?= lang('reset_password') ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->