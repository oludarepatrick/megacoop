<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">

                <!-- Invoice Logo-->
                <div class="clearfix">
                    <div class="float-left mb-3">
                        <img src="<?= $assets ?>images/logo/coop/<?= $this->coop->logo ?>" alt="" height="28">
                    </div>
                    <div class="float-right">
                        <h4 class="m-0 d-print-none"><?= lang('payment_slip') ?></h4>
                    </div>
                </div>

                <!-- Invoice Detail-->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="float-left mt-3">
                            <p><b>Hello, <?= ucwords($savings->full_name) ?></b></p>
                            <p><b><?= lang('member_id')?>: <?= ucwords($savings->username) ?></b></p>
                            <p class="text-muted font-13">Please find below the breakdown of this savings and do not hesitate to contact us with any questions.</p>
                        </div>

                    </div><!-- end col -->
                    <div class="col-sm-4 offset-sm-2">
                        <div class="mt-3 float-sm-right">
                            <p class="font-13"><strong><?= lang('payment_date') ?>: </strong> &nbsp;&nbsp;&nbsp; <?= $this->utility->just_date($savings->payment_date) ?></p>
                            <p class="font-13"><strong><?= lang('payment_status') ?>: </strong> <span class="badge badge-success float-right"><?= $savings->status ?></span></p>
                            <p class="font-13"><strong><?= lang('savings_type') ?>: </strong> <span class="float-right"><?= $savings->savings_types ?></span></p>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row mt-4">
                    <div class="col-sm-4">
                        <h5><?= $this->coop->coop_name ?></h5>
                        <address>
                            <?= $this->coop->coop_address ?><br>
                            <?= $this->coop->contact_phone ?>
                        </address>
                    </div> <!-- end col-->
                </div>    
                <!-- end row -->        

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mt-4">
                                <tr>
                                    <td><?= lang('savings_type')?></td>
                                     <td><?= ucwords($savings->savings_types)?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('amount')?></td>
                                    <td><?= number_format($savings->amount, 2)?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('month')?></td>
                                    <td><?= $savings->month?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('year')?></td>
                                    <td><?= $savings->year?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('payment_date')?></td>
                                    <td><?= $savings->payment_date?></td>
                                </tr>
                                <tr>
                                    <td><?= lang('narration')?></td>
                                    <td><?= ucfirst($savings->narration)?></td>
                                </tr>
                            </table>
                        </div> 
                    </div>
                </div>
                

                <div class="d-print-none mt-4">
                    <div class="text-right">
                        <a href="javascript:window.print()" class="btn btn-primary"><i class="mdi mdi-printer"></i> <?= lang('print')?></a>
                        <a href="javascript: window.history.back();" class="btn btn-info"><?= lang('close')?></a>
                    </div>
                </div>   
                <!-- end buttons -->

            </div> 
        </div> 
    </div>
</div>