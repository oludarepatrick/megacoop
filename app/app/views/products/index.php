<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="" data-toggle="modal" data-target="#savings-type-modal" class="btn btn-primary mb-2"><i class="mdi mdi-plus mr-1"></i><?= lang('add') . ' ' . lang('product') ?></a>
                        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#right-modal"><i class="mdi mdi-filter-menu mr-2"></i> <?= lang('filter') ?></button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered w-100 dt-responsive tab nowrap" id="basic-datatable">
                        <thead class="thead-light">
                            <tr>
                                <th><?= lang('product') ?></th>
                                <th><?= lang('category') ?></th>
                                <th><?= lang('vendor') ?></th>
                                <th><?= lang('price') ?></th>
                                <th><?= lang('stock') ?></th>
                                <th><?= lang('sold') ?></th>
                                <th><?= lang('image') ?>(s)</th>
                                <th><?= lang('status') ?>(s)</th>
                                <th style="width: 85px;"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p) { ?>
                                <tr>
                                    <td>
                                        <div class="avatar-sm float-left mr-1">
                                            <span class="avatar-title bg-primary-lighten rounded text-primary">
                                                <?= $p->initials ?>
                                            </span>
                                        </div>
                                        <p class="m-0 d-inline-block align-middle font-16">
                                            <a href="apps-ecommerce-products-details.html" class="text-body"><?= ucfirst($p->name) ?></a>
                                            <br />
                                            <span class="text-warning mdi mdi-star"></span>
                                            <span class="text-warning mdi mdi-star"></span>
                                            <span class="text-warning mdi mdi-star"></span>
                                            <span class="text-warning mdi mdi-star"></span>
                                            <span class="text-warning mdi mdi-star"></span>
                                        </p>
                                    </td>
                                    <td> <?= ucfirst($p->product_type) ?> </td>
                                    <td> <?= ucfirst($p->vendor) ?> </td>
                                    <td><?= number_format($p->price, 2) ?> </td>
                                    <td><?= $p->stock ?> </td>
                                    <td><?= $p->sold ?> </td>
                                    <td> <?= $p->images ?> </td>
                                    <td>
                                        <?php if ($p->status == 'pending') { ?>
                                            <span class="badge badge-warning-lighten text-uppercase"><?= $p->status ?></span>
                                        <?php } ?>
                                        <?php if ($p->status == 'available') { ?>
                                            <span class="badge badge-primary-lighten text-uppercase"><?= $p->status ?></span>
                                        <?php } ?>
                                    </td>
                                    <td class="table-action">
                                        <a data-fancy href="<?= base_url('products/details/' . $this->utility->mask($p->id)) ?>" class="action-icon"> <i class="mdi mdi-eye"></i></a>
                                        <a onclick="edit_product('<?= $p->id ?>')" data-toggle="modal" data-target=".edit-product-modal" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div id="savings-type-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('add') . ' ' . lang('product') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?= form_open('products/add_product', 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                    <input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" required>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?= lang('price') ?></label>
                        <input class=" form-control" name="price" required data-toggle="input-mask" data-mask-format="#,##0" value="<?= set_value('price') ?>" type="text" data-reverse="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?= lang('stock') ?></label>
                        <input name="stock" required data-toggle="touchspin" type="text" data-bts-max="999999999999">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="product_type"><?= lang('product_type') ?></label><br>
                        <?= form_dropdown('product_type_id', $product_type, set_value('product_type_id'), '"class="form-control select2" data-toggle="select2"'); ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="vendor"><?= lang('vendor') ?></label><br>
                        <?= form_dropdown('vendor_id', $vendor, set_value('vendor_id'), '"class="form-control select2" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label><?= lang('description') ?></label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<div class="modal fade edit-product-modal" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel"><?= lang('edit') . ' ' . lang('product') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
                <div id="spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <?= form_open('products/edit_product', 'class="needs-validation" novalidate') ?>
                <div class="form-group">
                    <label for="<?= lang('name') ?>"><?= lang('name') ?></label>
                    <input type="text" name="name" class="form-control" value="<?= set_value('name') ?>" id="name" required>
                    <input type="hidden" , name="id" id="id">
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?= lang('price') ?></label>
                        <input class=" form-control" name="price" id="price" required data-toggle="input-mask" data-mask-format="#,##0" value="<?= set_value('price') ?>" type="text" data-reverse="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?= lang('stock') ?></label>
                        <input name="stock" required id="stock" data-toggle="touchspin" type="text" data-bts-max="999999999999">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="product_type"><?= lang('product_type') ?></label><br>
                        <?= form_dropdown('product_type_id', $product_type, set_value('product_type_id'), '"class="form-control select2" id="product_type_id" data-toggle="select2"'); ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="vendor"><?= lang('vendor') ?></label><br>
                        <?= form_dropdown('vendor_id', $vendor, set_value('vendor_id'), '"class="form-control id="vendor_id" select2" data-toggle="select2"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label><?= lang('description') ?></label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-floppy mr-1"></i><?= lang('save') ?></button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<div id="right-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-right">
        <div class="modal-content ">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="border border-light rounded bg-primary p-2 mb-3 text-white">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="m-0 mb-1"><?= lang('filter') ?></h4>
                        </div>
                    </div>
                    <p><?= lang('filter_note') ?></q>
                    </p>
                </div>
                <div class="">
                    <?= form_open('products', 'class="needs-validation" novalidate') ?>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="product_type"><?= lang('product_type') ?></label>
                            <?= form_dropdown('product_type_id', $product_type, set_value('product_type_id'), 'class="form-control select2"  data-toggle="select2"'); ?>
                        </div>

                        <div class="col mt-3">
                            <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant mr-1"></i><?= lang('apply_filter') ?></button>
                        </div>
                    </div>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>