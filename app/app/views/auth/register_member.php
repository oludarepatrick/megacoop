<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- end auth-fluid-->

<div class="auth-fluid">
    <!--Auth fluid left content -->
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body p-0">
                <!-- Logo -->
                <div class="text-center">
                    <a href="" class="logo-dark">
                        <span>
                            <?php if ($coop->logo) { ?>
                                <img src="<?= $assets ?>images/logo/coop/<?= $coop->logo ?>" alt="" height="38"> 
                            <?php } ?>
                            <?php if (!$coop->logo) { ?>
                                <img src="<?= $assets ?>images/logo/coopify/default_logo.png" alt="" height="38"> 
                            <?php } ?><br><br>
                            <strong class="text-dark font-18"> <?= $coop->coop_name ?></strong>
                        </span>
                    </a>
                </div>
                <!-- title-->
                <!--<h4 class="mt-5"><?= lang('register') ?></h4>-->
                <p class="text-muted text-center mb-4 mt-2"><?= lang('mem_reg_note') ?></p>

                <!-- form -->
                <?= form_open('register/'.$coop->url, 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 mt-3">
                        <label for="<?= lang('first_name') ?>"><?= lang('first_name') ?></label>
                        <input type="text" name="first_name" class="form-control" id="first_name" value="<?= set_value('first_name') ?>" required>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
                    <div class=" col-md-6 mt-3">
                        <label for="<?= lang('last_name') ?>"><?= lang('last_name') ?></label>
                        <input type="text" name="last_name" class="form-control" id="last_name" value="<?= set_value('last_name') ?>" required>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
                    
                    <div class=" col-md-6 mt-3">
                        <label for="<?= lang('email') ?>"><?= lang('email') ?></label>
                        <input type="email" name="email" class="form-control" id="email" value="<?= set_value('email') ?>" required>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
                    <div class=" col-md-6 mt-3">
                        <label for="<?= lang('phone') ?>"><?= lang('phone') ?></label>
                        <input type="number" name="phone" class="form-control" id="phone" value="<?= set_value('phone') ?>" required>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
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
                </div>
                <div class="row mb-3">
                    <div class=" col-md-6 mt-3">
                        <label for="<?= lang('acc_name') ?>"><?= lang('acc_name') ?></label>
                        <input type="text" name="acc_name" class="form-control" id="acc_name" value="<?= set_value('acc_name') ?>" required>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
                    <div class=" col-md-6 mt-3">
                        <label for="<?= lang('acc_no') ?>"><?= lang('acc_no') ?></label>
                        <input type="text" name="acc_no" class="form-control" id="acc_no" value="<?= set_value('acc_no') ?>" required>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
                    <div class=" col-md-12 mt-3">
                        <label for="bank_id"><?= lang('bank') ?></label>
                        <?php
                        $bk[""] = "Select";
                        foreach ($banks as $b) {
                            $bk[$b->id] = $b->bank_name;
                        }
                        ?>
                        <?= form_dropdown('bank_id', $bk, set_value('bank_id'), 'class="form-control select2" id="bank_id" data-toggle="select2"'); ?>
                        <div class="valid-tooltip">
                            <?= lang('looks_good') ?>
                        </div>
                        <div class="invalid-tooltip">
                            <?= lang('field_required') ?>
                        </div>
                    </div>
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