<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('send') ?> <?= lang('message') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('communication/send', '') ?>
                <div class="row">
                    <div class=" form-group col-md-12">
                        <label for="recip_type"><?= lang('recip_type') ?></label>
                        <?php
                        $recipiants = ['' => 'Select Recipient Type', 'all' => 'All', 'admin' => 'Admin (Exco only)', 'member' => 'Member only', 'custom' => 'Custom'];
                        ?>
                        <?= form_dropdown('recip_type', $recipiants, set_value('recip_type'), 'class="form-control" required onchange="show_recipient()" id="recip_type" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="row" id="recipients-box" style="display: none;">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('recipients') ?>"><?= lang('recipients') ?>  
                            <small class="text-danger">(use phone number if you intend to send as SMS  e.g 08164517303, 09033446767))</small>
                        </label>
                        <input type="text" name="recipients" id="recipients" class="form-control" placeholder="Eg. memberone@gmail.com, membertwo@gmail.com, memberthree@gmail.com" value="<?= set_value('recipients') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('subject') ?>"><?= lang('subject') ?></label>
                        <input type="text" name="subject" id="subject" class="form-control" required value="<?= set_value('subject') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('message') ?>"><?= lang('message') ?></label>
                        <textarea class="form-control" name="message" rows="8" required></textarea>
                    </div>
                </div>
            </div>
            <div class="bg-light card-footer">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadio3" name="channel" value="email" required class="custom-control-input">
                    <label class="custom-control-label" for="customRadio3">Email (Free)</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadio4" name="channel" value="sms" required class="custom-control-input">
                    <label class="custom-control-label" for="customRadio4">SMS (charges apply) </label>
                </div>
                <button  type="submit" class="btn btn-primary float-right"><i class="mdi mdi-send-outline"></i> <?=lang('send')?></button>
            </div>
            <?= form_close() ?>
        </div> 
    </div> 
</div> 
<!-- end row-->