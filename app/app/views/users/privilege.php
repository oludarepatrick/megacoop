
    
    <div class="row justify-content-center">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <div class=" table-responsive">
                    <h4><?= lang('users_privilege')?></h4>
                <?=  form_open('users/privilege/'.$this->utility->mask($role_id))?>
                <table  class="table table-striped table-bordered dt-responsive nowrap w-100" >
                    <thead>
                        <tr>
                            <th ><?= lang('name')?></th>
                            <th  style="width: 10px"><?= lang('read')?></th>
                            <th  style="width: 10px"><?= lang('write')?></th>
                            <th style="width: 10px"><?= lang('delete')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu as $m) { ?>
                            <tr>
                                <td><?= ucwords($m->name) ?></td>
                                <td>
                                    <input type="checkbox" name="xread[<?= $m->id ?>]" <?php if($m->xread=='off'){echo '';} else {echo 'checked';} ?> id="switchr<?= $m->id ?>" data-switch="bool"/>
                                    <label for="switchr<?= $m->id ?>" data-on-label="On" data-off-label="Off"></label>
                                </td>
                                <td>
                                    <input type="checkbox" name="xwrite[<?= $m->id ?>]" <?php if($m->xwrite=='off'){echo '';} else {echo 'checked';} ?> id="switchw<?= $m->id ?>" data-switch="bool"/>
                                    <label for="switchw<?= $m->id ?>" data-on-label="On" data-off-label="Off"></label>
                                </td>
                                <td>
                                    <input type="checkbox" name="xdelete[<?= $m->id ?>]" <?php if($m->xdelete=='off'){echo '';} else {echo 'checked';} ?> id="switchd<?= $m->id ?>" data-switch="bool"/>
                                    <label for="switchd<?= $m->id ?>" data-on-label="On" data-off-label="Off"></label>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
                    <button name="save" value="save" type="submit" class="btn btn-primary float-right"><?=  lang('save')?></button>
                </div>
                <?= form_close()?>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
