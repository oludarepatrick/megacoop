<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('add_batch_loan_repayment') ?></h4>
            </div>
            <div class="card-body bg-light m-3 rounded">
                <h5 class="mb-2 font-16"><?= lang('instruction_batch_upload') ?></h5>
                <p><?= lang('how_to_upload_batch1') ?></p>
                <p><?= lang('how_to_upload_batch2') ?></p>
                <p><?= lang('how_to_upload_batch3') ?></p>
                <p><?= lang('how_to_upload_batch4') ?></p>
                <div class="form-row">
                    <div class="col-md-8 form-group">
                        <?php
                        $stt[""] = lang('select') . ' ' . lang('loan_type');
                        foreach ($loan_types as $st) {
                            $stt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('loan_type_1', $stt, set_value('loan_type_1'), 'class="form-control select2" name="loan_type_1"  id="loan_type_1" data-toggle="select2"'); ?>
                    </div>
                    <div class=" col-md-4 form-group">
                        <a id="hide_generate_savin_btn" data-toggle="tooltip" data-placement="bottom" title="Download"
                           class="btn btn-primary text-white mb-2" onclick="generate_loan_repayment_template()">
                            <i class='uil uil-notes mr-1'></i> <?= lang('generate_template') ?>
                        </a>
                        <button  id="wait_generate_savin_btn" style="display: none" class="btn btn-primary mb-2" type="button" disabled >
                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                            <span class="mr-1 ml-1">Please wait a bit...</span>
                        </button>
                    </div>
                </div>

                <div class="card mb-2 mt-2 shadow-none border border-danger" style="display: none" id="savings_template_download">
                    <div class="p-1">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded">
                                        .XLS
                                    </span>
                                </div>
                            </div>
                            <div class="col pl-0 d-none d-sm-block">
                                <a class="text-muted font-weight-bold">loan-repayment-template.xls</a>
                                <!--<p class="mb-0">4.50 kb</p>-->
                            </div>
                            <div class="col-auto">
                                <!-- Button -->
                                <a id="savings_template_download_btn" class="btn btn-primary btn-sm text-white">
                                    <i class='uil uil-cloud-download mr-1'></i> <?= lang('download') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="dripicons-wrong mr-2"></i> <span id="error"></span>
                </div>
                <?= form_open_multipart('loanrepayment/add_batch', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-6 form-group">
                        <label>Maximum upload size, 900kb</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                                <label class="custom-file-label" for="inputGroupFile04" id="file_label"> </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="loan_type"><?= lang('loan_type') ?></label>
                        <?php
                        $stt[""] = lang('select') . ' ' . lang('loan_type');
                        foreach ($loan_types as $st) {
                            $stt[$st->id] = $st->name;
                        }
                        ?>
                        <?= form_dropdown('loan_type', $stt, set_value('loan_type'), 'class="form-control select2" name="loan_type"  id="loan_type" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="source"><?= lang('source') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('source');
                        foreach ($savings_source as $sc) {
                            if ($sc->id == 1) {
                                continue;
                            }
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('source', $scc, set_value('source'), 'class="form-control select2" name="source"  id="source" data-toggle="select2"'); ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="<?= lang('month') ?>"><?= lang('month') ?></label>
                        <input type="text" name="month" class="form-control" data-provide="datepicker" data-date-format="MM" data-date-min-view-mode="1">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="<?= lang('year') ?>"><?= lang('year') ?></label>
                        <input type="text" name="year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('narration') ?>"><?= lang('narration') ?></label>
                    <textarea class="form-control" name="narration" value="<?= set_value('narration') ?>" id="description" name="description" required=""></textarea>
                </div>
                <button  type="submit" class="btn btn-primary float-right"><i class="uil uil-cloud-upload mr-1"></i><?= lang('upload') ?></button>
                    <?= form_close() ?>
            </div>
            <!-- end row -->

        </div>
    </div>
</div> 
<!-- end row-->