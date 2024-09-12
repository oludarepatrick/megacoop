<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 p-0">
            <div class="card">
                <div class="tab-content b-0 mb-0">
                    <div class="tab-pane active show" id="basictab1">
                        <div class="collapse show" id="todayTasks">
                            <div class="card-body">
                                <div class="text-center">
                                    <span class=" m-2">
                                        <img src="<?= $assets ?>images/users/<?= $user->avatar ?>" style="height: 100px;" alt="" class="rounded img-thumbnail">
                                    </span>
                                    <h4 class="mt-1 mb-1"><?= ucwords($user->first_name . ' ' . $user->last_name) ?></h4>
                                    <p class="font-13"> <?= $user->username ?></p>
                                </div>
                                <div class="card">
                                    <div class="card-body cta-box bg-primary rounded text-white">
                                        <div class="float-right">
                                            <i class="mdi mdi-wallet widget-icon bg-light-lighten text-white"></i>
                                        </div>
                                        <h5 class="text-white font-weight-normal mt-0" title="<?= lang('wallet_bal') ?>"><?= lang('wallet_bal') ?></h5>
                                        <h3 class="mt-3 mb-3"><?= number_format($wallet_bal, 2) ?></h3>
                                    </div>
                                </div>
                                <!-- end card-body-->
                                <?= form_open('agents/fund/' . $this->utility->mask($id), 'class="needs-validation" novalidate') ?>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="<?= lang('amount') ?>"><?= lang('amount') ?></label>
                                        <input type="text" name="amount" id="amount" class="form-control" required value="<?= set_value('amount') ?>" data-toggle="input-mask" data-mask-format="#,##0" data-reverse="true">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary float-right mb-2"><i class="mdi mdi-floppy mr-1"></i><?= lang('fund_agent') ?></button>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>