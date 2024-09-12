<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
     <div class="col-md-8">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="mdi  mdi-cash-multiple text-primary" style="font-size: 24px;"></i>
                                
                                <h3><span class="text-primary"><?= number_format($credit_sales->total_due, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('total_due') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-refund text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= number_format($credit_sales->total_due - $credit_sales->total_remain, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total_repayment') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-multiple text-danger" style="font-size: 24px;"></i>
                                <h3><span class="text-danger"><?= number_format($credit_sales->total_remain, 2) ?></span></h3>
                                <p class="text-danger font-15 mb-0"><?= lang('total_balance') ?></p>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->
            </div>
        </div> <!-- end card-box-->
    </div> <!-- end col-->
</div>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card bg-gen">
            <div class="card-body">
                <table  class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-member">
                    <thead>
                        <tr>
                            <th>
                                <span><?= lang('my') ?> <?= lang('repayment_history') ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($credit_sales_repayment as $st) { ?>
                            <tr>
                                <td>
                                    <a data-fancy href="<?= base_url('member/creditsales/receipt/' . $this->utility->mask($st->id)) ?>" class="text-secondary">
                                        <div>
                                            <span class="font-weight-bolder"> <?= lang('date') ?></span>
                                            <span class="badge badge-primary float-right"><?= lang('amount')?> </span>
                                            <br>
                                            <br>
                                            <span> <?= $this->utility->just_date($st->created_on, FALSE) ?></span>
                                            <span class="float-right font-weight-bolder"><?= number_format($st->amount, 2) ?></span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>

<!--<div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <?= form_open('loanrepayment', 'class="needs-validation" novalidate') ?>
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
    </div> /.modal-content 
</div> /.modal-dialog -->