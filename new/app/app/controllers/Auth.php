<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class Auth extends CI_Controller{

    public $data = [];

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->app_settings = $this->common->get_this('app_settings', ['id' => 1]);
        $this->lang->load('auth');
    }

    /**
     * Redirect if needed, otherwise display the user list
     */
    public function index() {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
            //        } else if (!$this->ion_auth->is_admin()) { // remove this elseif if you want to enable this for non-admins
            //            // redirect them to the home page because they must be an administrator to view this
            //            show_error('You must be an administrator to view this page.');
        } else {
            $this->data['title'] = $this->lang->line('login_heading');

            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['users'] = $this->ion_auth->users()->result();

            //USAGE NOTE - you can do more complicated queries like this
            //$this->data['users'] = $this->ion_auth->where('field', 'value')->users()->result();

            foreach ($this->data['users'] as $k => $user) {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }

            $this->layout->set_auth_page(DIRECTORY_SEPARATOR . 'auth/start', $this->data);
        }
    }


    private function resolve_identity($identity)
    {
        $identity = str_replace([';', '=', '*', '^', '%', '$'], '', $identity);
        $user = $this->common->get_this('users', ['email' => $identity]);
        if ($user) {
            return $user->username;
        }

        $user = $this->common->get_this('users', ['phone' => $identity]);
        if ($user) {
            return $user->email;
        }

        return $identity;
    }
 
    /**
     * Log the user in
     */

    public function login(){
        $this->data['title'] = $this->lang->line('login_heading');
        $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
        if ($this->form_validation->run() === TRUE) {
            // check to see if the user is logging in
            // check for "remember me"
            $remember = (bool) $this->input->post('remember');
            if ($this->ion_auth->login($this->resolve_identity($this->input->post('identity')), $this->input->post('password'), $remember)) {
                //if the login is successful
                //redirect them back to the home page
                $user = $this->common->get_this('users', ['id' => $this->session->userdata('user_id')]);
                if ($user->twofa == 'true') {
                    $coop = $this->common->get_this('cooperatives', ['id' => $user->coop_id]);
                    $token = $this->utility->get_2fa_token();
                    $this->common->update_this('users', ['id' => $user->id], ['twofa_token' => $token->hash, 'twofa_expires_on' => $token->expires_on]);
                    $this->data['token'] = $token->split_token;
                    $message = $this->load->view('emails/email_twofa', $this->data, true);
                    if ($this->utility->send_mail($this->app_settings->company_email, $coop->coop_name, $user->email, 'Login Token', $message)) {
                        redirect('auth/twofa');
                    }
                }

                $this->session->set_flashdata('message', $this->ion_auth->messages());
                if ($this->ion_auth->is_admin()) {
                    redirect('dashboard', 'refresh');
                } else {
                    redirect('member/dashboard', 'refresh');
                }
            } else {
                // if the login was un-successful
                // redirect them back to the login page
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect('auth/start', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        } else {
            // the user is not logging in so display the login page
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->layout->set_auth_page(DIRECTORY_SEPARATOR . 'auth/start', $this->data);
        }
    }

    public function twofa(){
        $this->form_validation->set_rules('token', $this->lang->line('token'), 'required');

        if ($this->form_validation->run() === TRUE) {
            $token = str_replace('-', '', $this->input->post('token'));
            // var_dump($token);exit;
            if ($this->utility->verify_twofa_token($this->session->userdata('user_id'), $token)) {
                redirect('member/dashboard');
            } else {
                $this->session->set_flashdata('error', 'Invalid Login Token');
            }
        }

        $this->data['user'] = $this->common->get_this('users', ['id' => $this->session->userdata('user_id')]);
        // $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = $this->lang->line('2fa');
        $this->layout->set_auth_page(DIRECTORY_SEPARATOR . 'auth/twofa', $this->data);
    }

    public function start(){
        $this->form_validation->set_rules('token', $this->lang->line('token'), 'required');
        $this->data['title'] = $this->lang->line('2fa');
        $this->layout->set_auth_page(DIRECTORY_SEPARATOR . 'auth/start', $this->data);
    }

    public function ajax_member_coops() {
        $identity = trim($this->input->get('identity', true));
        $action = trim($this->input->get('action', true));
        
        if($identity){
            $this->data['users'] = $this->info->get_user_coops($identity);
            $this->data['action'] = $action;
            $this->data['identity'] = $identity;
            if (count($this->data['users']) > 1) {
                $this->data['identity'] = $this->data['users'][0]->username;
                $mesage = $this->load->view('auth/component_coop', $this->data, true);
                echo json_encode(array('status' => 'success', 'message' => $mesage));
            }elseif(count($this->data['users']) == 1 and $action === 'login'){
                $this->data['identity'] = $this->data['users'][0]->username;
                $mesage = $this->load->view('auth/component_password', $this->data, true);
                echo json_encode(array('status' => 'success', 'message' => $mesage));
            }elseif($action === 'forget_pass'){
                $mesage = $this->load->view('auth/component_coop', $this->data, true);
                echo json_encode(array('status' => 'success', 'message' => $mesage));
            } else {
                echo json_encode(array('status' => 'error', 'message' => "Account not found!"));
            }
        }else{
            echo json_encode(array('status' => 'error', 'message' => 'Identity Field required!'));
        }
    }

    public function ajax_member_password() {
        $identity = trim($this->input->get('identity', true));
        if($identity){
            $this->data['identity'] = $identity;
            $mesage = $this->load->view('auth/component_password', $this->data, true);
            echo json_encode(array('status' => 'success', 'message' => $mesage));
        }else{
            echo json_encode(array('status' => 'error', 'message' => 'Invalid Cooperative Selected'));
        }
    }

    public function sal($public_token){
        $private_token = $this->session->userdata('private_token');
        $token_exist = $this->common->get_this('super_agent_login_token', ['public_token' => $public_token, 'private_token' => $private_token]);

        if ($token_exist) {
            $this->session->set_userdata('public_token', $public_token);
            redirect('dashboard');
        } else {
            redirect('auth/login', 'refresh');
        }
    }

    public function register(){
        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->data['identity_column'] = $identity_column;
        $this->form_validation->set_rules('h_pot', lang('h_pot'), 'trim');

        //send robort notification
        if ($this->input->post('h_pot')) {
            // $this->data['post'] = $this->input->post();
            // $message = $this->load->view('emails/email_robot_reg_alert', $this->data, true);
            // $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, 'shoguns2001@yahoo.com, razmybox@gmail.com', 'Anti-Robot Alert', $message);
            redirect('auth/login');
        }

        if ($identity_column !== 'email') {
            $this->form_validation->set_rules('identity', lang('create_user_validation_identity_label'), 'trim|required|valid_email');
        } else {
            $this->form_validation->set_rules('identity', lang('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
        }

        $this->form_validation->set_rules('coop_name', lang('coop_name'), 'trim|required');
        $this->form_validation->set_rules('password', lang('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']');
        $this->form_validation->set_rules('coop_type', lang('coop_type'), 'trim|required');
        $this->form_validation->set_rules('referrer_code', lang('coop_type'), 'trim');

        if ($this->form_validation->run() === TRUE) {
            $referer_code = $this->utility->referer_check($this->input->post('referrer_code'));
            if ($referer_code['error']) {
                $this->session->set_flashdata('error', $referer_code['error']);
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $email = strtolower($this->input->post('identity'));
            $password = $this->input->post('password');

            $coop_data = [
                'coop_name' => $this->input->post('coop_name'),
                'contact_email' => $this->input->post('identity'),
                'coop_type_id' => $this->input->post('coop_type'),
                'coop_code' => $this->utility->coop_code(),
                'referrer_code' => $referer_code['referer_code'] //default referrer_code
            ];
            $groups = [1];
        }

        if ($this->form_validation->run() === TRUE) {
            $this->common->start_trans();
            $coop_id = $this->common->add('cooperatives', $coop_data);
            $identity = $this->utility->generate_member_id($coop_data['coop_name'], $coop_id);
            $this->ion_auth->register($identity, $password, $email, ['coop_id' => $coop_id, 'tranx_pin' => hash('sha256', '123456')], $groups);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->data['name'] = ucwords($coop_data['coop_name']);
                $this->data['member_id'] = $identity;
                $subject = 'Cooprative Account Creation';
                $message = $this->load->view('emails/email_coop_reg', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $this->coop->coop_name, $email, $subject, $message);
                $this->session->set_flashdata('message', lang('reg_successful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

        } else {
            $this->data['coop_types'] = $this->common->get_all('coop_type');
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->layout->set_auth_page('auth/register', $this->data);
        }
    }

    public function register_member(){
        $coop_url = trim(strstr(uri_string(), '/', FALSE), '/');
        $coop = $this->common->get_this('cooperatives', ['url' => $coop_url]);
        if (!$coop) {
            show_error('Invalid Registration Url');
        }
        $this->data['coop'] = $coop;

        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required');
        $this->form_validation->set_rules('phone', lang('phone'), 'trim|required|numeric');
        $this->form_validation->set_rules('acc_name', lang('acc_name'), 'trim|required');
        $this->form_validation->set_rules('acc_no', lang('acc_no'), 'trim|required|numeric');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|is_unique[users.email]');
        $this->form_validation->set_rules('password', lang('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']');

        if ($this->form_validation->run() === TRUE) {

            if (!$this->utility->subscription_status($coop->id)) {
                $this->session->set_flashdata('error', lang('registration_limit_exided2'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            foreach ($this->input->post() as $key => $post) {
                if ($key == 'username' or $key == 'password' or $key == 'email') {
                    $auth_data[$key] = $post;
                } else {
                    $additional_data[$key] = $post;
                }
            }

            $groups = [2];
            $member_role = $this->common->get_this('role', ['coop_id' => $coop->id, 'group_id' => 2]);
             if($coop->approve_reg_member == 'false') {
                $additional_data['status'] = 'pending';
                $additional_data['active'] = 0;
             }
            $additional_data['role_id'] = $member_role->id;
            $auth_data['username'] = $this->utility->generate_member_id($coop->coop_name, $coop->id);
            $additional_data['coop_id'] = $coop->id;
            $additional_data['tranx_pin'] = hash('sha256', '123456');
            $added = $this->ion_auth->register($auth_data['username'], $auth_data['password'], $auth_data['email'], $additional_data, $groups);
            if ($added) {
                $this->data['name'] = ucwords($additional_data['first_name'] . ' ' . $additional_data['last_name']);
                $this->data['member_id'] = $auth_data['username'];
                $subject = 'Member Account Creation';
                $message = $this->load->view('emails/email_mem_self_reg', $this->data, true);
                $this->utility->send_mail($this->app_settings->company_email, $coop->coop_name, $auth_data['email'], $subject, $message);
                $this->session->set_flashdata('message', lang('action_successful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {

            $this->data['banks'] = $this->common->get_all_these('banks', ['country_id' => $coop->country_id]);
            $err = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->session->set_flashdata('error', $err);
            $this->layout->set_auth_page('auth/register_member', $this->data);
        }
    }

    /**
     * Log the user out
     */
    public function logout(){
        $this->data['title'] = "Logout";
        // log the user out
        $private_token = $this->session->userdata('private_token');
        if ($private_token) {
            $this->common->delete_this('super_agent_login_token', ['private_token' => $private_token]);
        }
        $this->ion_auth->logout();

        // redirect them to the login page
        redirect('auth/login', 'refresh');
    }

    /**
     * Change password
     */

    /**
     * Forgot password
     */
    public function forgot_password(){
        $this->data['title'] = $this->lang->line('forgot_password_heading');

        // setting validation rules by checking whether identity is username or email
        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        } else {
            $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        }

        if ($this->form_validation->run() === FALSE) {
           
            $this->data['type'] = $this->config->item('identity', 'ion_auth');

            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            // set any errors and display the form
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            redirect("auth", 'refresh');
        } else {
            $identity_column = $this->config->item('identity', 'ion_auth');
            $identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();
            if (empty($identity)) {

                if ($this->config->item('identity', 'ion_auth') != 'email') {
                    $this->ion_auth->set_error('forgot_password_identity_not_found');
                } else {
                    $this->ion_auth->set_error('forgot_password_email_not_found');
                }

                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth", 'refresh');
            }

            // run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

            if ($forgotten) {
                // if there were no errors
                $subject = 'Password Reset';
                $this->data['name'] = $identity->first_name . ' ' . $identity->last_name;
                $this->data['url'] = 'auth/reset_password/' . $forgotten['forgotten_password_code'];
                $message = $this->load->view('emails/email_forget_password', $this->data, true);
                $email = $this->input->post('identity');
                $this->utility->send_mail($this->app_settings->company_email, $this->app_settings->app_name, $email, $subject, $message);
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                redirect("auth", 'refresh');
            }
        }
    }

    public function init_forgot_password(){
        $this->data['title'] = $this->lang->line('reset_pass');
        $this->layout->set_auth_page(DIRECTORY_SEPARATOR . 'auth/init_forgot_password', $this->data);
    }

    public function ajax_send_password_rest_lik() {
        $identity = trim($this->input->get('identity', true));
        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity = $this->ion_auth->where($identity_column, $identity)->users()->row();
        if (empty($identity)) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid Cooperative Selected'));  
        }else{
            // run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
            if ($forgotten) {
                // if there were no errors
                $subject = 'Password Reset';
                $this->data['name'] = $identity->first_name . ' ' . $identity->last_name;
                $this->data['url'] = 'auth/reset_password/' . $forgotten['forgotten_password_code'];
                $message = $this->load->view('emails/email_forget_password', $this->data, true);
                $email = $identity->email;
                $this->utility->send_mail($this->app_settings->company_email, $this->app_settings->app_name, $email, $subject, $message);
                $this->data['identity'] = $identity;
                echo json_encode(array('status' => 'success', 'message' =>"<p> Password Reset link sent to your email </p>"));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Invalid Cooperative Selected'));
            }
        }
    }

    /**
     * Reset password - final step for forgotten password
     *
     * @param string|null $code The reset code
     */
    public function reset_password($code = NULL){
        if (!$code) {
            show_404();
        }

        $this->data['title'] = $this->lang->line('reset_password_heading');

        $user = $this->ion_auth->forgotten_password_check($code);
        
        if ($user) {
            // if the code is valid then display the password reset form

            $this->form_validation->set_rules('new_pass', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() === FALSE) {
                // display the form
                // set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['code'] = $code;

                $this->layout->set_auth_page('auth' . DIRECTORY_SEPARATOR . 'forget_password', $this->data);
            } else {
                $identity = $user->{$this->config->item('identity', 'ion_auth')};
                
                // finally change the password
                $change = $this->ion_auth->reset_password($identity, $this->input->post('new_pass'));

                if ($change) {
                    // if the password was successfully changed
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect("auth/login", 'refresh');
                } else {
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                    redirect('auth/reset_password/' . $code, 'refresh');
                }
            }
        } else {
            // if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    /**
     * Activate the user
     *
     * @param int         $id   The user ID
     * @param string|bool $code The activation code
     */
    public function activate($id, $code = FALSE){
        $activation = FALSE;

        if ($code !== FALSE) {
            $activation = $this->ion_auth->activate($id, $code);
        } else if ($this->ion_auth->is_admin()) {
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            // redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        } else {
            // redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }


    /**
     * Create a new user
     */
    public function create_user(){
        $this->data['title'] = $this->lang->line('create_user_heading');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->data['identity_column'] = $identity_column;

        // validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
        if ($identity_column !== 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
        } else {
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
        }
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if ($this->form_validation->run() === TRUE) {
            $email = strtolower($this->input->post('email'));
            $identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
            $password = $this->input->post('password');

            $additional_data = [
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
            ];
        }
        if ($this->form_validation->run() === TRUE && $this->ion_auth->register($identity, $password, $email, $additional_data)) {
            // check to see if we are creating the user
            // redirect them back to the admin page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        } else {
            // display the create user form
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['first_name'] = [
                'name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('first_name'),
            ];
            $this->data['last_name'] = [
                'name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('last_name'),
            ];
            $this->data['identity'] = [
                'name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            ];
            $this->data['email'] = [
                'name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'value' => $this->form_validation->set_value('email'),
            ];
            $this->data['company'] = [
                'name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'value' => $this->form_validation->set_value('company'),
            ];
            $this->data['phone'] = [
                'name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone'),
            ];
            $this->data['password'] = [
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password'),
            ];
            $this->data['password_confirm'] = [
                'name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            ];

            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'create_user', $this->data);
        }
    }

    /**
     * Redirect a user checking if is admin
     */
    public function redirectUser(){
        if ($this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }
        redirect('/', 'refresh');
    }

    /**
     * Create a new group
     */
    public function create_group(){
        $this->data['title'] = $this->lang->line('create_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        // validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');

        if ($this->form_validation->run() === TRUE) {
            $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
            if ($new_group_id) {
                // check to see if we are creating the group
                // redirect them back to the admin page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("auth", 'refresh');
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
            }
        }

        // display the create group form
        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        $this->data['group_name'] = [
            'name' => 'group_name',
            'id' => 'group_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_name'),
        ];
        $this->data['description'] = [
            'name' => 'description',
            'id' => 'description',
            'type' => 'text',
            'value' => $this->form_validation->set_value('description'),
        ];

        $this->_render_page('auth/create_group', $this->data);
    }

    /**
     * Edit a group
     *
     * @param int|string $id
     */
    public function edit_group($id){
        // bail if no group id given
        if (!$id || empty($id)) {
            redirect('auth', 'refresh');
        }

        $this->data['title'] = $this->lang->line('edit_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        $group = $this->ion_auth->group($id)->row();

        // validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'trim|required|alpha_dash');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === TRUE) {
                $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], array(
                    'description' => $_POST['group_description']
                ));

                if ($group_update) {
                    $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
                    redirect("auth", 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }
            }
        }

        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        // pass the user to the view
        $this->data['group'] = $group;

        $this->data['group_name'] = [
            'name' => 'group_name',
            'id' => 'group_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_name', $group->name),
        ];
        if ($this->config->item('admin_group', 'ion_auth') === $group->name) {
            $this->data['group_name']['readonly'] = 'readonly';
        }

        $this->data['group_description'] = [
            'name' => 'group_description',
            'id' => 'group_description',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_description', $group->description),
        ];

        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'edit_group', $this->data);
    }

    /**
     * @return array A CSRF key-value pair
     */
    public function _get_csrf_nonce(){
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return [$key => $value];
    }

    /**
     * @return bool Whether the posted CSRF token matches
     */
    public function _valid_csrf_nonce(){
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param string     $view
     * @param array|null $data
     * @param bool       $returnhtml
     *
     * @return mixed
     */
    public function _render_page($view, $data = NULL, $returnhtml = FALSE){ //I think this makes more sense
        $viewdata = (empty($data)) ? $this->data : $data;

        $view_html = $this->load->view($view, $viewdata, $returnhtml);

        // This will return html on 3rd argument being true
        if ($returnhtml) {
            return $view_html;
        }
    }
}
