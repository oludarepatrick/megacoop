<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('statement') ?></h4>
            </div>
            
            <div class="card-body">
                <?= form_open_multipart('member/statement', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                        <input type="text" name="member_id" value="<?= set_value('member_id', $this->user->username) ?>" id="member_id" class="form-control" readonly required>
                    </div>
                    <div class=" form-group col-md-6">
                        <label for="statement_type"><?= lang('statement') ?> <?= lang('type') ?></label>
                        <?php
                        $statement_type = ['' => 'Select Statement Type', 'loan' => 'Loan', 'savings' => 'Savings'];
                        ?>
                        <?= form_dropdown('statement_type', $statement_type, set_value('statement_type'), 'class="form-control" required onchange="show_statement_type()" id="statement_type" data-toggle="select2"'); ?>
                    </div>
                    <div class="col-md-12 form-group" style="display: none" id="loan_type_box">
                        <label for="loan_type"><?= lang('loan_type') ?></label>
                        <?php
                        $lt[""] = lang('select') . ' ' . lang('loan_type');
                        foreach ($loan_types as $st) {
                            $lt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('loan_type', $lt, set_value('loan_type'), 'class="form-control select2"  id="loan_type" data-toggle="select2"'); ?>
                    </div>
                    <div class="col-md-12 form-group" style="display: none" id="savings_type_box">
                        <label for="savings_type"><?= lang('savings_type') ?></label>
                        <?php
                        $stt[""] = lang('select') . ' ' . lang('savings_type');
                        foreach ($savings_type as $st) {
                            $stt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('savings_type', $stt, set_value('savings_type'), ' class="form-control"  id="savings_type" data-toggle="select2"'); ?>
                    </div>
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                        <input type="text" name="start_date" class="form-control" value="<?= set_value('start_date') ?>" required data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                    </div>
                    <div class=" col-md-6 form-group">
                        <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                        <input type="text" name="end_date" value="<?= set_value('end_date') ?>"  class="form-control" required data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                    </div>
                </div>
                <button  type="submit" class="btn btn-primary float-right"><i class="mdi mdi-file-document-box-search mr-1"></i><?= lang('fetch') ?></button>
                <?= form_close() ?>
            </div>
        </div> 
    </div> 
</div> 