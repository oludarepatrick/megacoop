<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('guarantor') ?> <?= lang('req') ?></h4>
            </div>
            <div class="card-body">
                <span class="float-left m-2 mr-5">
                    <img src="<?= $assets ?>images/users/<?= $loan->avater ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                </span>
                <div class="media-body">
                    <h4 class="mt-1 mb-1"><?= ucwords($loan->full_name) ?></h4>
                    <p class="font-13"> <?= $loan->username ?></p>
                    <ul class="mb-0 list-inline">
                        <li class="list-inline-item mr-3">
                            <h5 class="mb-1"><?= $loan->phone ?></h5>
                            <p class="mb-0 font-13"><?= lang('phone') ?></p>
                        </li>
                        <li class="list-inline-item">
                            <h5 class="mb-1"><?= $loan->email ?></h5>
                            <p class="mb-0 font-13"><?= lang('email') ?></p>
                        </li>
                    </ul>
                </div> <!-- end card-body-->
            </div> <!-- end card-body-->
            <div class=" card-body">
                <h4><?= lang('hello') ?> <?= ucwords($this->user->first_name . ' ' . $this->user->last_name) ?></h4>
                <p>This is to notify you that a member whose details/ passport photograph appears above needs your approval as his/her
                    guarantor for the loan request scheduled as follows:</p>
            </div>
            <div class="card-body">
                <div class="table-responsive table-bordered">
                    <table class=" table mb-0">
                        <?php if ($src === 'loan') { ?>
                            <h5 class=" ml-2"><?= lang('schedule_for_requested_loan') ?></h5>
                        <?php } ?>
                        <?php if ($src === 'crs') { ?>
                            <h5 class=" ml-2"><?= lang('schedule_for_requested_credit_sales') ?></h5>
                        <?php } ?>
                        <thead class="thead-light">
                            <tr>
                                <th><?= lang('item') ?></td>
                                <th><?= lang('value') ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= lang('principal') ?></td>
                                <td><?= number_format($loan->principal, 2) ?></td>
                            </tr>

                            <tr>
                                <td><?= lang('interest') ?></td>
                                <td><?= number_format($loan->interest, 2) ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('total_due') ?></td>
                                <td><?= number_format($loan->total_due, 2) ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('monthly_due') ?></td>
                                <td><?= number_format($loan->monthly_due, 2) ?></td>
                            </tr>
                            <tr>
                                <td><?= lang('tenure') ?></td>
                                <td><?= $loan->tenure ?> <?= lang('month') ?>(s)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class=" card-body">
                <strong class="text-danger">By clicking approve: </strong><br>
                <span>1. You agree that you are aware of this and accept to be the guarantor</span><br>
                <span>2. You agree with the terms and condition of guarantor-ship of <?= ucfirst($this->coop->coop_name) ?> </span><br>
                <strong class="text-danger">Otherwise: </strong><br>
                <span>3. Kindly click decline</span>
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#decline"><?= lang('decline') ?></button>
                <a data-fancy href="<?= base_url("member/guarantor/approve/{$src}/" . $this->utility->mask($loan->id)) ?>" onclick="return confirm('Are you sure you want to accept')" class="btn btn-primary"><i class="mdi mdi-checkbox-marked-circle-outline mr-2"></i><?= lang('accept') ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="decline" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel"><?= lang('decline') ?> <?= lang('note') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open("member/guarantor/decline/{$src}/" . $this->utility->mask($loan->id), 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('note') ?>">Let <strong><?= ucwords($loan->full_name) ?></strong> why his/her request was rejected</label>
                    <textarea name="note" class="form-control" required> </textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block float-right"><?= lang('proceed') ?></button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>