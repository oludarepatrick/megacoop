<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card widget-inline">
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0">
                            <div class="card-body text-center">
                                <i class="dripicons-suitcase text-success" style="font-size: 24px;"></i>
                                <h3><span class="text-success"><?= $this->country->currency ?> <?= number_format($total_due, 2) ?></span></h3>
                                <p class="text-success font-15 mb-0"><?= lang('total_due') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-plus text-primary" style="font-size: 24px;"></i>
                                <h3><span class="text-primary"><?= $this->country->currency ?> <?= number_format($total_due - $total_remain, 2) ?></span></h3>
                                <p class="text-primary font-15 mb-0"><?= lang('total') ?> <?= lang('paid') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="card shadow-none m-0 border-left text-danger">
                            <div class="card-body text-center">
                                <i class="mdi mdi-cash-multiple " style="font-size: 24px;"></i>
                                <h3><span class=""><?= $this->country->currency ?> <?= number_format($total_remain, 2) ?></span></h3>
                                <p class=" font-15 mb-0"><?= lang('total_remain') ?></p>
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
                <div class="row mb-2">
                    <div class="col-12">
                        <!-- <button type="button" class="btn btn-outline-primary float-right m-1" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant"></i></button> -->
                        <a data-fancy href="<?= base_url('member/products') ?>" class="btn btn-primary float-right m-1" data-toggle="tooltip" title="Request Credit Sales"><i class="mdi mdi-plus"></i> </a>
                    </div>
                </div>
                <table class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-member">
                    <thead>
                        <tr>
                            <th>
                                <span></span><?= lang('my') ?> <?= lang('credit_sales') ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($credit_sales as $st) { ?>
                            <tr>
                                <td>
                                    <a data-fancy href="<?= base_url('member/creditsales/preview/' . $this->utility->mask($st->id)) ?>" class="text-secondary">
                                        <div>
                                            <span class="font-weight-bolder"> <?= ucfirst($st->product_type) ?></span>
                                            <span class="badge badge-primary float-right"><?= $st->status ?></span>
                                            <br>
                                            <br>
                                            <span> <?= $this->utility->just_date($st->created_on, FALSE) ?></span>
                                            <span class="float-right font-weight-bolder"><?= number_format($st->total_remain, 2) ?></span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>