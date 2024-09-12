<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= ucfirst($title) ?></h4>

            </div>
            <div class="card-body">
                <?= form_open('accounting', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('pv_no') ?>"><?= lang('pv_no') ?></label>
                        <input type="text" name="pv_no" value="<?= set_value('pv_no', $pv_no) ?>" id="pv_no" readonly="" class="form-control"  required>
                    </div>
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('reference') ?>"><?= lang('reference') ?></label>
                        <input type="text" name="reference" value="<?= set_value('reference') ?>" id="reference" class="form-control"  required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" class="form-control" <?= set_value('amount') ?> data-toggle="input-mask" data-mask-format="000.000.000.000,000,000" data-reverse="true">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="mop"><?= lang('mop') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('mop');
                        foreach ($mop as $m) {
                            $scc[$m->id] = $m->name;
                        }
                        ?>
                        <?= form_dropdown('mop', $scc, set_value('mop'), 'class="form-control select2" name="mop"  id="mop" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="<?= lang('dr') ?>"><?= lang('dr') . ' ' . lang('account') ?></label>
                        <select class="form-control select2" data-toggle="select2" name="dr">
                            <?php foreach ($acc_data as $key_1 =>$val_1) { ?>
                            <optgroup label="<?=$key_1?>">
                                <?php foreach ($val_1 as $key_2=>$val_2){ ?>
                                <optgroup label="&nbsp; &nbsp; &nbsp;<?=$key_2?>">
                                    <?php foreach ($val_2 as $val_3){?>
                                    <option value="<?=$val_3->id?>">&nbsp; &nbsp;&nbsp; &nbsp;<?=$val_3->code.' - '.$val_3->name?></option>
                                    <?php } ?>
                                </optgroup>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="<?= lang('cr') ?>"><?= lang('cr') . ' ' . lang('account') ?></label>
                        <select class="form-control select2" data-toggle="select2" name="cr">
                            <?php foreach ($acc_data as $key_1 =>$val_1) { ?>
                            <optgroup label="<?=$key_1?>">
                                <?php foreach ($val_1 as $key_2=>$val_2){ ?>
                                <optgroup label="&nbsp; &nbsp; &nbsp;<?=$key_2?>">
                                    <?php foreach ($val_2 as $val_3){?>
                                    <option value="<?=$val_3->id?>">&nbsp; &nbsp;&nbsp; &nbsp;<?=$val_3->code.' - '.$val_3->name?></option>
                                    <?php } ?>
                                </optgroup>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('particular') ?>"><?= lang('particular') ?></label>
                        <input type="text" name="particular" class="form-control" >
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('payment_date') ?>"><?= lang('payment_date') ?></label>
                        <input type="text" name="payment_date" class="form-control" data-provide="datepicker" data-date-format="yyyy-m-d">
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('narration') ?>"><?= lang('narration') ?></label>
                    <textarea class="form-control" name="narration" value="<?= set_value('narration') ?>" id="narration" required=""></textarea>
                </div>
                <button  type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                    <?= form_close() ?>
            </div>  
        </div>
    </div>
</div>