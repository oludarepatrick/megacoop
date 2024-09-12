    <div class="list-group position-fixed" style="z-index:5">
        <?php if ($member) { ?>
            <?php foreach ($member as $d) { ?>
                <button onclick="set_search_value(this)" value="<?= $d->username ?>" type="button" class="list-group-item-action list-group-item list-group-item-primary">
                    <?= $d->first_name . ' ' . $d->last_name . '- ' . $d->username ?>
                </button>
            <?php } ?>
        <?php } else { ?>
            <a href="#" class="list-group-item list-group-item-action list-group-item-danger">No Record Found!</a>
        <?php } ?>
    </div>