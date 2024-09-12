<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="col-12">
                            <p>Total Record: <?= count($failed_upload) + $record_num ?></p>
                            <p>Successful: <?= $record_num ?></p>
                            <p>Failed: <?= count($failed_upload) ?></p>
                            <p>The table below displays the failed record</p>
                            <p>Some of your records contain inappropriate data.
                                Kindly download and the csv, correct the inappropriate
                                and upload again.
                            </p>
                        </div>

                    </div>
                </div>
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>-</th>
                            <th><?= lang('member_id') ?></th>
                            <th><?= lang('full_name') ?></th>
                            <th><?= lang('principal') ?></th>
                            <th><?= lang('monthly_repayment') ?></th>
                            <th><?= lang('total_repayment') ?></th>
                            <th><?= lang('balance') ?></th>
                            <th><?= lang('disbursed_on') ?></th>
                            <th><?= lang('tenure') ?></th>
                            <th><?= lang('loan_type') ?></th>
                            <th>Error Message</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php for ($i = 1; $i < 8; $i++) { ?>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($failed_upload as $m) { ?>
                            <tr>
                                <td>-</td>
                                <td><?= $m->member_id ?></td>
                                <td><?= ucwords($m->full_name) ?></td>
                                <td><?= $m->principal ?></td>
                                <td><?= $m->monthly_repayment ?></td>
                                <td><?= $m->total_repayment ?></td>
                                <td><?= $m->balance ?></td>
                                <td><?= $m->disbursed_on ?></td>
                                <td><?= $m->tenure ?></td>
                                <td><?= $m->loan_type ?></td>
                                <td><span class="btn btn-warning"><?= $m->error ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>