<!-- Start Content-->

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="shadow-sm px-2 card">
            <h4 class="alert-heading"><?= lang('enable') ?> <?= lang('sms') ?></h4>
            <div class=" d-flex justify-content-between">
                <p>
                    Get SMS notification on transaction that happens
                    on your account
                </p>
                <div class="ml-2">
                    <?php if ($this->user->sms_notice == 'true') { ?>
                        <input type="checkbox" id="sms" onclick="update_preferences('false', 'sms_notice')" checked data-switch="bool" />
                    <?php } else { ?>
                        <input type="checkbox" id="sms" onclick="update_preferences('true','sms_notice')" data-switch="bool" />
                    <?php } ?>
                    <label for="sms" data-on-label="Yes" data-off-label="No"></label>
                </div>
            </div>
        </div>
        <div class="shadow-sm px-2 card">
            <h4 class="alert-heading"><?= lang('enable') ?> <?= lang('email') ?></h4>
            <div class=" d-flex justify-content-between">
                <p>
                    Get Email notification on transaction that happens
                    on your account
                </p>
                <div class="ml-2">
                    <?php if ($this->user->email_notice == 'true') { ?>
                        <input type="checkbox" id="email" onclick="update_preferences('false', 'email_notice')" checked data-switch="bool" />
                    <?php } else { ?>
                        <input type="checkbox" id="email" onclick="update_preferences('true', 'email_notice')" data-switch="bool" />
                    <?php } ?>
                    <label for="email" data-on-label="Yes" data-off-label="No"></label>
                </div>
            </div>
        </div>
        <div class="shadow-sm px-2 card">
            <h4 class="alert-heading"><?= lang('enable') ?> <?= lang('pass_expiry') ?></h4>
            <div class=" d-flex justify-content-between">
                <p>
                    Get a reminder whenever your password is due for reset
                </p>
                <div class="ml-2">
                    <?php if ($this->user->pass_expiry_notice == 'true') { ?>
                        <input type="checkbox" id="password" onclick="update_preferences('false', 'pass_expiry_notice')" checked data-switch="bool" />
                    <?php } else { ?>
                        <input type="checkbox" id="password" onclick="update_preferences('true', 'pass_expiry_notice')" data-switch="bool" />
                    <?php } ?>
                    <label for="password" data-on-label="Yes" data-off-label="No"></label>
                </div>
            </div>
        </div>
    </div>

</div>