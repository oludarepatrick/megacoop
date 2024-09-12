<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-sm-12">
        <!-- Profile -->
        <div class="card bg-primary">
            <div class="card-body profile-user-box">

                <div class="row">
                    <div class="col-sm-8">
                        <div class="media">
                            <span class="float-left m-2 mr-4"><img src="<?= $assets ?>images/users/<?= $user->avatar ?>" style="height: 100px;" alt="" class="rounded-circle img-thumbnail"></span>
                            <div class="media-body">

                                <h4 class="mt-1 mb-1 text-white"><?= ucwords($user->first_name . ' ' . $user->last_name) ?></h4>
                                <p class="font-13 text-white-50"> <?= ucwords($user->g_name) ?></p>

                                <ul class="mb-0 list-inline text-light">
                                    <li class="list-inline-item mr-3">
                                        <h5 class="mb-1"><?= date('Y-M-d g:i:s a', $user->last_login) ?></h5>
                                        <p class="mb-0 font-13 text-white-50"><?= lang('last_login') ?></p>
                                    </li>
                                </ul>
                            </div> <!-- end media-body-->
                        </div>
                    </div> <!-- end col-->

                    <div class="col-sm-4">
                        <div class="text-center mt-sm-0 mt-3 text-sm-right">
                            <button type="button" class="btn btn-light">
                                <i class="mdi mdi-account-edit mr-1"></i> Edit Profile
                            </button>
                        </div>
                    </div> <!-- end col-->
                </div> <!-- end row -->

            </div> <!-- end card-body/ profile-user-box-->
        </div><!--end profile/ card -->
    </div> <!-- end col-->
</div>
<!-- end row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?= form_open_multipart('registration/edit_member/'.$this->utility->mask($user->id), 'class="needs-validation" novalidate') ?>
                <div class="form-group position-relative mb-3">
                    <h5 class=" text-uppercase bg-light p-2"><i class="mdi mdi-account-box mr-1"></i> <?= lang('personal_details') ?></h5>
                    <div class="row">
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('member_id') ?>"><?= lang('member_id') ?></label>
                            <input type="text" name="username" class="form-control" id="username" value="<?= set_value('username', $user->username) ?>" readonly required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('first_name') ?>"><?= lang('first_name') ?></label>
                            <input type="text" name="first_name" class="form-control" id="first_name" value="<?= set_value('first_name', $user->first_name) ?>" required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('last_name') ?>"><?= lang('last_name') ?></label>
                            <input type="text" name="last_name" class="form-control" id="last_name" value="<?= set_value('last_name', $user->last_name) ?>" required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('email') ?>"><?= lang('email') ?></label>
                            <input type="email" name="email" class="form-control" id="email" value="<?= set_value('email', $user->email) ?>" readonly required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('phone') ?>"><?= lang('phone') ?></label>
                            <input type="number" name="phone" class="form-control" id="phone" value="<?= set_value('phone', $user->phone) ?>" required>

                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('dob') ?>"><?= lang('dob') ?></label>
                            <input type="text" name="dob" class="form-control" id="dob" value="<?= set_value('dob', $user->dob) ?>" data-provide="datepicker" data-date-format="d-M-yyyy">
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('gender') ?>"><?= lang('gender') ?></label>
                            <?php $gender = ['male' => 'Male', 'female' => 'Female']; ?>
                            <?= form_dropdown('gender', $gender, set_value('gender', $user->gender), 'class="form-control select2"  id="gender" data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('marital_status') ?>"><?= lang('marital_status') ?></label>
                            <?php $marital_status = ['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced']; ?>
                            <?= form_dropdown('marital_status', $marital_status, set_value('marital_status', $user->marital_status), 'class="form-control select2"  id="marital_status" data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-8 mt-3">
                            <label for="<?= lang('address') ?>"><?= lang('address') ?></label>
                            <input type="text" name="address" class="form-control" id="address" value="<?= set_value('address', $user->address) ?>" required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label><?= lang('passport') ?> <small> (Maximum upload size, 100kb)</small></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                                    <label class="custom-file-label" for="inputGroupFile04" id="file_label"> </label>
                                </div>
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
                            <?= form_dropdown('month_id', $mt, set_value('month_id', $user->month_id), 'class="form-control select2" name="month_id"  id="month_id" data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="year"><?= lang('start_year') ?></label>
                            <?php
                            $yr[""] = "Select";
                            for ($i = 2018; $i <= date('Y'); $i++) {
                                $yr[$i] = $i;
                            }
                            ?>
                            <?= form_dropdown('year', $yr, set_value('year', $user->year), 'class="form-control select2" name="year"  id="year" data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('monthly_savings') ?>"><?= lang('monthly_savings') ?></label>
                            <input type="text" name="monthly_savings" class="form-control" id="monthly_savings" value="<?= set_value('monthly_savings', $user->monthly_savings) ?>" required>
                        </div>

                    </div>
                    <h5 class=" text-uppercase mt-4 mb-2 bg-light p-2"><i class="mdi mdi-currency-usd mr-1"></i> <?= lang('bank_details') ?></h5>
                    <div class="row">
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('acc_name') ?>"><?= lang('acc_name') ?></label>
                            <input type="text" name="acc_name" class="form-control" id="acc_name" value="<?= set_value('acc_name', $user->acc_name) ?>" required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="<?= lang('acc_no') ?>"><?= lang('acc_no') ?></label>
                            <input type="text" name="acc_no" class="form-control" id="acc_no" value="<?= set_value('acc_no', $user->acc_no) ?>" required>
                        </div>
                        <div class=" col-md-4 mt-3">
                            <label for="bank_id"><?= lang('bank') ?></label>
                            <?php
                            $bk[""] = "Select";
                            foreach ($banks as $b) {
                                $bk[$b->id] = $b->bank_name;
                            }
                            ?>
                            <?= form_dropdown('bank_id', $bk, set_value('bank_id', $user->bank_id), 'class="form-control select2" id="bank_id" data-toggle="select2"'); ?>
                        </div>
                    </div>
                    <h5 class=" text-uppercase mt-4 mb-2 bg-light p-2"><i class="mdi mdi-account-heart mr-1"></i> <?= lang('kin_details') ?></h5>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-group input_fields_wrap">
                                <?php
                                if($kin_details) {
                                foreach ($kin_details as $d) { ?>
                                    <div class="row mt-2">
                                        <div class=" form-group col-md-3">
                                            <input placeholder="Next of Kin Full Name" class="form-control" type="text" value="<?= $d[0] ?>" name="kin_name[]"> 
                                        </div>
                                        <div class=" form-group col-md-3">
                                            <input placeholder="Next of Kin Phone" class="fee_val form-control" type="number" value="<?= $d[1] ?>" name="kin_phone[]"> 
                                        </div>
                                        <div class=" form-group col-md-5">
                                            <input placeholder="Next of Kin Address" class="fee_val form-control" type="text" value="<?= $d[2] ?>" name="kin_address[]"> 
                                        </div>
                                    </div>
                                <?php }
                                 } ?>
                            </div>
                        </div>
                        <div class="col-md-1 mt-2">
                            <button class="btn-primary btn btn-block add_field_button" type="button"><i class="mdi mdi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary float-right" type="submit"> <i class="mdi mdi-floppy mr-1"></i> <?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>