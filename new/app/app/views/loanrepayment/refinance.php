<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('refinance') . ' ' . lang('loan') ?></h4>
            </div>
            <div class="card-body">
                <?= form_open('loanrepayment/refinance/' . $this->utility->mask($loan->id), 'class="needs-validation" novalidate') ?>
                <div>
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <span class="float-left m-2 mr-4">
                                <img src="<?= $assets ?>/images/users/<?= $member->avatar ?>" style="height: 100px;" alt="" class=" img-thumbnail">
                            </span>
                            <div class="media-body">

                                <h4 class="mt-1 mb-1"><?= ucwords($member->first_name . ' ' . $member->last_name) ?></h4>
                                <p class="font-13"> <?= ucfirst($member->role) ?></p>
                                <ul class="mb-0 list-inline">
                                    <li class="list-inline-item mr-3">
                                        <h5 class="mb-1"><?= $member->username ?></h5>
                                        <p class="mb-0 font-13"><?= lang('member_id') ?></p>
                                    </li>
                                    <li class="list-inline-item mr-3">
                                        <h5 class="mb-1"><?= number_format($wallet_bal, 2) ?></h5>
                                        <p class="mb-0 font-13"><?= lang('wallet_bal') ?></p>
                                    </li>
                                </ul>
                            </div>
                            <!-- end media-body-->
                        </div>
                        <!-- end card-body-->
                    </div>
                    <div class="card rounded mt-2 bg-primary text-white">
                        <h5 class="ml-2"><?= $loan->loan_type ?></h5>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><?= lang('total') ?></th>
                                        <th><?= lang('paid') ?></th>
                                        <th><?= lang('balance') ?></th>
                                    </tr>
                                </thead>
                                <tbody class=" text-white">
                                    <tr>
                                        <td><?= number_format($loan->total_due, 2) ?></td>
                                        <td><?= number_format($loan->total_due - $loan->total_remain, 2) ?></td>
                                        <td><?= number_format($loan->total_remain, 2) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="<?= lang('principal') ?>"><?= lang('principal') ?></label>
                        <input type="text" name="principal" id="principal" class="form-control" required readonly value="<?= set_value('amount', $loan->principal) ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                        <input type="hidden" name="refinance_data" id="refinance_data">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="<?= lang('rate') ?>">New <?= lang('rate') ?></label>
                        <input type="number" placeholder="Eg. 10 means 10%" name="rate" value="<?= set_value('rate') ?>" class=" form-control" id="rate" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="<?= lang('tenure') ?>"><?= lang('tenure') ?> (In months)</label>
                        <input type="number" name="tenure" class="form-control" id="tenure" value="<?= set_value('tenure') ?>" required>
                    </div>
                    <div class=" form-group col-md-12">
                        <div class="alert alert-primary" role="alert">
                            <h5 class="mb-2">Kindly select appropriately if the loan is within accounting year or not</h5>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input onclick="refinance(<?= $loan->loan_type_id ?>, <?= $loan->id ?>, this )" name="acc_year" type="radio" class="custom-control-input" id="within-acc-year" required>
                                <label class="custom-control-label" for="within-acc-year">Within an accounting year</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input onclick="refinance(<?= $loan->loan_type_id ?>, <?= $loan->id ?>, this)" name="acc_year" type="radio" class="custom-control-input" id="not-within-acc-year" required>
                                <label class="custom-control-label" for="not-within-acc-year">Not within accounting year</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('refinance') ?> <?= lang('amount') ?></label>
                        <input type="text" name="amount" id="amount"  class="form-control" required data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="source"><?= lang('source') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('source');
                        foreach ($savings_source as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('source', $scc, set_select('source'), 'class="form-control select2" name="source"  id="source" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('narration') ?>"><?= lang('narration') ?></label>
                    <textarea class="form-control" name="narration" value="<?= set_value('narration') ?>" id="description" name="description" required=""></textarea>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>