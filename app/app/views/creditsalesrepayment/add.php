<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <!--            <div class="card-header">
                <h4><?= lang('add_credit_sales_repayment') ?></h4>
            </div>-->
            <div class="card-body">
                <?= form_open('creditsalesrepayment/add/' . $this->utility->mask($credit_sales->id), 'class="needs-validation" novalidate') ?>
                <div>
                    <div class="card bg-primary text-white">
                        <div class="card-body ">
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
                        </div>
                    </div>
                    <div class="card rounded mt-2 bg-primary text-white">
                        <h5 class="ml-2"><?= $credit_sales->product_type ?></h5>
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
                                        <td><?= number_format($credit_sales->total_due, 2) ?></td>
                                        <td><?= number_format($credit_sales->total_due - $credit_sales->total_remain, 2) ?></td>
                                        <td><?= number_format($credit_sales->total_remain, 2) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                        <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount', ($credit_sales->monthly_due)) ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="source"><?= lang('source') ?></label>
                        <?php
                        $scc[""] = lang('select') . ' ' . lang('source');
                        foreach ($savings_source as $sc) {
                            $scc[$sc->id] = $sc->name;
                        }
                        ?>
                        <?= form_dropdown('source', $scc, set_value('source'), 'class="form-control select2" name="source"  id="source" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="<?= lang('month') ?>"><?= lang('month') ?></label>
                        <input type="text" name="month" class="form-control" data-provide="datepicker" data-date-format="MM" data-date-min-view-mode="1">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="<?= lang('year') ?>"><?= lang('year') ?></label>
                        <input type="text" name="year" class="form-control" data-provide="datepicker" data-date-format="yyyy" data-date-min-view-mode="2">
                    </div>
                </div>
                <div class="form-group">
                    <label for="<?= lang('narration') ?>"><?= lang('narration') ?></label>
                    <textarea class="form-control" name="narration" value="<?= set_value('narration') ?>" id="description" name="description" required=""></textarea>
                </div>
                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
                <?= form_close() ?>
            </div>
            <!-- end row -->

        </div>
    </div>
</div>