<!-- Start Content-->

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mt-4 ml-2"><?= lang('security') ?></h4>
            </div>
            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                <li class="nav-item">
                    <a href="#tab1" data-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                        <i class="mdi mdi-lock-open-outline d-md-none d-block"></i>
                        <span class="d-none d-md-block"><?= lang('change_pass') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tab2" data-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                        <i class="mdi mdi-shield-lock d-md-none d-block"></i>
                        <span class="d-none d-md-block"><?= lang('change_pin') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tab3" data-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                        <i class="mdi mdi-shield-account d-md-none d-block"></i>
                        <span class="d-none d-md-block"><?= lang('2FA') ?></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane show active" id="tab1">
                    <h4 class="ml-3">Change login password</h4>
                    <?= form_open('member/profile/change_password', 'class="needs-validation" novalidate') ?>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="old_pass"><?= lang('old_pass') ?></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="old_pass" name="old_pass" required class="form-control" placeholder="Enter your old password">
                                <div class="input-group-append" data-password="false">
                                    <div class="input-group-text">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_pass"><?= lang('new_pass') ?></label>
                            <div id="message" style="display: none;">
                                <small id="letter" class="text-danger">A lowercase,</small>
                                <small id="capital" class="text-danger">uppercase,</small>
                                <small id="number" class="text-danger">number</small>
                                <small id="length" class="text-danger">and 8 characters min. required!</b></small>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" name="new_pass" required class="form-control" placehnewer="Enter your new password">
                                <div class="input-group-append" data-password="false">
                                    <div class="input-group-text">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                                <div class="valid-tooltip">
                                    <?= lang('looks_good') ?>
                                </div>
                                <div class="invalid-tooltip">
                                    <?= lang('field_required') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary float-right"><?= lang('change') ?></button>
                        <div class="clearfix"></div>
                    </div>

                    <?= form_close() ?>
                </div>

                <div class="tab-pane" id="tab2">
                    <h4 class="ml-3">Change Transaction Pin</h4>
                    <small class="ml-4">Default Pin (123456)</small> <small class="text-danger">Must be changed!</small>
                    <?= form_open('member/profile/change_pin', 'class="needs-validation" novalidate') ?>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="old_pin"><?= lang('old_pin') ?></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="old_pin" name="old_pin" required class="form-control" data-toggle="input-mask" data-mask-format="00-00-00">
                                <div class="input-group-append" data-password="false">
                                    <div class="input-group-text">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_pin"><?= lang('new_pin') ?></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="new_pin" name="new_pin" required class="form-control" data-toggle="input-mask" data-mask-format="00-00-00">
                                <div class="input-group-append" data-password="false">
                                    <div class="input-group-text">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary float-right"><?= lang('change') ?></button>
                        <div class="clearfix"></div>
                    </div>

                    <?= form_close() ?>
                </div>

                <div class="tab-pane" id="tab3">
                    <h4 class="ml-3"><?= lang('2fa') ?></h4>

                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading"><?= lang('enable') ?> <?= lang('2FA') ?></h4>
                            <p>Set up additional layer of security for your account. Whenever a login is initiated, the
                                system will send a 6-digit authorization token to you. The token will be provided on request
                                to allow the system honor the login request.
                            </p>
                            <hr>
                            <?php if ($this->user->twofa == 'true') { ?>
                                <input type="checkbox" id="switch1" onclick="enable_2fa('true')" checked data-switch="bool" />
                            <?php } else { ?>
                                <input type="checkbox" id="switch1" onclick="enable_2fa('false')" data-switch="bool" />
                            <?php } ?>

                            <label for="switch1" data-on-label="Yes" data-off-label="No"></label>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->