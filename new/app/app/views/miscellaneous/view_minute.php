<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('send') ?> <?= ucfirst($minutes->title) ?></h4>
                <small class="badge badge-primary-lighten text-uppercase"> <?=lang('created_on')?>:  <?=$this->utility->just_date($minutes->created_on, true)?></small>
            </div>
            <div class="card-body">
                <?= $minutes->content ?>
            </div>
            <div class=" card-footer d-print-none text-right">
                <a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> <?= lang('print') ?></a>
                <a href="javascript: window.history.back();" class="btn btn-info"><?= lang('close') ?></a>
            </div>
        </div>
    </div>
</div>