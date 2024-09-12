<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends BASE_Controller {
    const MENU_ID = 10; // same priviledge as categories

    public function __construct() {
        parent::__construct();
        if(!$this->ion_auth->is_admin()){
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('product_type_id', lang("product_type"), 'trim|required');

        $this->data['products'] = $this->info->get_products(['products.coop_id' => $this->coop->id], '1000');
        if ($this->form_validation->run()) {
            $where = [
                'products.coop_id' => $this->coop->id,
                'products.product_type_id' => $this->input->post('product_type_id'),
            ];
            $this->data['products'] = $this->info->get_products($where, false);
        }

        $product_type = $this->common->get_all_these('product_types', ['coop_id'=>$this->coop->id]);
        $vendor = $this->common->get_all_these('vendors', ['coop_id'=>$this->coop->id, 'deleted'=>0]);

        foreach($product_type as $p){
            $this->data['product_type'][$p->id] = $p->name;
        }
        foreach($vendor as $v){
            $this->data['vendor'][$v->id] = $v->name;
        }
        
        $this->data['title'] = lang('products');
        $this->data['controller'] = lang('products');
        $this->layout->set_app_page('products/index', $this->data);
    }

    public function add_product() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('price', lang('price'), 'trim|required');
        $this->form_validation->set_rules('stock', lang('stock'), 'trim|required|numeric');
        $this->form_validation->set_rules('product_type_id', lang('product_type'), 'trim|required|numeric');
        $this->form_validation->set_rules('vendor_id', lang('vendor'), 'trim|required|numeric');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('product').' '.lang('added')
            ];
            $product = $this->input->post();
            $product['coop_id'] = $this->coop->id;
            $product['price'] = str_replace(',', '', $this->input->post('price'));
            $product['initials'] = strtoupper($this->utility->shortend_str_len($this->input->post('name'), 2,''));
            $this->common->start_trans();
            $this->common->add('products', $product);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('product') . ' ' . lang('added'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function edit_product() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');
        $this->form_validation->set_rules('price', lang('price'), 'trim|required');
        $this->form_validation->set_rules('stock', lang('stock'), 'trim|required|numeric');
        $this->form_validation->set_rules('product_type_id', lang('product_type'), 'trim|required|numeric');
        $this->form_validation->set_rules('vendor_id', lang('vendor'), 'trim|required|numeric');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('product') . ' ' . lang('edited')
            ];
            $id = $this->input->post('id');
            $product = $this->input->post();
            $product['price'] = str_replace(',', '', $this->input->post('price'));
            $this->common->start_trans();
            $this->common->update_this('products', ['coop_id' => $this->coop->id, 'id' => $id], $product);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('product').' '.lang('edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function ajax_get_product() {
        $id = $this->input->get('id', true);
        $product = $this->common->get_this('products', ['id' => $id]);

        if (!$product) {
            echo json_encode(array('status' => 'error', 'message' => 'No saivings type found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $product));
        }
    }

    public function details($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->data['product'] = $this->info->get_products_details(['products.coop_id' => $this->coop->id, 'products.id'=>$id]);
        $this->data['images'] = $this->common->get_all_these('product_image',['product_id'=>$id]);
        $this->data['title'] = lang('details');
        $this->data['controller'] = lang('products');
        $this->layout->set_app_page('products/details', $this->data);
    }

    public function upload($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $product_details = $this->common->get_this('products', ['id'=>$id]);
        if($product_details->images > 2){
            $this->session->set_flashdata('error', 'Maximun number image upload reached');
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($_FILES['file']['error'] == 0) {
            $field_name = 'file';
            $file_name = $this->coop->coop_code.'-'.date("YmdHis");
            $upload_path = 'products/';
            $max_upload = 50;
            $is_uploaded = $this->utility->img_upload($field_name, $file_name, $max_upload, $upload_path);
            if (isset($is_uploaded['error'])) {
                $this->session->set_flashdata('error', $is_uploaded['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }
            $product['logo'] = $is_uploaded['upload_data']['file_name'];
        }

        $this->common->start_trans();
        $this->common->add('product_image', ['product_id'=>$id,'avatar'=> $product['logo'] ]);
        $this->common->update_this('products', ['id' => $id], ['images'=>$product_details->images + 1]);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function remove_image($id){
        $id = $this->utility->un_mask($id);
        $image = $this->common->get_this('product_image', ['id'=>$id]);
        $product_details = $this->common->get_this('products', ['id' => $image->product_id]);
        $path = 'assets/images/products/'.$image->avatar;
        
        if (file_exists($path)) {
            unlink($path);
        }
        if(($product_details->images - 1) < 0){
            $product_details->images = 0;
        }else{
            $product_details->images = $product_details->images- 1;
        }
        $this->common->start_trans();
        $this->common->delete_this('product_image', ['id' => $id, 'avatar']);
        $this->common->update_this('products', ['id' => $image->product_id], ['images' => $product_details->images]);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('act_successful'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
           
    }

    public function ajax_product_status_change(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $status = $this->input->get('status', true);
        $id = $this->input->get('id', true);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('product_status_changed')
        ];

        $this->common->start_trans();
        if ($status == 'false') {
            $activities['action'] = lang('product_status_changed');
            $this->common->update_this('products', ['id' => $id, 'coop_id' => $this->coop->id], ['status' => 'available']);
        }
        if ($status == 'true') {
            $activities['action'] = lang('product_status_changed');
            $this->common->update_this('products', ['id' => $id, 'coop_id' => $this->coop->id], ['status' => 'pending']);
        }
        $this->common->add('activities', $activities);
        $this->common->finish_trans();
    }

    public function vendors() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['vendors'] = $this->common->get_all_these('vendors', ['coop_id'=> $this->coop->id, 'deleted'=>0]);
        $this->data['title'] = lang('product_type');
        $this->data['controller'] = lang('categories');
        $this->layout->set_app_page('products/vendors', $this->data);
    }
    
    public function add_vendor() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('vendor').' '. lang('add')
            ];
            
            $post_data = $this->input->post();
            $post_data['coop_id'] = $this->coop->id;
            // $post_data['created_on'] = date('Y-m-d H:i:s');
            $this->common->start_trans();
            $this->common->add('vendors', $post_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();
            
            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('act_successful'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function edit_vendor() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('name', lang('name'), 'trim|required');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required');

        if ($this->form_validation->run()) {
            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('vendor').' ' .lang('edited')
            ];
            $post_data = $this->input->post();
            $id = $this->input->post('id');
            $this->common->start_trans();
            $this->common->update_this('vendors', ['id'=>$id, 'coop_id'=> $this->coop->id], $post_data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();
            
            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('vendor').' '.lang('edited'));
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
            }
        } else {
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    
    public function ajax_get_vendor() {
        $id = $this->input->get('id', true);
        $vendor = $this->common->get_this('vendors', ['id' => $id]);

        if (!$vendor) {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
        } else {
            echo json_encode(array('status' => 'success', 'message' => $vendor));
         }
    }

    public function delete_vendor($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('vendor').' '.lang('deleted')
        ];
        
        $this->common->start_trans();
        $this->common->update_this('vendors', ['coop_id' => $this->coop->id, 'id' => $id], ['deleted'=>1]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message', lang('vendor') . ' ' . lang('deleted'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function ordered_products(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');

        $this->data['products'] = $this->info->get_orders(['products.coop_id' => $this->coop->id], '1000');
        if ($this->form_validation->run()) {
            $where = [
                'products.coop_id' => $this->coop->id,
                'orders.created_on>=' =>$this->input->post('start_date'),
                'orders.created_on<=' =>$this->input->post('end_date'),
            ];
            $this->data['products'] = $this->info->get_orders($where, false);
        }

        $product_type = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);

        foreach ($product_type as $p) {
            $this->data['product_type'][$p->id] = $p->name;
        }

        $this->data['title'] = lang('ordered_products');
        $this->data['controller'] = lang('products');
        $this->layout->set_app_page('products/ordered_products', $this->data);
    }

    public function market_hub(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');

        $this->data['products'] = $this->common->get_all_these('orders',['coop_id' => $this->coop->id, 'product_id' => null]);
        if ($this->form_validation->run()) {
            $where = [
                'products.coop_id' => $this->coop->id,
                'orders.created_on>=' =>$this->input->post('start_date'),
                'orders.created_on<=' =>$this->input->post('end_date'),
            ];
            $this->data['products'] = $this->info->get_orders($where, false);
        }

        $product_type = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);

        foreach ($product_type as $p) {
            $this->data['product_type'][$p->id] = $p->name;
        }

        $this->data['title'] = lang('market_hub');
        $this->data['controller'] = lang('products');
        $this->layout->set_app_page('products/market_hub', $this->data);
    }
    
}
