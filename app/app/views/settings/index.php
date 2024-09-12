<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ($this->coop->status == 'processing') { ?>
    <div class="row">
        <div class="col">
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="spinner-grow text-success" role="status"></div>
                <h4 class="alert-heading">Hello <?= ucwords($this->coop->contact_name) ?></h4>
                <p>Yes! You are almost done setting up your cooperatives. Kindly fill the form provided in the <b>basic</b> tab as below</p>
                <hr>
                <p class="mb-0">1. Cooperative information</p>
                <p class="mb-0">2. Cooperative Account information</p>
                <p class="mb-0">3. Contact Person information</p>
            </div>
        </div>
    </div>
<?php } ?>
<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card text-center">
            <div class="card-body">
                <?php if (!$this->coop->logo) { ?>
                    <img src="<?= $assets ?>images/logo/coopify/default_logo.png" class="rounded-circle avatar-lg img-thumbnail" alt="coop_logo">
                <?php } ?>

                <?php if ($this->coop->logo) { ?>
                    <img src="<?= $assets ?>images/logo/coop/<?= $this->coop->logo ?>" class="rounded-circle avatar-lg img-thumbnail" alt="coop_logo">
                <?php } ?>

                <h4 class="mb-0 mt-2"><?= lang('coop_logo') ?></h4>
                <p class="text-muted font-14"><?= $this->coop->coop_name ?></p>

                <!--<button type="button" class="btn btn-success btn-sm mb-2">Follow</button>-->
                <button type="button" id="click_on_load" class="btn  btn-sm mb-2 btn-outline-primary" data-toggle="modal" data-target="#bottom-modal">
                    <i class="mdi mdi-upload mr-1"></i><?= lang('change_logo') ?>
                </button>

                <div class="text-left mt-3">
                    <h4 class="font-13 text-uppercase"><?= lang('contact_person') ?> :</h4>
                    <p class="text-muted font-13 mb-3">
                        This section contains information of the cooperative exco to be contacted if needed. kindly note that this
                        could be edited using the form in the settings.
                    </p>
                    <p class="text-muted mb-2 font-13"><strong><?= lang('full_name') ?>:</strong> <span class="ml-2"><?= $this->coop->contact_name ?></span></p>

                    <p class="text-muted mb-2 font-13"><strong><?= lang('phone') ?> :</strong><span class="ml-2"><?= $this->coop->contact_phone ?></span></p>

                    <p class="text-muted mb-2 font-13"><strong><?= lang('email') ?> :</strong> <span class="ml-2 "><?= $this->coop->contact_email ?></span></p>

                    <p class="text-muted mb-1 font-13"><strong><?= lang('address') ?> :</strong> <span class="ml-2"><?= $this->coop->contact_address ?></span></p>
                </div>

                <ul class="social-list list-inline mt-3 mb-0">
                    <li class="list-inline-item">
                        <a href="https://wa.me/<?= $this->coop->contact_phone ?>?text=Hello!" class="social-list-item border-success text-success"><i class="mdi mdi-whatsapp"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="mailto:<?= $this->coop->contact_email ?>" class="social-list-item border-danger text-danger"><i class="mdi mdi-gmail"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="tel:<?= $this->coop->contact_phone ?>" class="social-list-item border-secondary text-secondary"><i class="mdi mdi-phone"></i></a>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                    <li class="nav-item">
                        <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                            <?= lang('basic') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#payment" data-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                            <?= lang('payment') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#notification" data-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                            <?= lang('notification') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#loan" data-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                            <?= lang('loan') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#others" data-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                            <?= lang('others') ?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="settings">
                        <?= form_open('settings') ?>
                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle mr-1"></i><?= lang('coop_info') ?></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="coop_name"><?= lang('coop_name') ?></label>
                                    <input type="text" class="form-control" value="<?= set_value('coop_name', $this->coop->coop_name) ?>" name="coop_name" id="coop_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reg_no"><?= lang('reg_no') ?></label>
                                    <input type="text" class="form-control" name="reg_no" value="<?= set_value('reg_no', $this->coop->reg_no) ?>" id="reg_no" placeholder="<?= lang('reg_no') ?>">
                                </div>
                            </div> <!-- end col -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reg_no"><?= lang('coop_initials') ?></label>
                                    <input type="text" class="form-control" name="coop_initial" value="<?= set_value('coop_initial', $this->coop->coop_initial) ?>" id="coop_initial" placeholder="E.g PMC">
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="coop_address"><?= lang('coop_address') ?></label>
                                    <textarea class="form-control" id="coop_address" name="coop_address" rows="4" placeholder=""><?= $this->coop->coop_address ?></textarea>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country"><?= lang('country') ?></label>
                                    <?php
                                    $ct[""] = lang('select') . ' ' . lang('country');
                                    foreach ($country as $c) {
                                        $ct[$c->id] = $c->name;
                                    }
                                    ?>
                                    <?= form_dropdown('country_id', $ct, set_value('country_id', $this->coop->country_id), 'class="form-control select2" id="country" onchange="change_country()" data-toggle="select2"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state"><?= lang('state') ?></label>
                                    <select class="form-control select2" name="state_id" id="state" onchange="change_state()" data-toggle="select2">
                                        <?php if ($mystate) { ?>
                                            <option value="<?= $mystate->id ?>"><?= $mystate->name ?></option>
                                        <?php } ?>
                                        <?php foreach ($state as $stat) { ?>
                                            <option value="<?= $stat->id ?>"><?= $stat->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city"><?= lang('city') ?></label>
                                    <select class="form-control select2" name="city_id" id="city" data-toggle="select2">
                                        <?php if ($mycity) { ?>
                                            <option value="<?= $mycity->id ?>"><?= $mycity->name ?></option>
                                        <?php } ?>
                                        <?php foreach ($city as $cty) { ?>
                                            <option value="<?= $cty->id ?>"><?= $cty->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="url"><?= lang('url') ?></label>
                                    <?php if (!$this->coop->url) { ?>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= base_url('register/') ?></span>
                                            </div>
                                            <input type="text" class="form-control" id="url" name="url" value="<?= set_value('url', $this->coop->url) ?>" placeholder="eg democoop">
                                        </div>
                                        <span class="form-text text-danger">
                                            <small>If you set your url value to <b>democoop,</b> then your members would be able to join your cooperative
                                                at <b><?= base_url('register/democoop') ?></b></small>
                                        </span>
                                    <?php } ?>

                                    <?php if ($this->coop->url) { ?>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="url" readonly value="<?= set_value('url', base_url('register/' . $this->coop->url)) ?>" placeholder="eg democoop">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn" onclick="copy_to_clipboard()">Copy</span>
                                            </div>
                                        </div>
                                        <span class="form-text text-danger">
                                            <small>Share this link with your cooperative members to enable them join</small>
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div> <!-- end row -->

                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="mdi mdi-office-building mr-1"></i> <?= lang('coop_acc_info') ?></h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="acc_name"><?= lang('acc_name') ?></label>
                                    <input type="text" class="form-control" name="acc_name" value="<?= set_value('acc_name', $this->coop->acc_name) ?>" id="acc_name" placeholder="<?= lang('acc_name') ?>">
                                </div>
                            </div>
                        </div> <!-- end row -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="acc_no"><?= lang('acc_no') ?></label>
                                    <input type="number" class="form-control" name="acc_no" value="<?= set_value('acc_no', $this->coop->acc_no) ?>" id="acc_no" placeholder="<?= lang('acc_no') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank"><?= lang('bank') ?></label>
                                    <select onfocus="change_country()" class="form-control select2" name="bank_id" id="bank_id" data-toggle="select2">
                                        <?php if ($mybank) { ?>
                                            <option value="<?= $mybank->id ?>"><?= $mybank->bank_name ?></option>
                                        <?php } ?>
                                        <?php foreach ($bank as $bk) { ?>
                                            <option value="<?= $bk->id ?>"><?= $bk->bank_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div> <!-- end row -->

                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="mdi mdi-earth mr-1"></i> <?= lang('contact_person') ?></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_name"><?= lang('full_name') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account-box"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="contact_name" value="<?= set_value('contact_name', $this->coop->contact_name) ?>" id="contact_name" placeholder="<?= lang('contact_name') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email"><?= lang('email') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-gmail"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="contact_email" value="<?= set_value('contact_email', $this->coop->contact_email) ?>" id="contact_email" placeholder="<?= lang('email') ?>">
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_phone"><?= lang('phone') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="contact_phone" value="<?= set_value('contact_phone', $this->coop->contact_phone) ?>" id="contact_phone" placeholder="<?= lang('phone') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="contact_address"><?= lang('address') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-home-map-marker"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="contact_address" value="<?= set_value('contact_address', $this->coop->contact_address) ?>" id="contact_address" placeholder="<?= lang('address') ?>">
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <div class="text-right">
                            <button id="wait_save_btn" style="display: none" class="btn btn-primary mt-2" type="button" disabled>
                                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                <span class="mr-1 ml-1">Saving...</span>
                            </button>
                            <button type="submit" id="save_btn" onclick="wait_loader('save_btn', 'wait_save_btn')" class="btn btn-primary mt-2"><i class="mdi mdi-content-save"></i> <?= lang('save') ?></button>
                        </div>
                        </form>
                    </div>

                    <div class="tab-pane" id="payment">
                        <div class="border border-light rounded bg-light p-2 mb-3">
                            <div class="media">
                                <div class="media-body">
                                    <div class="spinner-grow text-danger" role="status"></div>
                                    <h5 class="m-0"><?= lang('what_will_i_do') ?></h5>
                                    <p class="text-muted"><small>Short note</small></p>
                                </div>
                            </div>
                            <p>This section allows you to configure your cooperative to process payment such as
                                savings, loan repayment, loan disbursement e.t.c <strong>online</strong>
                            </p>
                        </div>
                        <div class="border border-light rounded p-2 mb-3">
                            <div class="media">
                                <img class="mr-2 rounded-circle" src="<?= $assets ?>images/brands/paystack.jpeg" alt="<?= lang('paystack') ?>" height="32">
                                <div class="media-body">
                                    <h5 class="m-0"><?= lang('paystack') ?></h5>
                                    <p class="text-muted"> <?= lang('paystack_note') ?>
                                        <a href="https://paystack.com" class="btn btn-sm btn-outline-primary">Create Account</a>
                                    </p>
                                </div>
                            </div>
                            <div class="card-body bg-light">
                                <?= form_open('settings/paystack') ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="paystack_secrete"><?= lang('paystack_secrete') ?></label>
                                            <input type="text" class="form-control" value="<?= set_value('paystack_secrete', $this->coop->paystack_secrete) ?>" name="paystack_secrete" id="paystack_secrete">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="paystack_public"><?= lang('paystack_public') ?></label>
                                            <input type="text" class="form-control" name="paystack_public" value="<?= set_value('paystack_public', $this->coop->paystack_public) ?>" id="paystack_public">
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-md-12 mt-1">
                                        <div class="form-group">
                                            <?php if ($this->coop->paystack_status == 'on') { ?>
                                                <input type="checkbox" id="switch1" name="paystack_status" checked data-switch="bool" />
                                            <?php } else { ?>
                                                <input type="checkbox" id="switch1" name="paystack_status" data-switch="bool" />
                                            <?php } ?>
                                            <label for="switch1" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <button id="wait_save_btnps" style="display: none" class="btn btn-primary mt-2" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                            <span class="mr-1 ml-1">Saving...</span>
                                        </button>
                                        <button type="submit" id="save_btnps" onclick="wait_loader('save_btnps', 'wait_save_btnps')" class="btn btn-primary mt-2"><i class="mdi mdi-content-save"></i> <?= lang('save') ?></button>
                                    </div>
                                </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                        <div class="border border-light rounded p-2 mb-3">
                            <div class="media">
                                <img class="mr-2 rounded-circle" src="<?= $assets ?>images/brands/flutterwave.png" alt="<?= lang('flutter') ?>" height="32">
                                <div class="media-body">
                                    <h5 class="m-0"><?= lang('flutter') ?></h5>
                                    <p class="text-muted"> <?= lang('flutter_note') ?>
                                        <a href="https://flutterwave.com/us/" class="btn btn-sm btn-outline-primary">Create Account</a>
                                    </p>
                                </div>
                            </div>
                            <div class="card-body bg-light">
                                <?= form_open('settings/flutter') ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="flutter_secrete"><?= lang('flutter_secrete') ?></label>
                                            <input type="text" class="form-control" value="<?= set_value('flutter_secrete', $this->coop->flutter_secrete) ?>" name="flutter_secrete" id="flutter_secrete">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="flutter_public"><?= lang('flutter_public') ?></label>
                                            <input type="text" class="form-control" name="flutter_public" value="<?= set_value('flutter_public', $this->coop->flutter_public) ?>" id="flutter_public">
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-md-12 mt-1">
                                        <div class="form-group">
                                            <?php if ($this->coop->flutter_status == 'on') { ?>
                                                <input type="checkbox" id="flutter_status" name="flutter_status" checked data-switch="bool" />
                                            <?php } else { ?>
                                                <input type="checkbox" id="flutter_status" name="flutter_status" data-switch="bool" />
                                            <?php } ?>
                                            <label for="flutter_status" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <button id="wait_save_btnpp" style="display: none" class="btn btn-primary mt-2" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                            <span class="mr-1 ml-1">Saving...</span>
                                        </button>
                                        <button type="submit" id="save_btnpp" onclick="wait_loader('save_btnpp', 'wait_save_btnpp')" class="btn btn-primary mt-2"><i class="mdi mdi-content-save"></i> <?= lang('save') ?></button>
                                    </div>
                                </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="notification">
                        <div class="border border-light rounded p-2 mb-3">
                            <div class="media">
                                <div class="media-body">
                                    <h5 class="m-0"><?= lang('eneable_sms_notification') ?></h5>
                                </div>
                            </div>
                            <p class="text-muted mb-0"> <?= lang('beta_sms_note') ?> </p>
                            <a href="https://accounts.termii.com/#/register" target="_blank" class="btn btn-sm btn-primary mb-2">Create Account</a>

                            <div class="card-body bg-light">
                                <?= form_open('settings/sms') ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sms_sender"><?= lang('sms_sender') ?></label><br>
                                            <span class="help-block">
                                                <small>A registered sender name with the SMS provider</small>
                                            </span>
                                            <input type="text" class="form-control" value="<?= set_value('sms_sender', $this->coop->sms_sender) ?>" name="sms_sender" id="sms_sender">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="sms_api_key"><?= lang('sms_api_key') ?></label>
                                        <input type="text" class="form-control" value="<?= set_value('sms_api_key', $this->coop->sms_api_key) ?>" name="sms_api_key" id="sms_api_key">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="sms_price_per_unit"><?= lang('sms_price_per_unit') ?></label>
                                        <input type="text" class="form-control" value="<?= set_value('sms_price_per_unit', $this->coop->sms_price_per_unit) ?>" name="sms_price_per_unit" id="sms_price_per_unit">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="sms_lenght_per_unit"><?= lang('sms_lenght_per_unit') ?></label>
                                        <input type="text" class="form-control" value="<?= set_value('sms_lenght_per_unit', $this->coop->sms_lenght_per_unit) ?>" name="sms_lenght_per_unit" id="sms_lenght_per_unit">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="beta_sms_username"><?= lang('beta_sms_username') ?></label>
                                            <input type="text" class="form-control" value="<?= set_value('beta_sms_username', $this->coop->beta_sms_username) ?>" name="beta_sms_username" id="beta_sms_username">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password"><?= lang('beta_sms_pass') ?></label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="beta_sms_pass" value="<?= set_value('beta_sms_pass', $this->coop->beta_sms_pass) ?>" name="beta_sms_pass" id="beta_sms_pass" class="form-control">
                                                <div class="input-group-append" data-password="false">
                                                    <div class="input-group-text">
                                                        <span class="password-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-md-6 mt-2">
                                        <div class="form-group">
                                            <?php if ($this->coop->sms_notice == 'on') { ?>
                                                <input type="checkbox" id="sms_notice" name="sms_notice" checked data-switch="bool" />
                                            <?php } else { ?>
                                                <input type="checkbox" id="sms_notice" name="sms_notice" data-switch="bool" />
                                            <?php } ?>
                                            <label for="sms_notice" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="wait_save_btnpp" style="display: none" class="btn btn-primary mt-2" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                            <span class="mr-1 ml-1">Saving...</span>
                                        </button>
                                        <button type="submit" id="save_btnpp" onclick="wait_loader('save_btnpp', 'wait_save_btnpp')" class="btn btn-primary mt-2"><i class="mdi mdi-content-save"></i> <?= lang('save') ?></button>
                                    </div>
                                </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="loan">
                        <div class="card-body border rounded">
                            <?= form_open('settings/loan') ?>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="loan_processing_fee"><?= lang('loan_processing_fee') ?></label><br>
                                    <span class="help-block"><small><?= lang('amt_paid_before_applying_for_loan') ?>.</small></span>
                                    <input type="text" class="form-control" value="<?= set_value('loan_processing_fee', $this->coop->loan_processing_fee) ?>" name="loan_processing_fee" id="loan_processing_fee">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="max_loan_requestable"><?= lang('max_requestable') ?></label><br>
                                    <span class="help-block"><small><?= lang('max_loan_requestable') ?>.</small></span>
                                    <input type="text" class="form-control" value="<?= set_value('max_loan_requestable', $this->coop->max_loan_requestable) ?>" name="max_loan_requestable" id="max_loan_requestable">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="loan_approval_level"><?= lang('loan_approval_level') ?></label><br>
                                    <span class="help-block"><small><?= lang('number_of_exco_approval') ?>.</small></span>
                                    <input data-toggle="touchspin" type="text" class="form-control" value="<?= set_value('loan_approval_level', $this->coop->loan_approval_level) ?>" name="loan_approval_level" id="loan_approval_level">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="next_payment_date"><?= lang('start_repayment') ?></label><br>
                                    <span class="help-block"><small><?= lang('next_payment_start') ?>.</small></span>
                                    <input data-toggle="touchspin" type="text" class="form-control" value="<?= set_value('next_payment_date', $this->coop->next_payment_date) ?>" name="next_payment_date" id="withdrawal_approval_level">
                                </div>

                                <div class="col form-group">
                                    <button id="wait_save_btnpp" style="display: none" class="btn btn-primary mt-2 float-right" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                        <span class="mr-1 ml-1">Saving...</span>
                                    </button>
                                    <button type="submit" id="save_btnpp" onclick="wait_loader('save_btnpp', 'wait_save_btnpp')" class="btn btn-primary mt-2 float-right"><i class="mdi mdi-content-save"></i> <?= lang('save') ?></button>
                                </div>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                    <div class="tab-pane" id="others">
                        <div class="card-body border rounded">
                            <?= form_open('settings/others') ?>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="credit_sales_approval_level"><?= lang('credit_sales_approval_level') ?></label><br>
                                    <span class="help-block"><small><?= lang('number_of_exco_approval') ?>.</small></span>
                                    <input data-toggle="touchspin" type="text" class="form-control" value="<?= set_value('credit_sales_approval_level', $this->coop->credit_sales_approval_level) ?>" name="credit_sales_approval_level" id="credit_sales_approval_level">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="member_exit_approval_level"><?= lang('member_exit_approval_level') ?></label><br>
                                    <span class="help-block"><small><?= lang('number_of_exco_approval') ?>.</small></span>
                                    <input data-toggle="touchspin" type="text" class="form-control" value="<?= set_value('member_exit_approval_level', $this->coop->member_exit_approval_level) ?>" name="member_exit_approval_level" id="member_exit_approval_level">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="withdrawal_approval_level"><?= lang('withdrawal_approval_level') ?></label><br>
                                    <span class="help-block"><small><?= lang('number_of_exco_approval') ?>.</small></span>
                                    <input data-toggle="touchspin" type="text" class="form-control" value="<?= set_value('withdrawal_approval_level', $this->coop->withdrawal_approval_level) ?>" name="withdrawal_approval_level" id="withdrawal_approval_level">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="approve_reg_member"><?= lang('approve_reg_member') ?></label><br>
                                    <span class="help-block"><small><?= lang('approve_reg_member_note') ?>.</small></span>

                                    <div class="mt-2">
                                        <?php if ($this->coop->approve_reg_member == 'true') { ?>
                                            <input type="checkbox" id="sms" checked data-switch="bool" name="approve_reg_member" />
                                        <?php } else { ?>
                                            <input type="checkbox" id="sms" data-switch="bool" name="approve_reg_member" />
                                        <?php } ?>
                                        <label for="sms" data-on-label="Yes" data-off-label="No"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 form-group">
                                <button id="wait_save_btnpp" style="display: none" class="btn btn-primary mt-2 float-right" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                    <span class="mr-1 ml-1">Saving...</span>
                                </button>
                                <button type="submit" id="save_btnpp" onclick="wait_loader('save_btnpp', 'wait_save_btnpp')" class="btn btn-primary mt-2 float-right"><i class="mdi mdi-content-save"></i> <?= lang('save') ?></button>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<div id="bottom-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bottomModalLabel"><?= lang('change_logo') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open_multipart('settings/upload') ?>
                <div class="form-group">
                    <label>Maximum upload size, 50kb</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                            <label class="custom-file-label" for="inputGroupFile04" id="file_label"> </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                <button id="wait_upload_btn" style="display: none" class="btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                    <span class="mr-1 ml-1"><?= lang('please_wait') ?></span>
                </button>
                <button type="submit" id="upload_btn" onclick="wait_loader('upload_btn', 'wait_upload_btn')" class="btn btn-primary"><?= lang('upload') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?php if ($this->coop->status == 'active' and $this->coop->record_upload_reminder == 'yes') { ?>
    <button type="button" id="migrate_notice_triger" style="display: none;" class="btn btn-dark" data-toggle="modal" data-target="#fill-dark-modal">Dark Filled</button>
    <div id="fill-dark-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fill-dark-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content modal-filled bg-dark">
                <div class="modal-header">
                    <h4 class="modal-title" id="fill-dark-modalLabel"><?= lang('notification') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        From the left side menu, scroll down to start migrating you cooperative records.
                    </p>
                    <img class="card-img-top" src="<?= $assets ?>images/guide/how_1.PNG" alt="Card image cap">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?= lang('close') ?></button>
                    <a data-fancy href="<?= base_url('migration') ?>" class="btn btn-outline-primary"><?= lang('start_now') ?></a>
                    <a data-fancy href="<?= base_url('settings/notification/no') ?>" class="btn btn-outline-danger"><?= lang('dont_remind_me') ?></a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>