<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends BASE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $products = $this->info->get_products(['products.coop_id'=>$this->coop->id, 'products.status'=>'available']);
        foreach ($products as $od) {
            $od->avatar = $this->common->get_this('product_image', ['product_id' => $od->id]);
        }
        $this->data['products'] = $products;
        $this->data['title'] = lang('products');
        $this->data['controller'] = lang('products');
        $this->layout->set_app_page_member('member/products/index', $this->data);
    }

    public function details($id){
        $id = $this->utility->un_mask($id);
        $this->data['product'] = $this->info->get_products_details(['products.coop_id' => $this->coop->id, 'products.id' => $id]);
        $this->data['images'] = $this->common->get_all_these('product_image', ['product_id' => $id]);
        $this->data['title'] = lang('details');
        $this->data['controller'] = lang('products');
        $this->layout->set_app_page_member('member/products/details', $this->data);
    }
}
