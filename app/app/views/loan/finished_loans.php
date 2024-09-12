<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-header">
                <h5><i class="mdi mdi-timer text-primary"> </i></span><?= lang('finished_loans_btw') ?><?= $this->utility->just_date($start_date) . ' and ' . $this->utility->just_date($end_date) ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-suitcase text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= number_format($principal, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('principal') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-plus text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= number_format($interest, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('interest') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-multiple" style="font-size: 24px;"></i>
                                <h3><span class=""><?= number_format($principal + $interest, 2) ?></span></h3>
                                <p class=" font-15 mb-0"><?= lang('total_due') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>
                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="datatable-buttons">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('principal') ?></th>
                            <th><?= lang('total_due') ?></th>
                            <th><?= lang('refinance') ?></th>
                            <th><?= lang('loan_type') ?></th>
                            <!-- <th><?= lang('actions') ?></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= $st->username ?></td>
                                <td><?= ucfirst($st->full_name) ?></td>
                                <td><?= number_format($st->principal, 2) ?></td>
                                <td><?= number_format($st->total_due, 2) ?></td>
                                <td>
                                    <?php if ($st->refinance_data) { ?>
                                        <h4 class="badge badge-primary-lighten"> YES </h4>
                                    <?php } else { ?>
                                        <h4 class="badge badge-danger-lighten"> NO </h4>
                                    <?php } ?>
                                </td>
                                <td><?= ucfirst($st->loan_type) ?></td>
                                <!-- <td>
                                    <a data-fancy href="<?= base_url('loan/refinance_details/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('add_repayment') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-cash-plus"></i> </a>
                                </td> -->
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div> 
        </div> 
    </div> 
</div>
