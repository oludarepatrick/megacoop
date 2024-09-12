<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card bg-gen">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a data-fancy href="<?= base_url('agency/loan/add') ?>" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle mr-2"></i><?= lang('add_loan') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <table class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-member">
                    <thead>
                        <tr>
                            <th>
                                <span><i class="mdi mdi-calendar-range"> </i> <?= lang('loans') ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $l) { ?>
                            <tr>
                                <td>
                                    <a data-fancy href="<?= base_url('agency/loan/preview/' . $this->utility->mask($l->id)) ?>" class="text-secondary">
                                        <div>
                                            <span class="font-weight-bolder"> <?= ucfirst($l->full_name) ?></span>
                                            <span class="badge badge-primary float-right"><?= $l->status?></span>
                                            <br>
                                            <br>
                                            <span> <?= $this->utility->just_date($l->created_on, FALSE) ?></span>
                                            <span class="float-right font-weight-bolder"><?= number_format($l->principal,2); ?></span>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

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
                    <?= form_open('agency/loan', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="loan_type"><?= lang('loan_type') ?></label>
                            <?php
                            $scc[""] = lang('select') . ' ' . lang('loan_type');
                            foreach ($loan_types as $sc) {
                                $scc[$sc->id] = $sc->name;
                            }
                            ?>
                            <?= form_dropdown('loan_type', $scc, set_value('loan_type'), 'class="form-control select2"  data-toggle="select2"'); ?>
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('start_date') ?>"><?= lang('start_date') ?></label>
                            <input type="text" name="start_date" class="form-control" value="<?= set_value('start_date') ?>" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
                        </div>
                        <div class=" col-md-12 mt-2">
                            <label for="<?= lang('end_date') ?>"><?= lang('end_date') ?></label>
                            <input type="text" name="end_date" value="<?= set_value('end_date') ?>" class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-d" data-date-autoclose="true">
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