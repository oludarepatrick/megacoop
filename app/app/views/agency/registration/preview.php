<!-- Start Content-->
<div class="container-fluid">

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="card">
                <div class="row">
                    <div class="col-4">
                        <h4 class="header-title mt-4 ml-2"><?= lang('member_exit') ?></h4>
                    </div>
                </div>
                <div id="basicwizard">
                
                    <div class="card-body">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-2">
                            <li class="nav-item">
                                <a href="#basictab1" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                    <i class="mdi mdi-briefcase mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('aggregate') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-check mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('approval') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active show" id="basictab1">
                            <div class="card-body">
                                <span class="float-left m-2 mr-5">
                                    <img src="<?= $assets ?>images/users/<?= $user->avatar ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                                </span>
                                <div class="media-body">
                                    <h4 class="mt-1 mb-1"><?= ucwords($user->first_name . ' ' . $user->last_name) ?></h4>
                                    <p class="font-13"> <?= $user->username ?></p>

                                    <ul class="mb-0 list-inline">
                                        <li class="list-inline-item mr-3">
                                            <h5 class="mb-1"><?= date('Y-M-d g:i:s a', $user->last_login) ?></h5>
                                            <p class="mb-0 font-13"><?= lang('last_login') ?></p>
                                        </li>
                                        <li class="list-inline-item mr-3">
                                            <h5 class="mb-1"><?= date('Y-M-d', $user->created_on) ?></h5>
                                            <p class="mb-0 font-13"><?= lang('member_since') ?></p>
                                        </li>
                                        <li class="list-inline-item ">
                                            <h5 class="mb-1"><?= $user->status ?></h5>
                                            <p class="mb-0 font-13"><?= lang('status') ?></p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-primary"><?= lang('loans') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('loan_type') ?></th>
                                                <th><?= lang('balance') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($active_loans as $el) { ?>
                                                <tr>
                                                    <td> <?= $el->loan_type ?></td>
                                                    <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($all_loan_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-primary"><?= lang('credit_sales') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('product_type') ?></th>
                                                <th><?= lang('balance') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($active_credit_sales as $el) { ?>
                                                <tr>
                                                    <td> <?= $el->product_type ?></td>
                                                    <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($all_credit_sales_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered mt-4">
                                    <h5 class="ml-2 text-primary"><?= lang('savings') ?></h5>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('savings_type') ?></th>
                                                <th><?= lang('balance') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($savings as $sav) { ?>
                                                <tr>
                                                    <td> <?= $sav->name ?></td>
                                                    <td> <?= number_format($sav->bal, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($all_savings_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-responsive table-bordered">
                                    <h5 class="ml-2 text-primary mb-0"><?= lang('liquidity') ?></h5>
                                    <small class="ml-2"><?= lang('payable_amount') ?></small>
                                    <table class="table mb-0 dt-responsive nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><?= lang('total') ?> <?= lang('liquidity') ?></th>
                                                <th><?= number_format($all_savings_bal - $all_loan_bal - $all_credit_sales_bal, 2) ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <a data-fancy href="<?= base_url('memberexit/member_exit/' . $this->utility->mask($user->id)) ?>" onclick=" return confirm('Are you sure you want to exit this member ?')" class="btn btn-danger float-right mt-4">
                                    <i class="mdi mdi-walk mr-1"></i> <?= lang('exit') ?>
                                </a>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="tab-pane" id="basictab2">
                            <div class="card-body">
                                <?php if (!$approval) { ?>
                                    <div class="card-body bg-light border text-center ">
                                        <p class="lead"> <?= lang('no_exco_approval') ?></p>
                                    </div>
                                <?php } ?>
                                <?php foreach ($approval as $k => $g) { ?>
                                    <div class="card-body">
                                        <h4 class="text-primary"><?= lang('exco') ?> <?= $k + 1 ?> <?= lang('approval') ?></h4>
                                        <hr>
                                        <span class="float-left m-2 mr-5">
                                            <img src="<?= $assets ?>images/users/<?= $g->avatar ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                                        </span>
                                        <div class="media-body">
                                            <h4 class="mt-1 mb-1"><?= ucwords($g->full_name) ?></h4>
                                            <p class="font-13"> <?= $g->role ?></p>

                                            <ul class="mb-0 list-inline">
                                                <li class="list-inline-item mr-4">
                                                    <h5 class="mb-1"><?= lang('status') ?></h5>
                                                    <p class="mb-0"><?= $g->approval ?></p>
                                                </li>
                                                <li class="list-inline-item mr-4">
                                                    <h5 class="mb-1"><?= lang('approval_date') ?></h5>
                                                    <p class="mb-0"> <?= $this->utility->just_date($g->response_date) ?></p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="text-center">
                                    <a data-fancy href="<?= base_url('memberexit/approve/' . $this->utility->mask($member_exit_id)) ?>" class="btn btn-primary"><i class=" mdi mdi-check-bold mr-1"></i> <?= lang('approve') ?> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>