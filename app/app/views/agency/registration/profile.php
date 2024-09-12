<!-- Start Content-->
<div class="container-fluid">

    <div class="row">

        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="card">

                <div class="row">
                    <div class="col-4">
                        <h4 class="header-title mt-4 ml-2"><?= lang('profile') ?></h4>
                    </div>
                </div>


                <div id="basicwizard">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-2">
                            <li class="nav-item">
                                <a href="#basictab1" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                    <i class="mdi mdi-account-circle mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('member_details') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-cash-plus mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('account') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab3" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-briefcase mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('aggregate') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active show" id="basictab1">
                            <div class="card-body">
                                <span class="float-left m-2 mr-5">
                                    <img src="<?= $assets ?>images/users/<?= $user->avatar ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                                </span>
                                <div class="media-body">
                                    <h4 class="mt-1 mb-1"><?= ucwords($user->first_name . ' ' . $user->last_name) ?></h4>
                                    <p class="font-13"> <?= $user->username ?></p>
                                    <ul class="mb-0 list-inline">
                                        <li class="list-inline-item mr-3">
                                            <h5 class="mb-1"><?= date('Y-M-d g:i:s a', $user->last_login) ?></h5>
                                            <p class="mb-0 font-13"><?= lang('last_login') ?></p>
                                        </li>
                                        <li class="list-inline-item">
                                            <h5 class="mb-1"><?= date('Y-M-d', $user->created_on) ?></h5>
                                            <p class="mb-0 font-13"><?= lang('member_since') ?></p>
                                        </li>
                                        <li class="list-inline-item mr-3">
                                            <p class="mb-0 font-13"><?= lang('approved') ?></p>
                                        </li>
                                    </ul>
                                </div>
                                <?= form_open_multipart('agency/registration/edit_member/' . $this->utility->mask($user->id), 'class="needs-validation" novalidate') ?>
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
                                            <a href="!#" data-target="#centermodal" data-toggle="modal" class="btn-light btn-sm float-right">
                                                <i class="mdi mdi-pen" data-toggle="tooltip" data-original-title="Edit email"> </i>
                                            </a>
                                            <input type="email" name="email" class="form-control" id="email" value="<?= set_value('email', $user->email) ?>" readonly>
                                        </div>
                                        <div class=" col-md-4 mt-3">
                                            <label for="<?= lang('phone') ?>"><?= lang('phone') ?></label>
                                            <a href="!#" data-target="#edit_phone" data-toggle="modal" class="btn-light btn-sm float-right">
                                                <i class="mdi mdi-pen" data-toggle="tooltip" data-original-title="Edit phone number"> </i>
                                            </a>
                                            <input type="number" name="phone" readonly class="form-control" id="phone" value="<?= set_value('phone', $user->phone) ?>" required>
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
                                            <label for="<?= lang('reg_fee') ?>"><?= lang('reg_fee') ?></label>
                                            <input type="text" name="reg_fee" class="form-control" id="reg_fee" value="<?= set_value('reg_fee') ?>">
                                        </div>
                                        <?php foreach ($savings_types as $s) {
                                            $s_amt = json_decode($user->savings_amount, true);
                                        ?>
                                            <div class=" col-md-4 mt-3">
                                                <label> <?= lang('savings') . ' ' . lang('amount') ?> (<?= $s->name ?>)</label>
                                                <input type="number" name="savings_amount[<?= $s->id ?>]" class="form-control" value="<?= $s_amt[$s->id] ?>">
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <h5 class=" text-uppercase mt-4 mb-2 bg-light p-2"><i class="mdi mdi-currency-usd mr-1"></i> <?= lang('bank_details') ?></h5>
                                    <div class="row">
                                        <div class=" col-md-4 mt-3">
                                            <label for="<?= lang('acc_name') ?>"><?= lang('acc_name') ?></label>
                                            <input type="text" name="acc_name" class="form-control" id="acc_name" value="<?= set_value('acc_name', $user->acc_name) ?>">
                                        </div>
                                        <div class=" col-md-4 mt-3">
                                            <label for="<?= lang('acc_no') ?>"><?= lang('acc_no') ?></label>
                                            <input type="text" name="acc_no" class="form-control" id="acc_no" value="<?= set_value('acc_no', $user->acc_no) ?>">
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
                                                if ($kin_details) {
                                                    foreach ($kin_details as $d) {
                                                ?>
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
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-1 mt-2">
                                            <button class="btn-primary btn btn-block add_field_button" type="button"><i class="mdi mdi-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary float-right" type="submit"> <i class="mdi mdi-floppy mr-1"></i> <?= lang('save') ?></button>
                                <div class="clearfix"></div>
                                <?= form_close() ?>
                            </div>
                        </div>

                        <div class="tab-pane mb-4" id="basictab2">
                            <div class=" card-body">
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-danger"><?= lang('loans') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100 ">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('loan_type') ?></th>
                                                <th><?= lang('total') ?></th>
                                                <th><?= lang('paid') ?></th>
                                                <th><?= lang('balance') ?></th>
                                                <th><?= lang('status') ?></th>
                                                <th><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($existing_loans as $el) { ?>

                                                <tr>
                                                    <td> <?= $el->loan_type ?></td>
                                                    <td> <?= number_format($el->total_due, 2) ?></td>
                                                    <td class="text-success"> <?= number_format($el->total_due - $el->total_remain, 2) ?></td>
                                                    <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                    <td> <?= $el->status ?></td>
                                                    <td>
                                                        <a data-fancy href="<?= base_url('loanrepayment/repayment_history/' . $this->utility->mask($el->id)) ?>" data-toggle="tooltip" title="<?= lang('details') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-menu"></i> </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-danger"><?= lang('credit_sales') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100 ">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('loan_type') ?></th>
                                                <th><?= lang('total') ?></th>
                                                <th><?= lang('paid') ?></th>
                                                <th><?= lang('balance') ?></th>
                                                <th><?= lang('status') ?></th>
                                                <th><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($existing_credit_sales as $el) { ?>

                                                <tr>
                                                    <td> <?= $el->product_type ?></td>
                                                    <td> <?= number_format($el->total_due, 2) ?></td>
                                                    <td class="text-success"> <?= number_format($el->total_due - $el->total_remain, 2) ?></td>
                                                    <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                    <td> <?= $el->status ?></td>
                                                    <td>
                                                        <a data-fancy href="<?= base_url('creditsalesrepayment/repayment_history/' . $this->utility->mask($el->id)) ?>" data-toggle="tooltip" title="<?= lang('details') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-menu"></i> </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-danger"><?= lang('savings') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('savings_type') ?></th>
                                                <th><?= lang('total') ?> <?= lang('savings') ?></th>
                                                <th><?= lang('balance') ?></th>
                                                <th><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($savings as $sav) { ?>

                                                <tr>
                                                    <td> <?= $sav->name ?></td>
                                                    <td> <?= number_format($sav->total_savings, 2) ?></td>
                                                    <td> <?= number_format($sav->bal, 2) ?></td>
                                                    <td>
                                                        <a data-fancy href="<?= base_url('savings/savings_history/' . $this->utility->mask($sav->savings_type) . '/' . $this->utility->mask($user->id)) ?>
                                                           " data-toggle="tooltip" title="<?= lang('details') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-menu"></i> </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="basictab3">
                            <div class="card-body">
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-primary"><?= lang('loans') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('loan_type') ?></th>
                                                <th><?= lang('balance') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($active_loans as $el) { ?>
                                                <tr>
                                                    <td> <?= $el->loan_type ?></td>
                                                    <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($all_loan_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-primary"><?= lang('credit_sales') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('product_type') ?></th>
                                                <th><?= lang('balance') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($active_credit_sales as $el) { ?>
                                                <tr>
                                                    <td> <?= $el->product_type ?></td>
                                                    <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($all_credit_sales_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-primary"><?= lang('savings') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('savings_type') ?></th>
                                                <th><?= lang('balance') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($savings as $sav) { ?>
                                                <tr>
                                                    <td> <?= $sav->name ?></td>
                                                    <td> <?= number_format($sav->bal, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($all_savings_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered">
                                    <h5 class="ml-2 text-primary mb-0"><?= lang('liquidity') ?></h5>
                                    <small class="ml-2"><?= lang('payable_amount') ?></small>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?> <?= lang('liquidity') ?></th>
                                                <th><?= number_format($all_savings_bal - $all_loan_bal - $all_credit_sales_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel"><?= lang('edit') . ' ' . lang('email') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?= form_open('agency/registration/update_email/' . $this->utility->mask($user->id), 'class="needs-validation" novalidate') ?>
                    <div class="form-group">
                        <label for="<?= lang('email') ?>"><?= lang('email') ?></label>
                        <input type="email" name="email" class="form-control" id="email" value="<?= set_value('email', $user->email) ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right my-2"><?= lang('edit') ?></button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_phone" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel"><?= lang('edit') . ' ' . lang('phone') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?= form_open('agency/registration/update_phone/' . $this->utility->mask($user->id), 'class="needs-validation" novalidate') ?>
                    <div class="form-group">
                        <label for="<?= lang('phone') ?>"><?= lang('phone') ?></label>
                        <input type="phone" name="phone" class="form-control" id="phone" value="<?= set_value('phone', $user->phone) ?>" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right my-2"><?= lang('edit') ?></button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>