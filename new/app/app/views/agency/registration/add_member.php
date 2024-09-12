<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <?= form_open_multipart('agency/registration/add_member', 'class="needs-validation" novalidate') ?>
                <div class="form-group position-relative mb-3">
                    <h5 class=" text-uppercase bg-light p-2"><i class="mdi mdi-account-box mr-1"></i> <?= lang('personal_details') ?></h5>
                    <div class="row">
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                            <input type="text" name="username" class="form-control" id="username" value="<?= set_value('username', $member_id) ?>" readonly required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('first_name') ?>"><?= lang('first_name') ?></label>
                            <input type="text" name="first_name" class="form-control" id="first_name" value="<?= set_value('first_name') ?>" required>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('last_name') ?>"><?= lang('last_name') ?></label>
                            <input type="text" name="last_name" class="form-control" id="last_name" value="<?= set_value('last_name') ?>" required>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('email') ?>"><?= lang('email') ?></label>
                            <input type="email" name="email" class="form-control" id="email" value="<?= set_value('email') ?>">
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('phone') ?>"><?= lang('phone') ?></label>
                            <input type="number" name="phone" class="form-control" id="phone" value="<?= set_value('phone') ?>" required>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('password') ?>"><?= lang('password') ?></label>
                            <div id="message" style="display: none;">
                                <small id="letter" class="text-danger">A lowercase,</small>
                                <small id="capital" class="text-danger">uppercase,</small>
                                <small id="number" class="text-danger">number</small>
                                <small id="length" class="text-danger">and 8 characters min. required!</b></small>
                            </div>
                            <input type="text" name="password" class="form-control" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" value="<?= set_value('password') ?>" required>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('dob') ?>"><?= lang('dob') ?></label>
                            <input type="text" name="dob" class="form-control" id="dob" value="<?= set_value('dob') ?>" data-provide="datepicker" data-date-format="d-M-yyyy">
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('gender') ?>"><?= lang('gender') ?></label>
                            <?php $gender = ['male' => 'Male', 'female' => 'Female']; ?>
                            <?= form_dropdown('gender', $gender, set_value('gender'), 'class="form-control select2"  id="gender" data-toggle="select2"'); ?>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('marital_status') ?>"><?= lang('marital_status') ?></label>
                            <?php $marital_status = ['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced']; ?>
                            <?= form_dropdown('marital_status', $marital_status, set_value('marital_status'), 'class="form-control select2"  id="marital_status" data-toggle="select2"'); ?>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-8 mt-3">
                            <label for="<?= lang('address') ?>"><?= lang('address') ?></label>
                            <input type="text" name="address" class="form-control" id="address" value="<?= set_value('address') ?>" required>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label><?= lang('passport') ?> <small> (Maximum upload size, 100kb)</small></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                                    <label class="custom-file-label" for="inputGroupFile04" id="file_label"> </label>
                                </div>
                            </div>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-6 mt-3">
                            <label for="<?= lang('id_card_id') ?>"><?= lang('select') ?> <?= lang('id_card') ?></label>
                            <?= form_dropdown('id_card_id', $id_card, set_value('id_card_id'), 'class="form-control select2"  id="id_card_id" data-toggle="select2"'); ?>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-6 mt-3">
                            <label><?= lang('upload') . ' ' . lang('id_card') ?> <small> (Maximum upload size, 100kb)</small></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="id_card" class="custom-file-input" oninput="file_upload_fix('id_card', 'id_card_label')" id="id_card">
                                    <label class="custom-file-label" for="id_card" id="id_card_label"> </label>
                                </div>
                            </div>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                    </div>

                    <h5 class=" text-uppercase mt-4 mb-2 bg-light p-2"><i class="mdi mdi-account-circle mr-1"></i> <?= lang('personal_coop_details') ?></h5>
                    <div class="row">
                        <div class=" col-md-4 mt-3">
                            <label for="month_id"><?= lang('start_month') ?></label>
                            <?php
                            $mt[""] = "Select";
                            foreach ($months as $m) {
                                $mt[$m->id] = $m->name;
                            }
                            ?>
                            <?= form_dropdown('month_id', $mt, set_value('month_id'), 'class="form-control select2" name="month_id"  id="month_id" data-toggle="select2"'); ?>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="year"><?= lang('start_year') ?></label>
                            <?php
                            $yr[""] = "Select";
                            for ($i = 2018; $i <= date('Y'); $i++) {
                                $yr[$i] = $i;
                            }
                            ?>
                            <?= form_dropdown('year', $yr, set_value('year'), 'class="form-control select2" name="year"  id="year" data-toggle="select2"'); ?>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('reg_fee') ?>"><?= lang('reg_fee') ?></label>
                            <input type="text" name="reg_fee" class="form-control" id="reg_fee" value="<?= set_value('reg_fee') ?>">
                        </div>
                        <?php foreach ($savings_types as $s) { ?>
                            <div class=" col-md-4 mt-3">
                                <label> <?= lang('savings') . ' ' . lang('amount') ?> (<?= $s->name ?>)</label>
                                <input type="number" name="savings_amount[<?= $s->id ?>]" class="form-control" value="0">
                            </div>
                        <?php } ?>
                    </div>
                    <h5 class=" text-uppercase mt-4 mb-2 bg-light p-2"><i class="mdi mdi-currency-usd mr-1"></i> <?= lang('bank_details') ?></h5>
                    <div class="row">
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('acc_name') ?>"><?= lang('acc_name') ?></label>
                            <input type="text" name="acc_name" class="form-control" id="acc_name" value="<?= set_value('acc_name') ?>">
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('acc_no') ?>"><?= lang('acc_no') ?></label>
                            <input type="number" name="acc_no" class="form-control" id="acc_no" value="<?= set_value('acc_no') ?>">
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="bank_id"><?= lang('bank') ?></label>
                            <?php
                            $bk[""] = "Select";
                            foreach ($banks as $b) {
                                $bk[$b->id] = $b->bank_name;
                            }
                            ?>
                            <?= form_dropdown('bank_id', $bk, set_value('bank_id'), 'class="form-control select2" id="bank_id" data-toggle="select2"'); ?>
                            <div class="valid-tooltip">
                                <?= lang('looks_good') ?>
                            </div>
                            <div class="invalid-tooltip">
                                <?= lang('field_required') ?>
                            </div>
                        </div>
                    </div>
                    <h5 class=" text-uppercase mt-4 mb-2 bg-light p-2"><i class="mdi mdi-account-heart mr-1"></i> <?= lang('kin_details') ?></h5>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-group input_fields_wrap">
                                <div class="row mt-2">
                                    <div class=" form-group col-md-3">
                                        <input placeholder="Next of Kin Full Name" class="form-control" type="text" name="kin_name[]">
                                    </div>
                                    <div class=" form-group col-md-3">
                                        <input placeholder="Next of Kin Phone" class="fee_val form-control" type="number" name="kin_phone[]">
                                    </div>
                                    <div class=" form-group col-md-5">
                                        <input placeholder="Next of Kin Address" class="fee_val form-control" type="text" name="kin_address[]">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 mt-2">
                            <button class="btn-primary btn btn-block add_field_button" type="button"><i class="mdi mdi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary float-right" type="submit"> <i class="mdi mdi-floppy mr-1"></i> <?= lang('add_member') ?></button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>