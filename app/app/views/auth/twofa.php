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
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38"> <strong class="text-dark font-18"> <?= $app_settings->app_name ?></strong></span>
                    </a>
                    <a href="" class="logo-light">
                        <span><img src="<?= $assets ?>images/logo/<?= $app_settings->logo ?>" alt="" height="38"> <strong class=" text-dark font-18"> <?= $app_settings->app_name ?></strong></span>
                    </a>
                </div>

                <!-- title-->
                <div class="text-center w-75 m-auto">
                    <img src="<?= $assets ?>/images/users/<?= $user->avatar ?>" height="84" alt="user-image" class="rounded border">
                    <h4 class="text-dark-50 text-center mt-3 font-weight-bold text-uppercase">Hi ! <?= $user->first_name ?> </h4>
                    <p class="text-muted mb-4">Enter the token sent to your email</p>
                </div>

                <!-- form -->
                <?= form_open('auth/twofa') ?>
                <div class="form-group">
                    <label for="password"> <?= lang('token') ?></label>
                    <div class="input-group input-group-merge">
                        <input data-toggle="input-mask" data-mask-format="00-00-00" type="text" name="token" id="token" class="form-control text-center" placeholder="Enter token here">
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
                <div class="text-center mt-4">
                    <p class="text-muted font-16">Email not received ? </p>
                    <a class=" btn btn-outline-primary" data-fancy href="<?= base_url('auth/logout') ?>">Try Again</a>
                </div>
                <?= form_close() ?>

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