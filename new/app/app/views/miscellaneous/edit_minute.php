<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('send') ?> <?= lang('message') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('miscellaneous/edit_minute/'.$id, '') ?>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('title') ?>"><?= lang('title') ?></label>
                        <input type="text" name="title" id="title" class="form-control" required value="<?= set_value('title', $minutes->title) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('content') ?>"><?= lang('content') ?></label>
                        <textarea id="summernote-basic" name="content" required ><?=$minutes->content?></textarea>
                    </div>
                </div>
            </div>
            <div class=" card-footer">
                <button  type="submit" class="btn btn-primary float-right"><i class="mdi mdi-file"></i> <?=lang('save')?></button>
            </div>
            <?= form_close() ?>
        </div> 
    </div> 
</div>
<!-- end row-->