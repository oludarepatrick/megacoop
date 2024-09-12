<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a data-fancy href="<?= base_url('miscellaneous/add_minute') ?>" class="btn btn-primary mb-2"><i class="mdi mdi-plus mr-1"></i><?= lang('add') . ' ' . lang('minute') ?></a>
                        <!-- <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button> -->
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="basic-datatable">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('updated_on') ?></th>
                            <th><?= lang('title') ?></th>
                            <th><?= lang('status') ?> </th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($minutes as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= $this->utility->just_date($st->updated_on) ?></td>
                                <td><?= ucwords($st->title) ?></td>
                                <td><span class="badge badge-primary text-uppercase"> <?= $st->status ?> </span></td>
                                <td>
                                    <a data-fancy href="<?= base_url('miscellaneous/view_minute/' . $this->utility->mask($st->id)) ?>" title="<?= lang('preview') ?>" class="btn btn-info btn-sm"><i class="mdi mdi-eye-check"></i> </a>
                                    <a data-fancy href="<?= base_url('miscellaneous/edit_minute/' . $this->utility->mask($st->id)) ?>" title="<?= lang('edit') ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <!-- <a data-fancy href="<?= base_url('miscellaneous/delete_minute/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a> -->
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>