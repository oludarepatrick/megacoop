<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row  justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered dt-responsive nowrap w-100" id="basic-datatable-member">
                    <thead>
                        <tr>
                            <th>
                                <span></span><?= lang('meeting') ?> <?= lang('minutes') ?></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($minutes as $st) { ?>
                            <tr>
                                <td>
                                    <a data-fancy href="<?= base_url('member/miscellaneous/view_minute/' . $this->utility->mask($st->id)) ?>" class="text-secondary">
                                        <div>
                                            <span > <?= $this->utility->just_date($st->created_on) ?></span>
                                            <br>
                                            <br>
                                            <span class="font-weight-bolder"><?= ucwords($st->title) ?></span>
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