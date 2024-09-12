<!-- Start Content-->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div>
                    <h4 class="header-title mt-4 ml-2 float-left"><?= lang('loan_request') ?></h4>
                    <a data-fancy href="<?= base_url('agency/loan/delete/' . $this->utility->mask($loan->id)) ?>" class="btn btn-danger float-right">
                    <i class="mdi mdi-trash-can mr-2"></i><?= lang('delete_request') ?></a>
                    <a data-fancy href="<?= base_url('agency/loan/edit/' . $this->utility->mask($loan->id)) ?>" class="btn btn-primary mr-1 float-right">
                    <i class="mdi mdi-pen mr-2"></i><?= lang('edit_loan') ?></a>
                </div>
                <div id="basicwizard">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-4">
                            <li class="nav-item">
                                <a href="#basictab1" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                    <i class="mdi mdi-account-circle mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('member_details') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-face-profile mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('guarantor') ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#basictab3" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('approval') ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content b-0 mb-0">
                        <div class="tab-pane active show" id="basictab1">
                            <div class="collapse show" id="todayTasks">
                                <div class="card-body">
                                    <span class="float-left m-2 mr-5">
                                        <img src="<?= $assets ?>images/users/<?= $loan->avater ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                                    </span>
                                    <div class="media-body">
                                        <h4 class="mt-1 mb-1"><?= ucwords($loan->full_name) ?></h4>
                                        <p class="font-13"> <?= $loan->username ?></p>

                                        <ul class="mb-0 list-inline">
                                            <li class="list-inline-item mr-3">
                                                <h5 class="mb-1"><?= number_format($saving_bal, 2) ?></h5>
                                                <p class="mb-0 font-13"><?= lang('savings_bal') ?></p>
                                            </li>
                                            <li class="list-inline-item">
                                                <h5 class="mb-1"><?= number_format($wallet_bal, 2) ?></h5>
                                                <p class="mb-0 font-13"><?= lang('wallet_bal') ?></p>
                                            </li>
                                        </ul>
                                    </div> <!-- end card-body-->
                                    <div class="table-responsive table-bordered mt-4">
                                        <table class=" table mb-0">
                                            <h5 class=" ml-2"><?= lang('schedule_for_requested_loan') ?></h5>
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><?= lang('item') ?></td>
                                                    <th><?= lang('value') ?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?= lang('principal') ?></td>
                                                    <td><?= number_format($loan->principal, 2) ?></td>
                                                </tr>

                                                <tr>
                                                    <td><?= lang('interest') ?></td>
                                                    <td><?= number_format($loan->interest, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?= lang('total_due') ?></td>
                                                    <td><?= number_format($loan->total_due, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?= lang('monthly_due') ?></td>
                                                    <td><?= number_format($loan->monthly_due, 2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?= lang('tenure') ?></td>
                                                    <td><?= $loan->tenure ?> <?= lang('month') ?>(s)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive table-bordered mt-4">
                                        <h5 class="ml-2 text-danger"><?= lang('existing_loan') ?></h5>
                                        <table class="table mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><?= lang('loan_type') ?></th>
                                                    <th><?= lang('total') ?></th>
                                                    <th><?= lang('paid') ?></th>
                                                    <th><?= lang('balance') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($existing_loans as $el) { ?>
                                                    <tr>
                                                        <td> <?= $el->loan_type ?></td>
                                                        <td> <?= number_format($el->total_due, 2) ?></td>
                                                        <td class="text-success"> <?= number_format($el->total_due - $el->total_remain, 2) ?></td>
                                                        <td class="text-danger"> <?= number_format($el->total_remain, 2) ?></td>
                                                    </tr>
                                                <?php } ?>
                                                <?php if (!$existing_loans) { ?>
                                                    <tr>
                                                        <td> NA</td>
                                                        <td> NA</td>
                                                        <td> NA</td>
                                                        <td> NA</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> <!-- end .mt-2-->
                        </div>

                        <div class="tab-pane mb-4" id="basictab2">
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

                                        <ul class="mb-0 list-inline">
                                            <li class="list-inline-item mr-3">
                                                <h5 class="mb-1"><?= number_format($g->savings_bal, 2) ?></h5>
                                                <p class="mb-0 font-13"><?= lang('savings_bal') ?></p>
                                            </li>
                                            <li class="list-inline-item">
                                                <h5 class="mb-1"><?= number_format($g->wallet_bal, 2) ?></h5>
                                                <p class="mb-0 font-13"><?= lang('wallet_bal') ?></p>
                                            </li>
                                        </ul>
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

                                    <div class="table-responsive table-bordered mt-4">
                                        <h5 class="ml-2 text-danger"><?= lang('existing_loan') ?></h5>
                                        <table class="table mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th><?= lang('total') ?></th>
                                                    <th><?= lang('paid') ?></th>
                                                    <th><?= lang('balance') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td> <?= number_format($g->total_due, 2) ?></td>
                                                    <td class="text-success"> <?= number_format($g->total_due - $g->total_remain, 2) ?></td>
                                                    <td class="text-danger"> <?= number_format($g->total_remain, 2) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="tab-pane" id="basictab3">
                            <?php if (!$loan_approval) { ?>
                                <div class="card-body bg-light border text-center ">
                                    <p class="lead"> <?= lang('no_exco_approval') ?></p>
                                </div>
                            <?php } ?>
                            <?php foreach ($loan_approval as $k => $g) { ?>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>