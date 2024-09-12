<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <!--<button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal" ><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>-->
                    </div>
                </div>

                <table  class="table table-striped table-bordered dt-responsive nowrap w-100" id="datatable-buttons">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('tranx_ref') ?></th>
                             <th><?= lang('licence') ?></th>
                             <th><?= lang('member') ?>s</th>
                            <th><?= lang('start_date') ?></th>
                            <th><?= lang('end_date') ?></th>
                            <th><?= lang('status') ?></th>
<!--                            <th style="width: 70px"><?= lang('actions') ?></th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licence as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= $st->tranx_ref ?></td>
                                
                                <td><?= $st->subs_cat ?></td>
                                <td><?= $st->unit ?></td>
                                <td><?=$st->start_date ?></td>
                                <td><?= $st->end_date ?></td>
                                <td>
                                    <?php if ($st->status == 'successful') { ?>
                                        <span class="badge badge-success"><?= $st->status ?></span>
                                    <?php } ?>
                                    <?php if ($st->status == 'processing') { ?>
                                        <span class="badge badge-primary"><?= $st->status ?></span>
                                    <?php } ?>
                                    <?php if ($st->status == 'failed') { ?>
                                        <span class="badge badge-danger"><?= $st->status ?></span>
                                    <?php } ?>
                                </td>
<!--                                <td>
                                    <a data-fancy href="<?= base_url('loan/preview/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('preview') ?>"  class="btn btn-info btn-sm"><i class="mdi mdi-dots-vertical"></i> </a>
                                    <a data-fancy href="<?= base_url('loan/edit/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('edit') ?>"  class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <a data-fancy href="<?= base_url('loan/delete/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>"  class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
                                </td>-->
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>

<div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content ">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="border border-light rounded bg-primary p-2 mb-3 text-white">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="m-0 mb-1"><?= lang('filter') ?></h4>
                        </div>
                    </div>
                    <p><?= lang('filter_note') ?></q>
                    </p>
                </div>
                <div class="">
                    <?= form_open('wallet', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-12">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" value="<?= set_value('start_date') ?>" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                            <input type="text" name="end_date" value="<?= set_value('end_date') ?>"  class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class="col mt-3">
                            <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant mr-1"></i><?= lang('apply_filter') ?></button>
                        </div>
                    </div>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->