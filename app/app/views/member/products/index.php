<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row justify-content-center mt-3">
    <div class="col-md-8">
        <h3>Available products</h3>
        <div class="row">
            <?php foreach ($products as $p) { ?>
                <div class="col-md-4">
                    <div class="card d-block">
                        <a data-fancy href="<?= base_url('member/products/details/' . $this->utility->mask($p->id)) ?>">
                            <img class="card-img-top" src="<?= $assets ?>images/products/<?= $p->avatar ?>" alt="product image">

                            <div class="card-body position-relative">
                                <!-- project title-->
                                <h5 class="mt-0 font-14">
                                    <a href="" class="text-title"><?= ucfirst($p->name) ?></a>
                                </h5>
                                <div>
                                    <strong class="float-left"> <?= number_format($p->price) ?></strong>
                                    <small class=" badge badge-primary-lighten float-right">
                                        <i class="mdi mdi-store mr-1"></i>
                                        available: <?= $p->stock - $p->sold ?>
                                    </small>
                                    <span class="clearfix"></span>
                                </div>
                            </div>
                        </a>

                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if (!$products) { ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <p> <i class=" mdi mdi-delete-empty-outline widget-icon"></i></p>
                    <h4>Product not available</h4>
                    <p>Please check back later or contact cooperative excos for more information</p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>