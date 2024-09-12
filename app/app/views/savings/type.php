<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card widget-inline">
            <div class="card-header">
                <h5><i class="mdi mdi-timer text-primary"> </i></span> <?=$savings_type->name?></h5>
            </div>
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-6">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-graph-bar text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= number_format($total_savings->amount, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total_savings') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-6">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-graph-pie text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= number_format($filter_total_savings->amount, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= $month . ' ' . $year ?> <?= lang('savings') ?></p>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->
            </div>
        </div> <!-- end card-box-->
    </div> <!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a data-fancy href="<?= base_url('savings/add') ?>"class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('add_savings') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal" ><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <table  class="table table-striped table-bordered dt-responsive nowrap w-100" id="datatable-buttons">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('savings_type') ?></th>
                            <th><?= lang('narration') ?></th>
                            <th><?= lang('month') ?></th>
                            <th><?= lang('year') ?></th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($savings as $st) { ?>
                            <tr>
                                <td><?= $st->created_on ?></td>
                                <td><?= $st->username ?></td>
                                <td><?= ucfirst($st->full_name) ?></td>
                                <td><?= number_format($st->amount, 2) ?></td>
                                <td><?= ucfirst($st->savings_type) ?></td>
                                <td><?= ucfirst($st->narration) ?></td>
                                <td><?= ucfirst($st->month) ?></td>
                                <td><?= $st->year ?></td>
                                <td>
                                    <a data-fancy href="<?= base_url('savings/preview/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('print') ?>"  class="btn btn-info btn-sm"><i class="mdi mdi-printer"></i> </a>
                                    <a data-fancy href="<?= base_url('savings/edit/' . $this->utility->mask($st->id)) ?>" data-toggle="tooltip" title="<?= lang('edit') ?>"  class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <?php if ($st->source != 1) { ?> 
                                        <a data-fancy href="<?= base_url('savings/delete/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete') ?>"  class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
                                    <?php } ?> 
                                </td>
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
                    <?= form_open('savings/type', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-12">
                            <label for="<?= lang('month') ?>"><?= lang('month') ?></label>
                            <input type="text" name="month" class="form-control" data-provide="datepicker" data-date-format="MM" data-date-min-view-mode="1">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('year') ?>"><?= lang('year') ?></label>
                            <input type="text" name="year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
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
</div><!-- /.modal -->