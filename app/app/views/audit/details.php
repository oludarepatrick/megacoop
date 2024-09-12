<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <!-- <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-variant mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div> -->

                <div style="overflow-x:auto">
                    <table class="table w-100 table-bordered table-striped" id="datatable-buttons">
                        <thead>
                            <tr>
                                <th>Audit Logs | <?= $users[0]->first_name .' '. $users[0]-> last_name . ' | ' . $users[0]->username ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $st) { ?>
                                <tr>
                                    <td>
                                        <h5>Date: <?= $st->date ?></h5>
                                        <h5>Action: <?= $st->action ?></h5>
                                        <p>Previous Record: <?= $st->previous_data ?></p>
                                        <p>New Record: <?= $st->new_data ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>