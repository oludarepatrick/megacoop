<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered w-100 dt-responsive tab nowrap table-striped table-bordered" id="datatable-buttons">
                        <thead>
                            <tr>
                                <th><?= lang('created_on') ?></th>
                                <th><?= lang('price') ?></th>
                                <th><?= lang('quantity') ?></th>
                                <th><?= lang('total') ?></th>
                                <th><?= lang('description') ?></th>
                                <th><?= lang('status') ?>(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p) { ?>
                                <tr>
                                    <td> <?= $this->utility->just_date($p->created_on, false) ?> </td>
                                    <td><?= number_format($p->price, 2) ?> </td>
                                    <td><?= $p->quantity ?> </td>
                                    <td><?= number_format($p->total, 2) ?> </td>
                                    <td><?= $p->description ?> </td>
                                    <td>
                                        <?php if ($p->status == 'delivered') { ?>
                                            <span class="badge badge-success-lighten text-uppercase"><?= $p->status ?></span>
                                        <?php } ?>
                                        <?php if ($p->status == 'processing') { ?>
                                            <span class="badge badge-info-lighten text-uppercase"><?= $p->status ?></span>
                                        <?php } ?>
                                        <?php if ($p->status == 'approved') { ?>
                                            <span class="badge badge-primary-lighten text-uppercase"><?= $p->status ?></span>
                                        <?php } ?>
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
                    <?= form_open('products/ordered_products', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" value="<?= set_value('start_date') ?>" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                            <input type="text" name="end_date" value="<?= set_value('end_date') ?>" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class="col mt-3">
                            <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant mr-1"></i><?= lang('apply_filter') ?></button>
                        </div>
                    </div>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>