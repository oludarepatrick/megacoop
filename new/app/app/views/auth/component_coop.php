<div>
    <h4><?= count($users) ?> Account(s) found</h4>
    <p>Select an account to proceed</p>
    <?php foreach ($users as $u) { ?>
        <div onclick="select_coop_handle('<?= $u->username ?>', '<?=$action?>')" class="mb-3 shadow rounded bg-gen">
            <div class="media text-uppercase text-left btn pt-2 px-2 pb-0">
                <div class="d-flex mr-2 avatar-sm">
                    <span class="avatar-title bg-primary-lighten text-primary rounded">
                        <?= $this->utility->shortend_str_len($u->coop_name, 2, '') ?>
                    </span>
                </div>
                <div class="media-body">
                    <h5 class="mb-0 mt-1"><?= ucwords($u->coop_name) ?></h5>
                    <p class="mt-0"><?= ucwords($u->coop_code) ?></p>
                </div>
                <span class="badge badge-success-lighten">
                    <i class="mdi mdi-check-all"></i>
                </span>
            </div>
            <hr class="mt-0" />
            <div class="py-1 px-2">
                <p class="font-17"><?= ucwords(strtolower($u->coop_address)) ?></p>
            </div>
        </div>
    <?php } ?>
</div>