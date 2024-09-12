<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table  class="table table-striped dt-responsive nowrap w-100" id="basic-datatable">
                        <thead>
                            <tr>
                                <th>
                                    <span><?= lang('all_request') ?></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guarantor as $st) { ?>
                                <tr>
                                    <td><a data-fancy href="<?= base_url('member/loan/preview_request')?>" class="text-secondary">
                                        <div class="media">
                                            <img class="mr-3 rounded-circle" src="<?= $assets ?>images/users/<?= $st->avatar ?>" width="40" alt="<?= $st->full_name ?>">
                                            <div class="media-body">
                                                <span class="badge badge-primary float-right d-none d-xs-none"><?= $st->status ?></span>
                                                <h5 class="mt-0 mb-2"><?= ucwords($st->full_name) ?></h5>
                                                <span class="font-13"><?= $st->username ?></span>
                                                <span class=" float-right d-none d-xs-none"><?= $this->utility->just_date($st->request_date) ?></span>
                                            </div>
                                        </div></a>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end row -->

        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end card-->