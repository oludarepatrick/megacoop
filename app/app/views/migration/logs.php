<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('description') ?></th>
                            <th><?= lang('total') . ' ' . lang('record') ?></th>
                            <th style="width:100px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($logs as $m) { ?>
                            <tr>
                                <td><?= $m->migrated_date ?></td>
                                <td><?= ucwords($m->description) ?></td>
                                <td><?= ucwords($m->total_record) ?></td>
                                <td>
                                    <a onclick="return confirm('All record uploaded at this time will be deleted')" data-fancy href="<?= base_url('migration/rollback/' . $this->utility->mask($m->id)) ?>" data-toggle="tooltip" title="Rollback this migration" class="btn btn-danger btn-sm"><i class="mdi mdi-reload"></i> <?=lang('rollback')?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>