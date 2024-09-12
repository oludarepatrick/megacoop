<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- end auth-fluid-->

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box p-0">
        <div class="align-items-center d-flex h-100">
            <div class="card-body pt-5">
                <div class="text-center w-100 mt-5 ">
                    <h1 class="mdi mdi-lock-reset font-26"></h1>
                    <h4 class="mt-2 mb-5"><?= lang('forget_password') ?></h4>
                </div>
                <!-- form -->
                <div id="auth_root">
                    <?= form_open() ?>
                    <div class="form-group ">
                        <label for="password">Email Address</label>
                        <p>Enter your email Address</p>
                        <div id="err" class="text-danger"></div>
                        <div class="input-group input-group-merge">
                            <input type="email" name="identity" class="form-control form-control-lg" id="identity" required="" placeholder="<?= lang('email') ?>">
                            <input type="hidden" id="action" value="forget_pass">
                        </div>
                    </div>

                    <div class="form-group mb-0 mt-3 text-center">
                        <button class="btn btn-primary btn-block btn-lg" type="button" id="login" onclick="auth_start()"><i class="mdi mdi-account-search-outline"></i> Let's Go! </button>
                        <button id="auth" style="display: none" class="btn btn-primary btn-lg btn-block" type="button" disabled>
                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                            <span class="">Hold on please...</span>
                        </button>
                    </div>

                    <?= form_close() ?>
                </div>

                <footer class="footer footer-alt">
                    <p class="text-muted">Already have an account?
                        <a data-fancy href="<?= base_url('auth') ?>"><b><?= lang('login') ?></b></a>
                    </p>
                </footer>
            </div>
        </div>
    </div>
    <!-- end auth-fluid-form-box-->

    <!-- Auth fluid right content -->
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
    <!-- end Auth fluid right content -->
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
                        <a href="" data-toggle="modal" data-target="#fill-primary-modal" class="text-muted float-right"><small>Forgot your password?</small></a>
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