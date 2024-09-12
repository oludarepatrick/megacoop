<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5">
                        <?php if ($images) {
                            $active = 'active';
                        ?>
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner" role="listbox">
                                    <?php foreach ($images as $img) {  ?>
                                        <div class="carousel-item <?= $active ?>">
                                            <div class=" text-center ">
                                                <img class="img-fluid rounded" width="500" src="<?= $assets . 'images/products/' . $img->avatar ?>" alt="First slide">
                                            </div>
                                        </div>
                                    <?php
                                        $active = '';
                                    } ?>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        <?php }  ?>
                    </div>


                    <div class="col-lg-7">
                        <?= form_open('member/creditsales/order_proceed/'.$this->utility->mask($product->id), 'class="pl-lg-4"') ?>
                        <!-- Product title -->
                        <h3 class="mt-0"> <?= ucwords($product->name) ?> </h3>

                        <p class="mb-1"><?= lang('created_on') ?>: <?= $this->utility->just_date($product->created_on) ?></p>
                        <p class="font-16">
                            <span class="text-warning mdi mdi-star"></span>
                            <span class="text-warning mdi mdi-star"></span>
                            <span class="text-warning mdi mdi-star"></span>
                            <span class="text-warning mdi mdi-star"></span>
                            <span class="text-warning mdi mdi-star"></span>
                        </p>

                        <!-- Product stock -->
                        <div class="mt-3">
                            <h4><span class="badge badge-success-lighten"><?= $product->status ?></span></h4>
                        </div>

                        <!-- Product description -->
                        <div class="mt-4">
                            <h6 class="font-14"><?= lang('price') ?>:</h6>
                            <h3> <?= number_format($product->price, 2) ?></h3>
                        </div>
                        <div class="mt-4">
                            <h6 class="font-14"><?= lang('quantity') ?></h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group input-group-merge">
                                        <input class="form-control" min="1" max="2" value="1" name="quantity" type="number">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-sm btn-primary"> <i class=" mdi mdi-cart"></i> Proceed</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="font-14"><?= lang('description') ?>:</h6>
                            <p> <?= $product->description ?> </p>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bottom-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bottomModalLabel"><?= lang('change_logo') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open_multipart('products/upload/' . $this->utility->mask($product->id)) ?>
                <div class="form-group">
                    <label>Maximum upload size, 50kb</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" oninput="file_upload_fix('inputGroupFile03', 'file_label')" id="inputGroupFile03">
                            <label class="custom-file-label" for="inputGroupFile04" id="file_label"> </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                <button id="wait_upload_btn" style="display: none" class="btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                    <span class="mr-1 ml-1"><?= lang('please_wait') ?></span>
                </button>
                <button type="submit" id="upload_btn" onclick="wait_loader('upload_btn', 'wait_upload_btn')" class="btn btn-primary"><?= lang('upload') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>