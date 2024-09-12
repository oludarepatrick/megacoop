<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="float-left"><?= ucfirst($title) ?></h4>
                 <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#right-modal" ><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                 <button type="button" class="btn btn-primary float-right mr-2" data-toggle="modal" data-target="#statistic" ><i class="mdi mdi-chart-bar mr-2"></i> <?= lang('statistic') ?></button>
                 <div class=" clearfix"></div>
                 <p>From <?=$start_date?> to <?=$end_date?></p>
            </div>
            <div class="card-body">
                <table  class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-account">
                    <thead>
                            <tr>
                                <th> </th>
                                <th><?= lang('amount')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--asset sesction-->
                            <tr class="font-weight-bolder bg-primary-lighten text-primary">
                                <td><?= lang('income')?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                            $total_income = 0;
                            foreach ($income as $key => $sub_title){ ?>
                                <tr>
                                    <td class="font-18 font-weight-bolder"><?=strtoupper($key)?></td>
                                    <td> </td>
                                </tr>
                                <?php foreach ($sub_title as $ast){ 
                                    $total_income+=$ast->balance;
                                    ?>
                                    <tr>
                                        <td class="pl-4"><?=$ast->name?></td>
                                        <td><?= number_format($ast->balance,2)?> </td>
                                    </tr>
                                <?php } ?>
                                
                            <?php } ?>
                            <tr class=" font-18 font-weight-bolder bg-primary text-light">
                                <td><?= lang('total') . ' '. lang('income')?></td>
                                <td><?= number_format($total_income,2)?></td>
                            </tr>
                            
                            <!--asset sesction -->
                            <tr class=" font-18 font-weight-bolder bg-primary-lighten text-primary">
                                <td><?= lang('expenditure')?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                            $total_exp = 0;
                            foreach ($expenses as $key => $sub_title){ ?>
                                <tr>
                                    <td class=" font-weight-bolder"><?= strtoupper($key)?></td>
                                    <td> </td>
                                </tr>
                                <?php foreach ($sub_title as $ast){ 
                                    $total_exp+=$ast->balance;
                                    ?>
                                    <tr>
                                        <td class="pl-4"><?=$ast->name?></td>
                                        <td><?= number_format($ast->balance,2)?> </td>
                                    </tr>
                                <?php } ?>
                                
                            <?php } ?>
                            <tr class=" font-18 font-weight-bolder bg-primary text-light">
                                <td><?= lang('total') . ' '. lang('expenditure')?></td>
                                <td><?= number_format($total_exp,2)?></td>
                            </tr>
                            <tr class=" font-18 font-weight-bolder bg-dark text-light">
                                <td><?= lang('net') . ' '. lang('income').'/'.lang('loss')?></td>
                                <td><?= number_format($total_income - $total_exp,2)?></td>
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
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
                    <?= form_open('accounting/income_statement', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class=" col-md-12">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" data-provide="datepicker" data-date-format="yyyy-m-d">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                            <input type="text" name="end_date" class="form-control" data-provide="datepicker" data-date-format="yyyy-m-d">
                        </div>
                        <div class="col mt-3">
                            <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant mr-1"></i><?= lang('apply_filter') ?></button>
                        </div>
                    </div>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
    var total_income = <?=$total_income?> ; 
    var total_exp = <?=$total_exp?> ; 
</script>
<div class="modal fade" id="statistic" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel"><?= lang('statistic')?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4 class="header-title"><?= lang('income_statement')?></h4>
                <p>From <?=$start_date?> to <?=$end_date?></p>
                <div id="simple-pie-income-statement" class="apex-charts mb-3" data-colors="#0acf97,#fa5c7c"></div>
            </div>
        </div>
    </div>
</div>