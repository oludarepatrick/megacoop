<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card bg-gen">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable">
                        <thead>
                            <tr>
                                <th>
                                    <span><?= lang('all_request') ?></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loan_guaranteed as $st) { ?>
                                <tr>
                                    <td><a data-fancy href="<?= base_url('member/guarantor/preview/loan/' . $this->utility->mask($st->loan_id)) ?>" class="text-secondary">
                                            <div class="media">
                                                <?php if (!$st->avatar) { ?>
                                                    <div class="avatar-sm mr-2">
                                                        <span class="avatar-title bg-primary rounded">
                                                            <i class="mdi mdi-account-plus"></i>
                                                        </span>
                                                    </div>
                                                <?php } else { ?>
                                                    <img class="mr-3 rounded-circle" src="<?= $assets ?>images/users/<?= $st->avatar ?>" width="40">
                                                <?php } ?>
                                                <div class="media-body">
                                                    <span class="badge badge-primary float-right d-none d-sm-inline-block"><?= $st->status ?></span>
                                                    <h5 class="mt-0 mb-2"><?= ucwords($st->full_name) ?></h5>
                                                    <span class="font-13"><?= $st->username ?></span>
                                                    <span class=" float-right d-none d-sm-inline-block"><?= $this->utility->just_date($st->request_date) ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($credit_sales_guaranteed as $st) { ?>
                                <tr>
                                    <td><a data-fancy href="<?= base_url('member/guarantor/preview/crs/' . $this->utility->mask($st->credit_sales_id)) ?>" class="text-secondary">
                                            <div class="media">
                                                <img class="mr-3 rounded-circle" src="<?= $assets ?>images/users/<?= $st->avatar ?>" width="40" alt="<?= $st->full_name ?>">
                                                <div class="media-body">
                                                    <span class="badge badge-primary float-right d-none d-sm-inline-block"><?= $st->status ?></span>
                                                    <h5 class="mt-0 mb-2"><?= ucwords($st->full_name) ?></h5>
                                                    <span class="font-13"><?= $st->username ?></span>
                                                    <span class=" float-right d-none d-sm-inline-block"><?= $this->utility->just_date($st->request_date) ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>