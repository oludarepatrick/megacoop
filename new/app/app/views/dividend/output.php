<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a data-fancy href="<?= base_url('dividend') ?>" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('generate').' '. lang('dividend') ?></a>
                    </div>
                </div>

                <table class="table table-striped table-bordered dt-responsive nowrap w-100" id="datatable-buttons">
                    <thead>
                        <tr>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('savings_bal') ?></th>
                            <th><?= lang('savings').' ' . lang('dividend')  ?></th>
                            <th><?= lang('loan').' ' . lang('interest')  ?></th>
                            <th><?= lang('loan').' ' . lang('dividend')  ?></th>
                            <th><?= lang('credit_sales').' ' . lang('interest')  ?></th>
                            <th><?= lang('credit_sales').' ' . lang('dividend')  ?></th>
                            <th><?= lang('total').' ' . lang('dividend')  ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dividend as $st) { ?>
                            <tr>
                                <td><?= $st->member_id ?></td>
                                <td><?= ucfirst($st->first_name.' '.$st->last_name) ?></td>
                                <td><?= number_format($st->savings_bal, 2) ?></td>
                                <td><?= number_format($st->savings_dividend, 2) ?></td>
                                <td><?= number_format($st->loan_interest, 2) ?></td>
                                <td><?= number_format($st->loan_dividend, 2) ?></td>
                                <td><?= number_format($st->credit_sales_interest, 2) ?></td>
                                <td><?= number_format($st->credit_sales_dividend, 2) ?></td>
                                <td><?= number_format($st->total_dividend, 2) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
