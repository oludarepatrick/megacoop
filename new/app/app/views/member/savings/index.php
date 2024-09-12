<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-6">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-graph-bar text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $this->country->currency ?> <?= number_format($total_savings->amount, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total_savings') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-6">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="dripicons-graph-pie text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= $this->country->currency ?> <?= number_format($filter_total_savings->amount, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= $year ?> <?= lang('savings') ?></p>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->
            </div>
        </div> <!-- end card-box-->
    </div> <!-- end col-->
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-gen">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary float-right m-1" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant" data-toggle="tooltip" title="Filter"></i></button>
                        <a data-fancy href="<?= base_url('member/savings/add') ?>" class="btn btn-primary float-right m-1" data-toggle="tooltip" title="Add Savings"><i class="mdi mdi-plus"></i></a>
                    </div>
                </div>

                <table class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-member">
                    <thead>
                        <tr>
                            <th>
                                <span><i class="mdi mdi-calendar-range"> </i></span><?= $year ?> <?= lang('savings') ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($savings as $st) { ?>
                            <tr>
                                <td>
                                    <a data-fancy href="<?= base_url('member/savings/preview/' . $this->utility->mask($st->id)) ?>" class="text-secondary">
                                        <div>
                                            <span class="font-weight-bolder"> <?= ucfirst($st->savings_type) ?></span>
                                            <span class="badge badge-primary float-right"><?= $st->month . ' ' . $st->year ?></span>
                                            <br>
                                            <br>
                                            <span> <?= $this->utility->just_date($st->created_on, FALSE) ?></span>
                                            <span class="float-right font-weight-bolder">+<?= number_format($st->amount, 2) ?></span>
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
                    <?= form_open('member/savings', 'class="needs-validation" novalidate') ?>
                    <div class="row">
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