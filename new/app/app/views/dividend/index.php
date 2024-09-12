<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4><?= lang('generate') . ' ' . lang('dividend') ?></h4>
            </div>

            <div class="card-body">
                <?= form_open('dividend', 'class="needs-validation" novalidate') ?>
                <div class="card p-2 bg-light">
                    <h4>Cooperative Profits</h4>
                    <p>Input the respective calculated profit to be shared. E.g if the declared savings profit
                        is <strong>1,000,000</strong> and the cooparative is to share 50% of the profit, then the savings profit
                        to be input will be <strong>500,000</strong>
                    </p>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="<?= lang('savings_profit') ?>"><?= lang('savings_profit') ?></label>
                            <input type="text" name="savings_profit" id="savings_profit" class="form-control" required value="<?= set_value('savings_profit') ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="<?= lang('loan_profit') ?>"><?= lang('loan_profit') ?></label>
                            <input type="text" name="loan_profit" id="loan_profit" class="form-control" required value="<?= set_value('loan_profit') ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="<?= lang('credit_sales_profit') ?>"><?= lang('credit_sales_profit') ?></label>
                            <input type="text" name="credit_sales_profit" id="credit_sales_profit" class="form-control" required value="<?= set_value('credit_sales_profit') ?>" data-toggle="input-mask" data-mask-format="#,##0.00" data-reverse="true">
                        </div>
                    </div>
                </div>
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                <button type="submit" class="btn btn-primary float-right"><i class="mdi mdi-floppy mr-1"></i><?= lang('generate') ?></button>
                <?= form_close() ?>
            </div>

        </div>
    </div>
</div>