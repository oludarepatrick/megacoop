<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 p-0">
            <div class="card">
                <div class="row">
                    <div class="col">
                        <h4 class="header-title mt-4 ml-2"><?= lang('my') ?> <?= lang('loan') ?></h4>
                    </div>
                    <div class="col">
                        <?php if ($loan->status == 'disbursed') { ?>
                            <a data-fancy href="<?= base_url('member/loan/repayment_history/' . $this->utility->mask($loan->id)) ?>" class="btn btn-outline-primary float-right m-3" data-toggle="tooltip" title="<?= lang('repayment_history') ?>">
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
                                <a href="#basictab11" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 ">
                                    <i class="mdi mdi-cash-plus mr-1"></i>
                                    <span class="d-none d-sm-inline"><?= lang('schedule') ?></span>
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
                                <?php if ($loan->status == 'disbursed') { ?>
                                    <div class="card-body">
                                        <div class="card-body cta-box bg-primary rounded text-white mb-2">
                                            <h5 class="ml-2"><?= $loan->loan_type ?></h5>
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <tbody class=" text-white">
                                                        <tr>
                                                            <th><?= lang('total') ?></th>
                                                            <td><?= number_format($loan->total_due, 2) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th><?= lang('paid') ?></th>
                                                            <td><?= number_format($loan->total_due - $loan->total_remain, 2) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th><?= lang('balance') ?></th>
                                                            <td><?= number_format($loan->total_remain, 2) ?></td>
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
                                        <?= form_open('member/loan/repay/' . $this->utility->mask($loan->id), 'class="needs-validation" novalidate') ?>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                                                <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount', $loan->monthly_due) ?>" data-toggle="input-mask" data-mask-format="000.000.000.000,000,000.00" data-reverse="true">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary float-right mb-2"><i class="mdi mdi-floppy mr-1"></i><?= lang('repay_loan') ?></button>
                                        <?= form_close() ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="tab-pane mb-4" id="basictab11">
                            <div class="card-body">
                                <div class="table-responsive table-bordered mt-1">
                                    <table class="table table-striped dt-responsive nowrap w-100" id="basic-datatable-account">
                                        <h5 class=" ml-2"><?= lang('repayment') . ' ' . lang('schedule') ?></h5>
                                        <thead class="thead-dark">
                                            <tr>
                                                <th><?= lang('month') ?></td>
                                                <th><?= lang('monthly_due') ?></td>
                                                <th><?= lang('interest') ?></td>
                                                <th><?= lang('principal') ?></td>
                                                <th><?= lang('balance') ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($repayment_schedule as $repay) { ?>
                                                <tr>
                                                    <td><?= $repay->month ?></td>
                                                    <td><?= number_format($repay->monthly_due, 2) ?></td>
                                                    <td><?= number_format($repay->interest_due, 2) ?></td>
                                                    <td><?= number_format($repay->principal_due, 2) ?></td>
                                                    <td><?= number_format($repay->balance, 2) ?></td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <th>-</th>
                                                <th><?= lang('total') ?></th>
                                                <th><?= number_format($loan->interest, 2) ?></th>
                                                <th><?= number_format($loan->principal, 2) ?></th>
                                                <th>-</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane mb-4" id="basictab2">
                            <div class="card-body">
                                <div class="table-responsive table-bordered mt-1">
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
                                            <tr>
                                                <td><?= lang('status') ?></td>
                                                <td><?= $loan->status ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                                        <div class="m-2">
                                            <h5 class="float-left"><?= lang('guarantor_approval') ?></h5>
                                            <a href="!#" onclick="setGuarantor('<?= $g->loan_guarantor_id ?>')" class="btn btn-primary float-right" data-toggle="modal" data-target="#decline">
                                                <i class="mdi mdi-file-edit mr-1"></i><?= lang('change_guarantor') ?>
                                            </a>
                                            <div class="clearfix"></div>
                                        </div>
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
                                                <?php if ($g->status == 'declined') { ?>
                                                    <tr>
                                                        <td colspan="3">
                                                            <h5 class="ml-2"><?= lang('decline') ?> <?= lang('note') ?></h5>
                                                            <p class="ml-2"><?= $g->note ?></p>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
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
    <div class="modal fade" id="decline" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel"><?= lang('decline') ?> <?= lang('note') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?= form_open("member/loan/change_guarantor", 'class="needs-validation" novalidate') ?>
                    <div class="form-group">
                        <label for="<?= lang('note') ?>">Let <strong><?= ucwords($loan->full_name) ?></strong> why you could not guarantee his/her request </label>
                        <input type="text" name="member_id" class="form-control" required>
                        <input type="hidden" name="loan_guarantor_id" id="loan_guarantor_id">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block float-right"><?= lang('proceed') ?></button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>