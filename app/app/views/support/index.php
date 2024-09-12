<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('send') ?> <?= lang('message') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('support', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" form-group col-md-12">
                        <label for="priority"><?= lang('priority') ?></label>
                        <?php
                        $priority = ['' => 'Select Priority', 'low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];
                        ?>
                        <?= form_dropdown('priority', $priority, set_select('priority'), 'class="form-control" required id="priority" data-toggle="select2"'); ?>
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
                        <textarea id="summernote-basic" name="message"></textarea>
                    </div>
                </div>
                <button  type="submit" class="btn btn-primary float-right"><i class="mdi mdi-send-outline mr-1"></i><?= lang('send') ?></button>
                <?= form_close() ?>
            </div>
        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end card-->
<!-- end row-->