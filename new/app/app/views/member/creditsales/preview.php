<!-- Start Content-->
<div class="container-fluid">

    <div class="row">

        <div class="col-md-2"></div>
        <div class="col-md-8 p-0">
            <div class="card">

                <div class="row">
                    <div class="col">
                        <h4 class="header-title mt-4 ml-2"><?= lang('my') ?> <?= lang('credit_sales') ?></h4>
                    </div>
                    <div class="col">
                        <?php if ($credit_sales->status == 'disbursed') { ?>
                            <a data-fancy href="<?= base_url('member/creditsales/repayment_history/' . $this->utility->mask($credit_sales->id)) ?>" class="btn btn-outline-primary float-right m-3" data-toggle="tooltip" title="<?= lang('repayment_history') ?>">
                                <i class="uil uil-book-alt mr-1"> </i> <span class="d-none  d-sm-inline"> <?= lang('repayment_history') ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div id="basicwizard">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                            <li class="nav-item">
                                <a href="#basictab1" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                    <i class="mdi mdi-cash-plus mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('repayment') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-graph mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('details') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab3" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('guarantor') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active show" id="basictab1">
                            <div class="collapse show" id="todayTasks">
                                <?php if ($credit_sales->status == 'disbursed') { ?>
                                    <div class="card-body">
                                        <div class="card-body cta-box bg-primary rounded text-white mb-2">
                                            <h5 class="ml-2"><?= $credit_sales->product_type ?></h5>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <tbody class=" text-white">
                                                        <tr>
                                                            <th><?= lang('total') ?></th>
                                                            <td><?= number_format($credit_sales->total_due, 2) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th><?= lang('paid') ?></th>
                                                            <td><?= number_format($credit_sales->total_due - $credit_sales->total_remain, 2) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th><?= lang('balance') ?></th>
                                                            <td><?= number_format($credit_sales->total_remain, 2) ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body cta-box bg-primary rounded text-white">
                                                <div class="float-right">
                                                    <i class="mdi mdi-wallet widget-icon bg-light-lighten text-white"></i>
                                                </div>
                                                <h5 class="text-white font-weight-normal mt-0" title="<?= lang('wallet_bal') ?>"><?= lang('wallet_bal') ?></h5>
                                                <h3 class="mt-3 mb-3"><?= number_format($wallet_bal, 2) ?></h3>
                                                <p class="mb-0 text-white">
                                                    <a data-fancy href="<?= base_url('member/wallet/load_wallet') ?>" class="float-right mr-2 btn btn-primary btn-sm shadow"><i class="mdi mdi-dots-vertical mr-2"></i> Load <?= lang('wallet') ?></a>
                                                </p>
                                            </div>
                                        </div>
                                        <!-- end card-body-->
                                        <?= form_open('member/creditsales/repay/' . $this->utility->mask($credit_sales->id), 'class="needs-validation" novalidate') ?>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                                                <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount', $credit_sales->monthly_due) ?>" data-toggle="input-mask" data-mask-format="000.000.000.000,000,000.00" data-reverse="true">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary float-right mb-2"><i class="mdi mdi-floppy mr-1"></i><?= lang('repay_credit_sales') ?></button>
                                        <?= form_close() ?>
                                    </div>
                                <?php } ?>
                            </div> <!-- end .mt-2-->
                        </div>

                        <div class="tab-pane mb-4" id="basictab2">
                            <div class="card-body">
                                <div class="table-responsive table-bordered mt-1">
                                    <table class=" table mb-0">
                                        <!--<h5 class=" ml-2"><?= lang('schedule_for_requested_credit_sales') ?></h5>-->
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('item') ?></td>
                                                <th><?= lang('value') ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= lang('principal') ?></td>
                                                <td><?= number_format($credit_sales->principal, 2) ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= lang('interest') ?></td>
                                                <td><?= number_format($credit_sales->interest, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= lang('total_due') ?></td>
                                                <td><?= number_format($credit_sales->total_due, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= lang('monthly_due') ?></td>
                                                <td><?= number_format($credit_sales->monthly_due, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= lang('tenure') ?></td>
                                                <td><?= $credit_sales->tenure ?> <?= lang('month') ?>(s)</td>
                                            </tr>
                                            <tr>
                                                <td><?= lang('status') ?></td>
                                                <td><?= $credit_sales->status ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <h5 class="mb-1 mt-3"><?= lang('product') ?>(s)</h5>
                                <?php foreach ($orders as $od) { ?>
                                    <div class="shadow-sm p-2 mb-3 rounded">
                                        <div class="media">
                                            <div class="media-body">
                                                <h5 class="mt-0 mb-0"><?= ucfirst($od->product) ?></h5>
                                                <small>
                                                    <span class="mr-2">Qyt: <?= $od->quantity ?></span>
                                                    <span class="mr-2"><?= lang('price') ?>: <?= number_format($od->price, 2) ?></span>
                                                </small>
                                                <p class="mb-0 mt-1"><?= ucfirst($this->utility->shortend_str_len($od->description, 40)) ?></p>
                                            </div>

                                            <img class="d-flex ml-3 rounded" src="<?= $assets ?>images/products/<?= $od->image->avatar ?>" alt="Generic placeholder image" height="64">
                                        </div>
                                        <small>
                                            <span class="mr-2"> <i class="mdi mdi-menu mr-1"></i> <?= ucfirst($od->product_type) ?></span>
                                            <span class="mr-2"> <i class="mdi mdi-store mr-1"></i> <?= ucfirst($od->vendor) ?></span>
                                            <span class="mr-2 badge badge-primary-lighten"> <i class="mdi mdi-timelapse mr-1"></i> <?= $this->utility->just_date($od->created_on, false) ?></span>
                                        </small>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>

                        <div class="tab-pane" id="basictab3">
                            <?php if (!$guarantor) { ?>
                                <div class="card-body bg-light border text-center ">
                                    <p class="lead"> <?= lang('no_guarantor_required') ?></p>
                                </div>
                            <?php } ?>
                            <?php foreach ($guarantor as $k => $g) { ?>
                                <div class="card-body">
                                    <h4 class="text-primary"><?= lang('guarantor') ?> <?= $k + 1 ?> <?= lang('details') ?></h4>
                                    <hr>
                                    <span class="float-left m-2 mr-5">
                                        <img src="<?= $assets ?>images/users/<?= $g->avatar ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                                    </span>
                                    <div class="media-body">
                                        <h4 class="mt-1 mb-1"><?= ucwords($g->full_name) ?></h4>
                                        <p class="font-13"> <?= $g->member_id ?></p>
                                    </div>

                                    <div class="table-responsive table-bordered mt-4">
                                        <h5 class="ml-2"><?= lang('guarantor_approval') ?></h5>
                                        <table class="table mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><?= lang('request_date') ?></th>
                                                    <th><?= lang('response_date') ?></th>
                                                    <th><?= lang('status') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td> <?= $this->utility->just_date($g->request_date) ?></td>
                                                    <td> <?= $this->utility->just_date($g->response_date) ?></td>
                                                    <td> <?= $g->status ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>