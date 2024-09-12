<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="modal fade" id="loan_bal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="topModalLabel"><?= lang('all_loan_bal') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($loan_bal as $sb) { ?>
                        <div class="col-lg-6">
                            <div class="card widget-flat bg-gen">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="mdi mdi-cart-plus widget-icon bg-primary-lighten text-primary"></i>
                                    </div>
                                    <h5 class="text-muted font-weight-normal mt-0" title="<?= lang('loan') ?>"><?= $sb->name ?></h5>
                                    <h3 class="mt-3 mb-3"><?= number_format($sb->bal, 2) ?></h3>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <a data-fancy href="<?= base_url('loan/disbursed_loans') ?>" class="btn btn-primary"><?= lang('view') ?></a>
            </div>
        </div>
    </div>
</div>