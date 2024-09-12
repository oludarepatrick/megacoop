<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="#" data-toggle="modal" data-target="#savings-type-modal" class="btn btn-primary mb-2"><i class="mdi mdi-plus mr-2"></i><?= lang('add_role') ?></a>
                    </div>
                </div>

                <table  class="table table-striped table-bordered dt-responsive nowrap w-100" id="basic-datatable">
                    <thead>
                        <tr>
                            <th><?= lang('created_on') ?></th>
                            <th><?= lang('name') ?></th>
                            <th><?= lang('description') ?></th>
                            <th style="width: 70px"><?= lang('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $st) { ?>
                            <tr>
                                <td><?= $this->utility->just_date($st->created_on) ?></td>
                                <td><?= $st->name ?></td>
                                <td><?= ucfirst($st->description) ?></td>
                                <td>
                                    <a data-fancy href="<?= base_url('users/privilege/' . $this->utility->mask($st->id))?>" data-toggle="tooltip" title="<?= lang('privilege') ?>"  class="btn btn-info btn-sm"><i class="mdi mdi-lock-open-outline"></i> </a>
                                    <a href="" onclick="edit_role(<?= $st->id ?>)" data-toggle="modal" data-target="#edit-savings-type-modal" data-toggle="tooltip" title="<?= lang('edit') ?>"  class="btn btn-primary btn-sm"><i class="mdi mdi-square-edit-outline"></i> </a>
                                    <a data-fancy href="<?= base_url('users/delete_role/' . $this->utility->mask($st->id)) ?>" onclick="return confirm('Are you sure you want to delete this?')" data-toggle="tooltip" title="<?= lang('delete_role') ?>"  class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i> </a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
<div id="savings-type-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('add_role') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('users/add_role', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12">
                        <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                        <input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" required>
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('description') ?>"><?= lang('description') ?></label>
                        <textarea class="form-control" name="description" required=""></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="edit-savings-type-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('edit_role') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <?= form_open('users/edit_role', 'class="needs-validation" novalidate') ?>
                <div class="row">
                    <div class=" col-md-12">
                        <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                        <input type="text" name="name" id="name" class="form-control"  required>
                        <input type="hidden" name="id" id="id" class="form-control" >
                    </div>
                    <div class=" col-md-12 mt-2">
                        <label for="<?= lang('description') ?>"><?= lang('description') ?></label>
                        <textarea class="form-control" id="description" name="description" required=""></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.mo