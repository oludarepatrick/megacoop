<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th><?= lang('exit_date') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('status') ?></th>
                            <th style="width: 80px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($members as $m) { ?>
                            <tr>
                                <td><?= $m->request_date ?></td>
                                <td><?= $m->username ?></td>
                                <td><?= ucwords($m->first_name . ' ' . $m->last_name) ?></td>
                                <td>
                                    <?php if ($m->status == 'completed') { ?>
                                        <span class="badge badge-success"><?= $m->status ?></span>
                                    <?php } ?>
                                    <?php if ($m->status == 'processing') { ?>
                                        <span class="badge badge-primary"><?= $m->status ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a data-fancy href="<?= base_url('memberexit/preview/' . $this->utility->mask($m->member_exit_id)) ?>" data-toggle="tooltip" title="<?= lang('profile') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-account-box"></i> </a>
                                    <a onclick="return confirm('Are you sure you want to re-activate member')" data-fancy href="<?= base_url('memberexit/reactivate/' . $this->utility->mask($m->member_exit_id)) ?>" data-toggle="tooltip" title="Reactivate Member" class="btn btn-warning btn-sm"><i class="mdi mdi-check"></i> </a>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->