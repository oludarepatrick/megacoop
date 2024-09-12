<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-lg-5">
                        <?php if ($images) { ?>
                            <a href="javascript: void(0);" class="text-center d-block mb-4">
                                <img src="<?= $assets ?>images/products/<?= $images[0]->avatar ?>" class="img-fluid" style="max-width: 350px;" alt="Product-img" />
                            </a>
                        <?php } else { ?>
                            <a href="javascript: void(0);" class="text-center d-block mb-4">
                                <div style="width: 300px; height:300px;"></div>
                            </a>

                        <?php } ?>

                        <div class="d-lg-flex d-none justify-content-center">
                            <?php if ($images) {
                                foreach ($images as $img) { ?>
                                    <a href="javascript: void(0);" class="ml-2">
                                        <img src="<?= $assets . 'images/products/' . $img->avatar ?>" class="img-fluid img-thumbnail p-2" style="max-width: 75px;" alt="Product-img" />
                                    </a>
                                    <a onclick="return confirm('Are you sure you want to remove')" data-fancy href="<?=base_url('products/remove_image/'.$this->utility->mask($img->id))?>" class="text-danger"> <i class="mdi mdi-trash-can font-18"></i></a>
                            <?php
                                }
                            }
                            ?>

                            <a href="" data-toggle="modal" data-target=".bottom-modal" class="text-center d-block mb-4">
                                <div class=" ml-2 avatar-md">
                                    <span class="avatar-title bg-primary-lighten text-primary rounded">
                                        <span class="font-24">+</span><br>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="col-lg-7">
                        <form class="pl-lg-4">
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
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 class="font-14"><?= lang('vendor') ?>:</h6>
                                        <p class="text-sm lh-150"><?= ucwords($product->vendor) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="font-14"><?= lang('category') ?>:</h6>
                                        <p class="text-sm lh-150"><?= ucwords($product->product_type) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="font-14"><?= lang('available') ?>:</h6>
                                        <?php if ($product->status == 'available') { ?>
                                            <input type="checkbox" id="switch1" onclick="product_status_change('true', <?= $product->id ?>)" checked data-switch="bool" />
                                        <?php } else { ?>
                                            <input type="checkbox" id="switch1" onclick="product_status_change('false', <?= $product->id ?>)" data-switch="bool" />
                                        <?php } ?>
                                        <label for="switch1" data-on-label="Yes" data-off-label="No"></label>
                                    </div>
                                </div>
                            </div>

                            <!-- Product description -->
                            <div class="mt-4">
                                <h6 class="font-14"><?= lang('description') ?>:</h6>
                                <p> <?= $product->description ?> </p>
                            </div>

                            <!-- Product information -->
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 class="font-14"><?= lang('total') . ' ' . lang('stock') ?>:</h6>
                                        <p class="text-sm lh-150"><?= $product->stock ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="font-14"><?= lang('total') . ' ' . lang('sold') ?>:</h6>
                                        <p class="text-sm lh-150"><?= $product->sold ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="font-14"><?= lang('total') . ' ' . lang('remain') ?>:</h6>
                                        <p class="text-sm lh-150"><?= $product->stock - $product->sold ?></p>
                                    </div>
                                </div>
                            </div>

                        </form>
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