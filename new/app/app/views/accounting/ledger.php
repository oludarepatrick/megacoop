<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a data-fancy href="<?= base_url('accounting') ?>"class="btn btn-primary mb-2"><i class="mdi mdi-plus mr-2"></i><?= lang('ledger_entry') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal" ><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                        <div class=" clearfix"></div>
                        <h4><?= ucwords($ledger_name) .' '. lang('ledger')?> From <?=$start_date?> to <?=$end_date?></h4>
                    </div>
                </div>

                <table  class="table table-striped table-bordered dt-responsive nowrap w-100" id="basic-datatable-admin">
                    <thead>
                        <tr>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('pv_no') ?></th>
                            <th><?= lang('cr') ?></th>
                            <th><?= lang('dr') ?></th>
                            <th><?= lang('balance') ?></th>
                            <th><?= lang('narration') ?></th>
                            <th><?= lang('particular') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ledger as $st) { ?>
                            <tr>
                                <td><?= $st->payment_date ?></td>
                                <td><?= $st->pv_no ?></td>
                                <?php if($st->type =='credit') {?>
                                    <td><?= number_format($st->amount, 2) ?></td>
                                    <td>0.00</td>
                                <?php }?>
                                <?php if($st->type =='debit') {?>
                                    <td>0.00</td>
                                    <td><?= number_format($st->amount, 2) ?></td>
                                <?php }?>
                                <td><?= number_format($st->bal, 2) ?></td>
                                <td><?= ucfirst($st->note) ?></td>
                                <td><?= ucfirst($st->particular) ?></td>
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
                    <?= form_open('accounting/ledger/'.$ledger_id, 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-12">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" data-provide="datepicker" data-date-format="yyyy-m-d">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                            <input type="text" name="end_date" class="form-control" data-provide="datepicker" data-date-format="yyyy-m-d">
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