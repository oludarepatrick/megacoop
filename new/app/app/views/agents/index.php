<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a data-fancy href="<?= base_url('registration/add_member') ?>" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('add_agent') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal" ><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter')?></button>
                    </div>
                </div>
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('phone') ?></th>
                            <th><?= lang('status') ?></th>
                            <th style="width: 80px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($members as $m) { ?>
                            <tr>
                                <td><?= date('Y-m-d g:i', $m->created_on) ?></td>
                                <td><?= $m->username ?></td>
                                <td><?= ucwords($m->first_name . ' ' . $m->last_name) ?></td>
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
                                    <a data-fancy href="<?= base_url('agents/fund/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="<?= lang('fund_agent') ?>"  class="btn btn-success btn-sm"><i class="mdi mdi-wallet-plus"></i> </a>
                                    <a data-fancy href="<?= base_url('registration/profile/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="<?= lang('profile') ?>"  class="btn btn-info btn-sm"><i class="mdi mdi-account-box"></i> </a>
                                    <a onclick="return confirm('Are you sure you want to delete')" data-fancy href="<?= base_url('registration/delete_member/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="<?= lang('delete') ?>"  class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
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
                    <?= lang('filter')?>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->