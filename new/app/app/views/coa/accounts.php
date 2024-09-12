<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><?= ucfirst($title)?>
                    <a href="javascript:window.history.back()" class="float-right btn btn-secondary"><i class="dripicons-arrow-left mr-2"></i> <?= lang('back')?></a>
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary float-right mr-2" data-toggle="modal" data-target="#top-modal-value" >
                            <i class="mdi mdi-plus mr-1"></i><?= lang('add') ?> <?= lang('acc_value') ?>
                        </button>
                        <button type="button" class="btn btn-primary float-right mr-2" data-toggle="modal" data-target="#top-modal-sub" >
                            <i class="mdi mdi-plus mr-1"></i><?= lang('add') ?> <?= lang('sub_title') ?>
                        </button>
                    </div>
                </div>
                <table  class="table  dt-responsive nowrap w-100" id="basic-datatable-account">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?= lang('code') ?></th>
                            <th><?= lang('name') ?></th>
                            <th style="width: 90px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($title_sub as $st) { ?>
                            <tr class="bg-primary-lighten text-light border-0">
                                <td class="mr-3 text-primary font-weight-bolder"><?= $st->sub_title_code ?> - <?= ucfirst($st->sub_title) ?></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a onclick="return confirm('Are you sure you want to delete?')" data-fancy href="<?= base_url('coa/delete_subtitle/'.$this->utility->mask($st->sub_title_id)) ?>" data-toggle="popover" data-trigger="hover" title="" data-content="Delete acc. subtitle and all the values attached to it " data-original-title="Caution !" class="btn btn-sm btn-block text-danger bg-danger-lighten">
                                        <i class="uil-trash"></i> 
                                    </a>
                                </td>
                            </tr>
                            <?php foreach ($st->act_val as $value) { ?>
                                <tr>
                                    <td></td>
                                    <td><?= $value->code ?> </td>
                                    <td><?= ucfirst($value->name) ?></td>
                                    <td>
                                        <a onclick="return confirm('Are you sure you want to delete?')" data-fancy href="<?= base_url('coa/delete_acc_value/'.$this->utility->mask($value->id)) ?>" class="btn btn-sm btn-danger btn-block">
                                            <i class="uil-trash"></i> 
                                        </a>
                                    </td>
                                </tr>
                            <?php } 
                            
                            } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="top-modal-sub" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-center">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="topModalLabel"><?= lang('add') ?> <?= lang('sub_title') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('coa/add_subtitle/'.$acc_title_id, 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                    <input type="text" name="name" id="name" class="form-control"  required placeholder="e.g Current Asset">
                </div>
                <div class=" form-group">
                    <label for="<?= lang('code') ?>"><?= lang('code') ?></label>
                    <input type="text" name="code" id="code" class="form-control"  required placeholder="e.g 1200">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<div id="top-modal-value" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-center">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="topModalLabel"><?= lang('add') ?> <?= lang('acc_value') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('coa/add_acc_value/'.$acc_title_id, 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?=lang('acc').' '. lang('sub_title') ?></label>
                    <select  class="form-control select2" data-toggle="select2" name="acc_subtitle">
                        <?php foreach ($assets_sub as $asb) { ?>
                            <option value="<?=$asb->id?>" ><?=$asb->name?></option>
                        <?php } ?>
                    </select>
                    <!--<input type="text" name="name" id="name" class="form-control"  required placeholder="e.g Current Asset">-->
                </div>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                    <input type="text" name="name" id="name" class="form-control"  required placeholder="e.g Current Asset">
                </div>
                <div class=" form-group">
                    <label for="<?= lang('code') ?>"><?= lang('code') ?></label>
                    <input type="text" name="code" id="code" class="form-control"  required placeholder="e.g 1200">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>