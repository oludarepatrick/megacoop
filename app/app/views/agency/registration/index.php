<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-user-group text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $total_approved ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('approved_members') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-account-remove text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= $total_pending ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('pending_members') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-exit text-danger" style="font-size: 24px;"></i>
                                <h3><span class="text-danger"><?= $total_exit ?></span></h3>
                                <p class="text-danger font-15 mb-0"><?= lang('exit_members') ?></p>
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
                        <a data-fancy href="<?= base_url('agency/registration/add_member') ?>" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('add_member') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <!-- <th><?= lang('date') ?></th> -->
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('phone') ?></th>
                            <th><?= lang('status') ?></th>
                            <th style="width: 80px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($members as $m) { ?>
                            <tr>
                                <!-- <td><?= date('Y-m-d g:i', $m->created_on) ?></td> -->
                                <td><?= ucwords($m->first_name . ' ' . $m->last_name) ?></td>
                                <td><?= $m->username ?></td>
                                <td><?= $m->phone ?></td>
                                <td>
                                    <?php if ($m->status == 'approved') { ?>
                                        <span class="badge badge-success"><?= $m->status ?></span>
                                    <?php } ?>
                                    <?php if ($m->status == 'pending') { ?>
                                        <span class="badge badge-primary"><?= $m->status ?></span>
                                    <?php } ?>
                                    <?php if ($m->status == 'exit') { ?>
                                        <span class="badge badge-danger"><?= $m->status ?></span>
                                    <?php } ?>

                                </td>
                                <td>
                                    <a data-fancy href="<?= base_url('agency/registration/profile/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="<?= lang('profile') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-account-box"></i> </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <?= lang('filter') ?>
                </div>
            </div>
        </div>
    </div>
</div>