<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- end auth-fluid-->

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">

                <!-- Logo -->
                <div class="auth-brand text-center text-lg-left">
                    <a href="" class="logo-dark">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38"> <strong class="text-dark font-18"> </strong></span>
                    </a>
                    <a href="" class="logo-light">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38"> <strong class=" text-dark font-18"> </strong></span>
                    </a>
                </div>

                <!-- title-->
                <h4 class="mt-0"><?= lang('login') ?></h4>
                <p class="text-muted mb-4"><?= lang('login_note') ?></p>

                <!-- form -->
                <?= form_open('auth/login') ?>
                <div class="form-group">
                    <label for="emailaddress"><?= lang('email_memberid') ?></label>
                    <input name="identity" class="form-control" type="text" id="emailaddress" required="" placeholder="<?= lang('email_memberid') ?>">
                </div>
                <div class="form-group">
                    <a href="" data-toggle="modal" data-target="#fill-primary-modal" class="text-muted float-right"><small>Forgot your password?</small></a>
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
                <div class="form-group mb-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkbox-signin">
                        <label class="custom-control-label" for="checkbox-signin">Remember me</label>
                    </div>
                </div>
                <div class="form-group mb-0 text-center">
                    <button class="btn btn-primary btn-block" type="submit" id="login" onclick="auth_loader()"><i class="mdi mdi-login"></i> <?= lang('login') ?> </button>
                    <button id="auth" style="display: none" class="btn btn-primary btn-block" type="button" disabled>
                        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                        <span class="">Authentication...</span>
                    </button>
                </div>
                <!-- social-->
                <!-- <div class="text-center mt-4">
                    <p class="text-muted font-16">Sign in with</p>
                    <ul class="social-list list-inline mt-3">
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-primary text-primary"><i class="mdi mdi-facebook"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i class="mdi mdi-google"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-info text-info"><i class="mdi mdi-twitter"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript: void(0);" class="social-list-item border-secondary text-secondary"><i class="mdi mdi-github-circle"></i></a>
                        </li>
                    </ul>
                </div> -->
                <?= form_close() ?>
                <!-- end form-->

                <!-- Footer-->
                <footer class="footer footer-alt">
                    <p class="text-muted">Don't have an account?
                        <a href="" data-toggle="modal" data-target="#warning-alert-modal"><b><?= lang('register') ?></b></a>
                    </p>
                </footer>

            </div>
        </div>
    </div>

    <div class="auth-fluid-right text-center">
        <div class="auth-user-testimonial">
            <h2 class="mb-3"> Cooperative management simplified </h2>
            <p class="lead"><i class="mdi mdi-format-quote-open"></i> Sign up today to have awesome experience..<i class="mdi mdi-format-quote-close"></i>
            </p>
            
        </div> 
    </div>
</div>

<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-primary"></i>
                    <h4 class="mt-2 text-primary"><?= lang('important_info') ?></h4>
                    <p class="mt-3"><?= lang('important_info2') ?> <?= $app_settings->app_name ?>.</p>
                    <a data-fancy href="<?= base_url('auth/register') ?>" class="btn btn-primary my-2" id="action_btn" onclick="wait_loader()"><i class=" mdi mdi-check-all mr-1"></i> Yes I'm an Exco</a>
                    <button style="display: none;" id="wait" class="btn btn-primary" type="button" disabled>
                        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                        <span class="mr-1 ml-1">Please wait...</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary my-2" data-dismiss="modal">
                        <i class="mdi mdi-window-close"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fill-primary-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fill-primary-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-filled bg-primary">
            <div class="modal-header">
                <h4 class="modal-title" id="fill-primary-modalLabel"><?= lang('reset_password') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <?= form_open('auth/forgot_password') ?>
            <div class="modal-body">
                <div class="card-body">
                    <p class="text-white mb-4">Enter your email address and we'll send you an email with instructions to reset your password.</p>

                    <div class="form-group mb-3">
                        <label for="emailaddress">Email address</label>
                        <input class="form-control" type="email" id="emailaddress" name="identity" required="" placeholder="Enter your email">
                    </div>

                </div>
            </div>
            <div class="modal-header">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal"><i class="mdi mdi-window-close"></i> </button>
                <button type="submit" class="btn btn-outline-light"> <i class="mdi mdi-lock-reset mr-1"></i> <?= lang('reset_password') ?></button>
            </div>
            </form>
        </div>
    </div>
</div>