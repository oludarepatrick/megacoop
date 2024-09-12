<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
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
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Member Since</th>
                            <th>Surname</th>
                            <th>Othernames</th>
                            <th><?= lang('email') ?></th>
                            <th><?= lang('phone') ?></th>
                            <th><?= lang('reg_fee') ?></th>
                            <th>Error Message</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($failed_upload as $m) { ?>
                            <tr>
                                <td><?= $m->mem_since ?></td>
                                <td><?= ucwords($m->surname) ?></td>
                                <td><?= ucwords($m->othernames) ?></td>
                                <td><?= $m->email ?></td>
                                <td>|<?= $m->phone ?>|</td>
                                <td><?= $m->reg_fee ?></td>
                                <td><span class="btn btn-warning"><?= $m->message ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>