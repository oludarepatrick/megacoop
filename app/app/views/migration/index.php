<!-- Start Content-->
<div class="container-fluid">

    <div class="row">
        <div class="col-xl-8">

            <!-- tasks panel -->
            <div class="">
                <a class="text-dark" data-toggle="collapse" href="#todayTasks" aria-expanded="false" aria-controls="todayTasks">
                    <h5 class="m-0 pb-2 bg-primary card-header text-white">
                        <i class='uil uil-angle-down font-18'></i> <?= lang('migrate_member_record') ?>
                    </h5>
                </a>

                <div class="collapse show" id="todayTasks">
                    <div class="card mb-0">
                        <div class="card-body">
                            <h5 class="mb-2 font-16"><?= lang('how_to_upload_member_record') ?></h5>
                            <p><?= lang('how_to_upload_record_step1') ?></p>
                            <p><?= lang('how_to_upload_record_step2') ?></p>
                            <p><?= lang('how_to_upload_record_step3') ?></p>

                            <div class="card mb-2 shadow-none border">
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
                                            <a href="javascript:void(0);" class="text-muted font-weight-bold">sample-member-record.xls</a>
                                            <p class="mb-0">1.0 kb</p>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Button -->
                                            <a data-fancy href="<?= $assets ?>/files/sample-member-record.xls" download data-toggle="tooltip" data-placement="bottom" title="Download" class="btn btn-primary btn-sm text-white">
                                                <i class='uil uil-cloud-download mr-1'></i> <?= lang('download') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border">
                                <div class="card-body bg-light">
                                    <h5 class="mb-2 font-16"><?= lang('upload_here') ?></h5>
                                    <?= form_open_multipart('migration/member_record') ?>
                                    <div class="form-group">
                                        <label>Maximum upload size, 900kb</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                                                <label class="custom-file-label" for="inputGroupFile03" id="file_label"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?= lang('default_pass') ?></label><br>
                                        <small class="text-danger">The password to share with your cooperative member on their first login</small>
                                        <input type="text" name="default_pass" class="form-control" value="12345678">
                                    </div>
                                    <div class="form-group text-right">
                                        <button id="wait_upload_btn" style="display: none" class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                            <span class="mr-1 ml-1"><?= lang('please_wait') ?></span>
                                        </button>
                                        <button type="submit" id="upload_btn" onclick="wait_loader('upload_btn', 'wait_upload_btn')" class="btn btn-primary"><i class='uil uil-cloud-upload mr-1'></i> <?= lang('upload') ?></button>
                                    </div>
                                    <?= form_close() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <a class="text-dark" data-toggle="collapse" href="#savings" aria-expanded="false" aria-controls="todayTasks">
                    <h5 class="m-0 pb-2 bg-primary card-header text-white">
                        <i class='uil uil-angle-down font-18'></i> <?= lang('migrate_savings_record') ?>
                    </h5>
                </a>

                <div class="collapse hide" id="savings">
                    <div class="card mb-0">
                        <div class="card-body">
                            <h5 class="mb-2 font-16"><?= lang('how_to_upload_saivings_record') ?></h5>
                            <p><?= lang('how_to_upload_savings_record_step1') ?></p>
                            <p><?= lang('how_to_upload_savings_record_step2') ?></p>
                            <p><?= lang('how_to_upload_savings_record_step3') ?></p>
                            <p><?= lang('how_to_upload_savings_record_step4') ?></p>
                            <a id="hide_generate_savin_btn" data-toggle="tooltip" data-placement="bottom" title="Generate savings template" class="btn btn-primary text-white mb-2" onclick="generate_savings_record()">
                                <i class='uil uil-notes mr-1'></i> <?= lang('generate_template') ?>
                            </a>
                            <button id="wait_generate_savin_btn" style="display: none" class="btn btn-primary mb-2" type="button" disabled>
                                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                <span class="mr-1 ml-1">Please wait a bit...</span>
                            </button>

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
                                        <div class="col pl-0">
                                            <a class="text-muted font-weight-bold">savings-template.xls</a>
                                            <p class="mb-0">4.50 kb</p>
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
                            <div class="card border">
                                <div class="card-body bg-light">
                                    <h5 class="mb-2 font-16"><?= lang('upload_savings_here') ?></h5>
                                    <?= form_open_multipart('migration/savings_record') ?>
                                    <div class="form-group">
                                        <label>Maximum upload size, 900kb</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile04', 'file_label2')" id="inputGroupFile04">
                                                <label class="custom-file-label" for="inputGroupFile04" id="file_label2"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mt-1">
                                            <div class="form-group">
                                                <label for="savings_type"><?= lang('savings_type') ?></label>
                                                <?php
                                                $stt[""] = lang('select') . ' ' . lang('savings_type');
                                                foreach ($savings_type as $st) {
                                                    $stt[$st->id] = $st->name;
                                                }
                                                ?>
                                                <?= form_dropdown('savings_type', $stt, set_value('savings_type'), 'class="form-control select2" name="savings_type"  id="savings_type" data-toggle="select2"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
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
                                        <div class="form-group col-md-6">
                                            <label for="<?= lang('month') ?>"><?= lang('month') ?></label>
                                            <input type="text" required name="month" class="form-control" data-provide="datepicker" data-date-format="MM" data-date-min-view-mode="1">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="<?= lang('year') ?>"><?= lang('year') ?></label>
                                            <input type="text" required name="year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <div class="form-group text-right">
                                                <button id="wait_upload_btn" style="display: none" class="btn btn-primary" type="button" disabled>
                                                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                                    <span class="mr-1 ml-1"><?= lang('please_wait') ?></span>
                                                </button>
                                                <button type="submit" id="upload_btn" onclick="wait_loader('upload_btn', 'wait_upload_btn')" class="btn btn-primary"><i class='uil uil-cloud-upload mr-1'></i> <?= lang('upload') ?></button>
                                            </div>
                                        </div>
                                    </div>

                                    <?= form_close() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <a class="text-dark" data-toggle="collapse" href="#loan" aria-expanded="false" aria-controls="todayTasks">
                    <h5 class="m-0 pb-2 bg-primary card-header text-white">
                        <i class='uil uil-angle-down font-18'></i> <?= lang('migrate_loan_record') ?>
                    </h5>
                </a>

                <div class="collapse hide" id="loan">
                    <div class="card mb-0">
                        <div class="card-body">
                            <h5 class="mb-2 font-16"><?= lang('how_to_upload_loan_record') ?></h5>
                            <p><?= lang('how_to_upload_loan_record_step1') ?></p>
                            <p><?= lang('how_to_upload_loan_record_step2') ?></p>
                            <p><?= lang('how_to_upload_loan_record_step3') ?></p>
                            <p><?= lang('how_to_upload_loan_record_step4') ?></p>
                            <a id="hide_generate_loan_btn" data-toggle="tooltip" data-placement="bottom" title="Download" class="btn btn-primary text-white mb-2" onclick="generate_loan_record()">
                                <i class='uil uil-notes mr-1'></i> <?= lang('generate_template') ?>
                            </a>
                            <button id="wait_generate_loan_btn" style="display: none" class="btn btn-primary mb-2" type="button" disabled>
                                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                <span class="mr-1 ml-1">Please wait a bit...</span>
                            </button>

                            <div class="card mb-2 mt-2 shadow-none border" style="display: none" id="loan_template_download">
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
                                            <a href="javascript:void(0);" class="text-muted font-weight-bold">loan-template.xls</a>
                                            <p class="mb-0">2.3 kb</p>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Button -->
                                            <a id="loan_template_download_btn" data-toggle="tooltip" data-placement="bottom" title="Download" download class="btn btn-primary btn-sm text-white">
                                                <i class='uil uil-cloud-download mr-1'></i> <?= lang('download') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border">
                                <div class="card-body bg-light">
                                    <h5 class="mb-2 font-16"><?= lang('upload_loan_here') ?></h5>
                                    <?= form_open_multipart('migration/loan_record') ?>
                                    <div class="form-group">
                                        <label>Maximum upload size, 900kb</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile05', 'file_label3')" id="inputGroupFile05">
                                                <label class="custom-file-label" for="inputGroupFile05" id="file_label3"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8 mt-1">
                                            <div class="form-group">
                                                <label for="loan_type"><?= lang('loan_type') ?></label>
                                                <?php
                                                $ltt[""] = lang('select') . ' ' . lang('loan_type');
                                                foreach ($loan_type as $lt) {
                                                    $ltt[$lt->id] = $lt->name;
                                                }
                                                ?>
                                                <?= form_dropdown('loan_type', $ltt, set_value('loan_type'), 'class="form-control select2" name="loan_type"  id="loan_type" data-toggle="select2"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <div class="form-group text-right">
                                                <button id="wait_upload_btn" style="display: none" class="btn btn-primary" type="button" disabled>
                                                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                                    <span class="mr-1 ml-1"><?= lang('please_wait') ?></span>
                                                </button>
                                                <button type="submit" id="upload_btn" onclick="wait_loader('upload_btn', 'wait_upload_btn')" class="btn btn-primary"><i class='uil uil-cloud-upload mr-1'></i> <?= lang('upload') ?></button>
                                            </div>
                                        </div>
                                    </div>

                                    <?= form_close() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <a class="text-dark" data-toggle="collapse" href="#credit_sales" aria-expanded="false" aria-controls="todayTasks">
                    <h5 class="m-0 pb-2 bg-primary card-header text-white">
                        <i class='uil uil-angle-down font-18'></i> <?= lang('migrate_credit_sales_record') ?>
                    </h5>
                </a>

                <div class="collapse hide" id="credit_sales">
                    <div class="card mb-0">
                        <div class="card-body">
                            <h5 class="mb-2 font-16"><?= lang('how_to_upload_loan_record') ?></h5>
                            <p><?= lang('how_to_upload_loan_record_step1') ?></p>
                            <p><?= lang('how_to_upload_loan_record_step2') ?></p>
                            <p><?= lang('how_to_upload_loan_record_step3') ?></p>
                            <p><?= lang('how_to_upload_loan_record_step4') ?></p>
                            <a id="hide_generate_credit_sales_btn" data-toggle="tooltip" data-placement="bottom" title="Download" class="btn btn-primary text-white mb-2" onclick="generate_credit_sales_record()">
                                <i class='uil uil-notes mr-1'></i> <?= lang('generate_template') ?>
                            </a>
                            <button id="wait_generate_credit_sales_btn" style="display: none" class="btn btn-primary mb-2" type="button" disabled>
                                <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                <span class="mr-1 ml-1">Please wait a bit...</span>
                            </button>

                            <div class="card mb-2 mt-2 shadow-none border" style="display: none" id="credit_sales_template_download">
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
                                            <a href="javascript:void(0);" class="text-muted font-weight-bold">credit_sales-template.xls</a>
                                            <p class="mb-0">2.3 kb</p>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Button -->
                                            <a id="credit_sales_template_download_btn" data-toggle="tooltip" data-placement="bottom" title="Download" download class="btn btn-primary btn-sm text-white">
                                                <i class='uil uil-cloud-download mr-1'></i> <?= lang('download') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border">
                                <div class="card-body bg-light">
                                    <h5 class="mb-2 font-16"><?= lang('upload_credit_sales_here') ?></h5>
                                    <?= form_open_multipart('migration/credit_sales_record') ?>
                                    <div class="form-group">
                                        <label>Maximum upload size, 900kb</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile06', 'file_label4')" id="inputGroupFile06">
                                                <label class="custom-file-label" for="inputGroupFile05" id="file_label4"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8 mt-1">
                                            <div class="form-group">
                                                <label for="product_type"><?= lang('product_type') ?></label>
                                                <?php
                                                $ptt[""] = lang('select') . ' ' . lang('product_type');
                                                foreach ($product_type as $lt) {
                                                    $ptt[$lt->id] = $lt->name;
                                                }
                                                ?>
                                                <?= form_dropdown('product_type', $ptt, set_value('product_type'), 'class="form-control select2" name="product_type"  id="product_type" data-toggle="select2"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <div class="form-group text-right">
                                                <button id="wait_upload_btn" style="display: none" class="btn btn-primary" type="button" disabled>
                                                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                                    <span class="mr-1 ml-1"><?= lang('please_wait') ?></span>
                                                </button>
                                                <button type="submit" id="upload_btn" onclick="wait_loader('upload_btn', 'wait_upload_btn')" class="btn btn-primary"><i class='uil uil-cloud-upload mr-1'></i> <?= lang('upload') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                    <?= form_close() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- task details -->
        <div class="col-xl-4">
            <div class="card cta-box bg-primary text-white">
                <div class="card-body">
                    <a data-fancy href="<?= base_url('migration/logs') ?>" class="text-white">
                        <div class="media align-items-center">
                            <div class="media-body">
                                <h2 class="mt-0"><i class="mdi mdi-reload"></i></h2>
                                <h3 class="m-0 font-weight-normal cta-box-title">Rollback <b>migrated records</b> if there was error in your uploaded record
                                    <i class="mdi mdi-arrow-right-bold-outline"></i>
                                </h3>
                            </div>
                            <img class="ml-3" src="<?= $assets ?>images/email-campaign.svg" width="120" alt="Generic placeholder image">
                        </div>
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="custom-control custom-checkbox float-left">
                        <label class="custom-control-label" for="completedCheck">
                            <?= lang('registration_invite') ?>
                        </label>
                    </div>
                    <hr class="mt-4 mb-2" />

                    <div class="row">
                        <div class="col">

                            <div class="border border-light rounded bg-light p-2 mb-3">
                                <div class="media">
                                    <div class="media-body">
                                        <div class="spinner-grow text-danger" role="status"></div>
                                        <h5 class="m-0"><?= lang('what_will_i_do') ?></h5>
                                    </div>
                                </div>
                                <p>You can send registration invite link to your members here. <q>This is an alternative to batch migration of Member Records</q>
                                </p>
                            </div>

                            <div class="custom-control  mt-1">
                                <label class="custom-control-label " for="checklist3">
                                    Add multiple members' email and separate its with ( , ) comma
                                </label>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="border rounded">
                                        <?= form_open('migration/send_invite', 'class="comment-area-box"') ?>
                                        <textarea rows="4" class="form-control border-0 resize-none" name="recipients" placeholder="e.g memberone@gmail.com, membertwo@gmail.com" required></textarea>
                                        <div class="p-2 bg-light">
                                            <div class="float-right">
                                                <button type="submit" class="btn btn-sm btn-primary"><i class='uil uil-message mr-1'></i><?= lang('send') ?></button>
                                            </div>
                                            <div>
                                                <a href="#" class="btn btn-sm px-1 btn-light"><i class='uil uil-question-circle'> </i></a>
                                            </div>
                                        </div>
                                        <?= form_close() ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<button type="button" id="saving_notice_triger" style="display: none;" class="btn btn-dark" data-toggle="modal" data-target="#fill-dark-modal"></button>
<div id="fill-dark-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fill-dark-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-filled bg-dark">
            <div class="modal-header">
                <h4 class="modal-title" id="fill-dark-modalLabel"><?= lang('notification') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p class="lead">
                    You need to setup the types of savings your cooperative operate first. Kindly follow the steps below to do that.
                </p>
                <p class="lead">
                    From the left side menu, select <b class="badge badge-primary"><?= lang('categories') ?></b> ,
                    then select <b class="badge badge-primary"><?= lang('savings_type') ?>,</b>.
                </p>
                <img class="card-img-top" src="<?= $assets ?>images/guide/how_2.png" alt="Card image cap">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= lang('close') ?></button>
                <a data-fancy href="<?= base_url('categories') ?>" class="btn btn-outline-primary"><?= lang('start_now') ?></a>
            </div>
        </div>
    </div>
</div>

<button type="button" id="loan_notice_triger" style="display: none;" class="btn btn-dark" data-toggle="modal" data-target="#fill-dark-modal2"></button>
<div id="fill-dark-modal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fill-dark-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-filled bg-dark">
            <div class="modal-header">
                <h4 class="modal-title" id="fill-dark-modalLabel"><?= lang('notification') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p class="lead">
                    You need to setup the types of loans your cooperative operate first. Kindly follow the steps below to do that.
                </p>
                <p class="lead">
                    From the left side menu, select <b class="badge badge-primary"><?= lang('categories') ?></b> ,
                    then select <b class="badge badge-primary"><?= lang('loan_type') ?>,</b>.
                </p>
                <img class="card-img-top" src="<?= $assets ?>images/guide/how_2.png" alt="Card image cap">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= lang('close') ?></button>
                <a data-fancy href="<?= base_url('categories/loan_type') ?>" class="btn btn-outline-primary"><?= lang('start_now') ?></a>
            </div>
        </div>
    </div>
</div>

<button type="button" id="credit_sales_notice_triger" style="display: none;" class="btn btn-dark" data-toggle="modal" data-target="#fill-dark-modal3"></button>
<div id="fill-dark-modal3" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fill-dark-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-filled bg-dark">
            <div class="modal-header">
                <h4 class="modal-title" id="fill-dark-modalLabel"><?= lang('notification') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p class="lead">
                    You need to setup the product plan/credit sales your cooperative operate first. Kindly follow the steps below to do that.
                </p>
                <p class="lead">
                    From the left side menu, select <b class="badge badge-primary"><?= lang('categories') ?></b> ,
                    then select <b class="badge badge-primary"><?= lang('product_type') ?>,</b>.
                </p>
                <img class="card-img-top" src="<?= $assets ?>images/guide/how_2.png" alt="Card image cap">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= lang('close') ?></button>
                <a data-fancy href="<?= base_url('categories/product_type') ?>" class="btn btn-outline-primary"><?= lang('start_now') ?></a>
            </div>
        </div>
    </div>
</div>

<?php if ($failed_upload) { ?>
    <div id="migration-error" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-wrong h1 text-danger"></i>
                        <h4 class="mt-2">Oh snap!</h4>
                        <p class="mt-3 text-danger">Your last upload has some errors that needs your attention</p>
                        <button type="button" class="btn btn-danger my-2" data-dismiss="modal">Close</button>
                        <a data-fancy href="<?= base_url('migration/'.$error_message_url) ?>" class="btn btn-primary my-2" >Open</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>