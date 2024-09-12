<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">

                <!-- Logo -->
                <div class="auth-brand text-center text-lg-left">
                    <a href="" class="logo-dark">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38"> <strong class="text-dark font-18"> <?= $app_settings->app_name ?></strong></span>
                    </a>
                    <a href="" class="logo-light">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38">  <strong class=" text-dark font-18"> <?= $app_settings->app_name ?></strong></span>
                    </a>
                </div>
                <h4 class="mt-0"><?= lang('reset_password') ?></h4>
                <p class="text-muted mb-4"><?= lang('reset_password_note') ?></p>

                <!-- form -->
                <?= form_open('auth/reset_password/'.$code) ?>
                <div class="form-group">
                    <label for="new_pass"><?= lang('new_pass') ?></label>
                    <div class="input-group input-group-merge">
                        <input type="password" name="new_pass" id="new_pass" class="form-control" placeholder="<?= lang('new_pass') ?>">
                        <div class="input-group-append" data-password="false">
                            <div class="input-group-text">
                                <span class="password-eye"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_confirm"><?= lang('new_confirm') ?></label>
                    <div class="input-group input-group-merge">
                        <input type="password" name="new_confirm" id="new_confirm" class="form-control" placeholder="<?= lang('new_confirm') ?>">
                        <div class="input-group-append" data-password="false">
                            <div class="input-group-text">
                                <span class="password-eye"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-center mt-2">
                    <button class="btn btn-primary btn-block" type="submit" id="login" onclick="auth_loader()"><i class="mdi mdi-shield-lock-outline"></i> <?= lang('reset_pass') ?> </button>
                    <button id="auth" style="display: none" class="btn btn-primary btn-block" type="button" disabled >
                        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                        <span class="">Please wait...</span>
                    </button>
                </div>
                
                <?= form_close() ?>
                <!-- end form-->

                <!-- Footer-->
                <footer class="footer footer-alt">
                    <p class="text-muted">Already have an account?
                        <a data-fancy href="<?= base_url('')?>"><b><?= lang('login') ?></b></a>
                    </p>
                </footer>
            </div> 
        </div>
    </div>

    <div class="auth-fluid-right text-center">
        <div class="auth-user-testimonial">
            <h2 class="mb-3">I love <?= $app_settings->app_name ?>!</h2>
            <p class="lead"><i class="mdi mdi-format-quote-open"></i> It's an elegant cooperative tool. I love it very much! . <i class="mdi mdi-format-quote-close"></i>
            </p>
            <p>
                - Abdulrazaq M Biodun
            </p>
        </div> 
    </div>

</div>
