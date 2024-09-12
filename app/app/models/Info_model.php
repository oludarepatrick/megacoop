<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Info_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_privilege_by_role_id($role_id) {
        $this->db->select('menu.name as name, menu.id as id, role_id, xread, xwrite, xdelete')
                ->from('menu')
                ->join('privilege', 'menu.id = privilege.menu_id')
                ->where('role_id', $role_id);
        $q = $this->db->get();
        return $q->result();
    }

    public function get_users_login($where = null, $limit = null) {
        $this->db->select("users.*, groups.name as g_name, role.name as role")
                ->join('users_groups', 'users_groups.user_id = users.id')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->join('role', 'role.id = users.role_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get('users')->result();
    }
    public function get_loan_types($where=null) {
        $this->db->select("loan_types.*, loan_calc_method.name as calc_method")
                ->join('loan_calc_method', 'loan_types.calc_method = loan_calc_method.id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get('loan_types')->result();
    }
    public function get_product_types($where=null) {
        $this->db->select("product_types.*, loan_calc_method.name as calc_method")
                ->join('loan_calc_method', 'product_types.calc_method = loan_calc_method.id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get('product_types')->result();
    }

    public function search_user($coop_id = null, $limit = null, $search) {
        $q = "SELECT * FROM users WHERE (first_name LIKE '%{$search}%' ESCAPE '!' 
        OR last_name LIKE '%{$search}%' ESCAPE '!'
        OR username LIKE '%{$search}%' ESCAPE '!' 
        OR phone LIKE '%{$search}%' ESCAPE '!' ) AND coop_id=$coop_id LIMIT 5";
        return $this->db->query($q)->result();
    }

    public function get_users_activities($where = null, $limit = null) {
        $this->db->select("users.*, role.name as role, action, 
        activities.created_on as date, activities.id as activities_id, metadata")
                ->join('role', 'role.id = users.role_id')
                // ->group_by("users_groups.user_id")
                ->join('activities', 'activities.user_id = users.id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get('users')->result();
    }
    public function get_users_activities_details($where = null) {
        $this->db->select("users.*, groups.name as g_name, role.name as role, action, 
        activities.created_on as date, activities.id as activities_id, activities.metadata")
                ->join('users_groups', 'users_groups.user_id = users.id')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->join('role', 'role.id = users.role_id')
                ->join('activities', 'activities.user_id = users.id');
        if ($where) {
            $this->db->where($where);
        }

        return $this->db->get('users')->row();
    }

    public function get_users($where = null, $limit = null) {
        $this->db->select("users.*, groups.name as g_name, role.name as role")
                ->join('users_groups', 'users_groups.user_id = users.id')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->group_by("users.id")
                ->join('role', 'role.id = users.role_id');
        if ($where) {
            $this->db->where($where);
        }

        if($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get('users')->result();
    }
    
    public function get_exit_members($where = null) {
        $this->db->select("first_name, last_name, username, request_date, groups.name as g_name, role.name as role,
        member_exit.status, users.id, member_exit.id as member_exit_id, member_exit.exit_date ")
                ->join('users_groups', 'users_groups.user_id = users.id')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->join('member_exit', 'member_exit.user_id = users.id')
                ->join('role', 'role.id = users.role_id');
        if ($where) {
            $this->db->where($where);
        }

        return $this->db->get('users')->result();
    }

    public function get_user_details($where) {
        $this->db->select("users.*, groups.name as g_name,groups.id as group_id, role.name as role,")
                ->join('users_groups', 'users_groups.user_id = users.id')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->join('role', 'role.id = users.role_id')
                ->where($where);
        return $this->db->get('users')->row();
    }

    public function get_savings($where = null, $limit = null, $order = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, savings.*, months.name as month, savings_types.name as savings_type")
                ->from('users')
                ->join('savings', 'savings.user_id=users.id')
                ->where('savings.tranx_type', 'credit')
                ->join('savings_types', 'savings_types.id=savings_type')
                ->join('months', 'savings.month_id=months.id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($order) {
            $this->db->order_by('savings.id', 'DESC');
        }
        return $this->db->get()->result();
    }

    public function get_savings_statement($where = null, $limit = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, savings.*, months.name as month, savings_types.name as savings_type")
                ->from('users')
                ->join('savings', 'savings.user_id=users.id')
                ->join('savings_types', 'savings_types.id=savings_type')
                ->join('months', 'savings.month_id=months.id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }

    public function get_savings_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, savings.*, months.name as month, savings_types.name as savings_types")
                ->from('users')
                ->join('savings', 'savings.user_id=users.id')
                ->where('savings.tranx_type', 'credit')
                ->join('savings_types', 'savings_types.id=savings_type')
                ->join('months', 'savings.month_id=months.id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }

    public function get_withdrawals($where = null, $limit = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, savings.*, months.name as month, savings_types.name as savings_type")
                ->from('users')
                ->join('savings', 'savings.user_id=users.id')
                ->where('savings.tranx_type', 'debit')
                ->join('savings_types', 'savings_types.id=savings_type')
                ->join('months', 'savings.month_id=months.id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }

    public function get_withdrawal_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, savings.*, months.name as month, savings_types.name as savings_types")
                ->from('users')
                ->join('savings', 'savings.user_id=users.id')
                ->where('savings.tranx_type', 'debit')
                ->join('savings_types', 'savings_types.id=savings_type')
                ->join('months', 'savings.month_id=months.id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }

    public function get_loans($where = null, $limit = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, loans.*, phone, email, loan_types.name as loan_type")
                ->from('users')
                ->join('loans', 'loans.user_id=users.id')
                ->join('loan_types', 'loan_types.id=loans.loan_type_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }

    public function get_loan_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, users.avatar as avater, "
                        . "phone, users.email as email, loans.*, loan_types.name as loan_type")
                ->from('users')
                ->join('loans', 'loans.user_id=users.id')
                ->join('loan_types', 'loan_types.id=loans.loan_type_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }
    
    public function get_credit_sales($where = null, $limit = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, credit_sales.*, product_types.name as product_type")
                ->from('users')
                ->join('credit_sales', 'credit_sales.user_id=users.id')
                ->join('product_types', 'product_types.id=credit_sales.product_type_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }
    
    public function get_credit_sales_details($where = null, $limit = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, users.avatar as avater,"
                . "phone, users.email as email,credit_sales.*, product_types.name as product_type")
                ->from('users')
                ->join('credit_sales', 'credit_sales.user_id=users.id')
                ->join('product_types', 'product_types.id=credit_sales.product_type_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->row();
    }

    public function get_loan_repayment($where = null, $limit = null, $order = true) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, loan_repayment.*, loan_types.name as loan_type, "
                        . "savings_source.name as source_name")
                ->from('users')
                ->join('loan_repayment', 'loan_repayment.user_id=users.id')
                ->join('savings_source', 'savings_source.id=loan_repayment.source')
                ->join('loan_types', 'loan_types.id=loan_repayment.loan_type_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($order) {
            $this->db->order_by('loan_repayment.id', 'DESC');
        }
        return $this->db->get()->result();
    }
    
    public function get_loan_repayment_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, loan_repayment.*,months.name as month, loan_types.name as loan_type, "
                        . "savings_source.name as source_name")
                ->from('users')
                ->join('loan_repayment', 'loan_repayment.user_id=users.id')
                ->join('savings_source', 'savings_source.id=loan_repayment.source')
                ->join('loan_types', 'loan_types.id=loan_repayment.loan_type_id')
                ->join('months', 'months.id=loan_repayment.month_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }
    
    public function get_credit_sales_repayment($where = null, $limit = null, $order = true) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, credit_sales_repayment.*, product_types.name as product_type, "
                        . "savings_source.name as source_name")
                ->from('users')
                ->join('credit_sales_repayment', 'credit_sales_repayment.user_id=users.id')
                ->join('savings_source', 'savings_source.id=credit_sales_repayment.source')
                ->join('product_types', 'product_types.id=credit_sales_repayment.product_type_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($order) {
            $this->db->order_by('credit_sales_repayment.id', 'DESC');
        }
        return $this->db->get()->result();
    }

    public function get_credit_sales_repayment_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, credit_sales_repayment.*,months.name as month, product_types.name as product_type, "
                        . "savings_source.name as source_name")
                ->from('users')
                ->join('credit_sales_repayment', 'credit_sales_repayment.user_id=users.id')
                ->join('savings_source', 'savings_source.id=credit_sales_repayment.source')
                ->join('product_types', 'product_types.id=credit_sales_repayment.product_type_id')
                ->join('months', 'months.id=credit_sales_repayment.month_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }

    public function get_loan_guarantors($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, loan_guarantors.*")
                ->from('loans')
                ->join('loan_guarantors', 'loan_guarantors.loan_id=loans.id')
                ->join('users', 'users.id=loan_guarantors.guarantor_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }
    
    public function get_credit_sales_guarantors($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, credit_sales_guarantors.*")
                ->from('credit_sales')
                ->join('credit_sales_guarantors', 'credit_sales_guarantors.credit_sales_id=credit_sales.id')
                ->join('users', 'users.id=credit_sales_guarantors.guarantor_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_loan_guaranteed($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, loan_guarantors.*")
                ->from('loans')
                ->join('users', 'users.id=loans.user_id')
                ->join('loan_guarantors', 'loan_guarantors.loan_id=loans.id');

        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }
    public function get_credit_sales_guaranteed($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, credit_sales_guarantors.*")
                ->from('credit_sales')
                ->join('users', 'users.id=credit_sales.user_id')
                ->join('credit_sales_guarantors', 'credit_sales_guarantors.credit_sales_id=credit_sales.id');

        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_loan_guaranteed_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, loan_guarantors.*")
                ->from('loans')
                ->join('users', 'users.id=loans.user_id')
                ->join('loan_guarantors', 'loan_guarantors.loan_id=loans.id');

        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }

    public function get_loan_approvals($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, role.name as role, loan_approvals.*")
                ->from('loans')
                ->join('loan_approvals', 'loan_approvals.loan_id=loans.id')
                ->join('users', 'users.id=loan_approvals.exco_id')
                ->join('role', 'role.id=users.role_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_member_exit_approvals($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, role.name as role, member_exit_approvals.*")
                ->from('member_exit')
                ->join('member_exit_approvals', 'member_exit_approvals.member_exit_id=member_exit.id')
                ->join('users', 'users.id=member_exit_approvals.exco_id')
                ->join('role', 'role.id=users.role_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }
    public function get_withdrawal_approvals($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, role.name as role, withdrawal_approvals.*")
                ->from('savings')
                ->join('withdrawal_approvals', 'withdrawal_approvals.savings_id=savings.id')
                ->join('users', 'users.id=withdrawal_approvals.exco_id')
                ->join('role', 'role.id=users.role_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }
    
    public function get_credit_sales_approvals($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, avatar, role.name as role, credit_sales_approvals.*")
                ->from('credit_sales')
                ->join('credit_sales_approvals', 'credit_sales_approvals.credit_sales_id=credit_sales.id')
                ->join('users', 'users.id=credit_sales_approvals.exco_id')
                ->join('role', 'role.id=users.role_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }

    public function get_wallet($where = null, $limit = null, $order = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, wallet.*, 
        payment_gate_way.name as gate_way")
                ->from('users')
                ->join('wallet', 'wallet.user_id=users.id')
                ->join('payment_gate_way', 'payment_gate_way.id=wallet.gate_way_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($order) {
            $this->db->order_by('wallet.id', 'DESC');
        }
        return $this->db->get()->result();
    }
    public function get_agent_wallet($where = null, $limit = null, $order = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, agent_wallet.*, 
        payment_gate_way.name as gate_way")
                ->from('users')
                ->join('agent_wallet', 'agent_wallet.user_id=users.id')
                ->join('payment_gate_way', 'payment_gate_way.id=agent_wallet.gate_way_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($order) {
            $this->db->order_by('agent_wallet.id', 'DESC');
        }
        return $this->db->get()->result();
    }

    public function get_wallet_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, users.avatar as avater, wallet.*, payment_gate_way.name as gate_way")
                ->from('users')
                ->join('wallet', 'wallet.user_id=users.id')
                ->join('payment_gate_way', 'payment_gate_way.id=wallet.gate_way_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }
    public function get_agent_wallet_details($where = null) {
        $this->db->select("users.username, CONCAT(first_name, ' ', last_name) as full_name, users.avatar as avater, wallet.*, payment_gate_way.name as gate_way")
                ->from('users')
                ->join('wallet', 'wallet.user_id=users.id')
                ->join('payment_gate_way', 'payment_gate_way.id=wallet.gate_way_id');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->row();
    }

    public function get_loan_by_month_year($month, $year, $coop_id) {
        $query = "SELECT SUM(total_remain) as total_remain, SUM(interest) as interest, SUM(total_due) as total_due "
                . "FROM loans WHERE EXTRACT(MONTH FROM disbursed_date) = $month AND EXTRACT(YEAR FROM disbursed_date) = $year "
                . " AND status != 'request' AND coop_id = $coop_id";

        $q = $this->db->query($query);
        return $q->row();
    }
    
    public function get_credit_sales_by_month_year($month, $year, $coop_id) {
        $query = "SELECT SUM(total_remain) as total_remain, SUM(interest) as interest, SUM(total_due) as total_due "
                . "FROM credit_sales WHERE EXTRACT(MONTH FROM disbursed_date) = $month AND EXTRACT(YEAR FROM disbursed_date) = $year "
                . " AND status != 'request' AND coop_id = $coop_id";

        $q = $this->db->query($query);
        return $q->row();
    }

    public function get_journals($where = null) {
        $this->db->select('*')
                ->from('ledger');
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get()->result();
    }
    
    public function get_subscriptions($where = null, $limit = null){
        $this->db->select("subscription.*, subscription_category.name as subs_cat")
                ->join('subscription_category', 'subscription_category.id = subscription.subscription_cat_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get('subscription')->result();
    }
    
    public function get_licence($where = null, $limit = null){
        $this->db->select("licence.*, licence_cat.name as subs_cat")
                ->join('licence_cat', 'licence_cat.id = licence.licence_cat_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get('licence')->result();
    }
    
    public function get_licence_details($where = null, $limit = null){
        $this->db->select("licence.*, licence_cat.name as licence_cat, licence_cat.month as month")
                ->join('licence_cat', 'licence_cat.id = licence.licence_cat_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get('licence')->row();
    }
    
    public function get_investment($where = null, $limit = null) {
        $this->db->select("investment.*, investment_types.name")
                ->from('investment')
                ->join('investment_types', 'investment_types.id=investment.investment_type');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }
    
    public function get_investment_details($where = null, $limit = null) {
        $this->db->select("investment.*, investment_types.name")
                ->from('investment')
                ->join('investment_types', 'investment_types.id=investment.investment_type');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->row();
    }

    public function get_products($where = null, $limit = null) {
        $this->db->select("products.*, product_types.name as product_type, vendors.name as vendor")
                ->from('products')
                ->join('product_types', 'product_types.id=products.product_type_id')
                ->join('vendors', 'vendors.id=products.vendor_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }
    public function get_products_details($where = null, $limit = null) {
        $this->db->select("products.*, product_types.name as product_type, vendors.name as vendor")
                ->from('products')
                ->join('product_types', 'product_types.id=products.product_type_id')
                ->join('vendors', 'vendors.id=products.vendor_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->row();
    }

    public function get_orders($where = null, $limit = null) {
        $this->db->select("orders.*, products.name as product, products.initials, products.sold, products.description,
         vendors.name as vendor, product_types.name as product_type")
                ->from('orders')
                ->join('product_types', 'product_types.id=orders.product_type_id')
                ->join('products', 'products.id=orders.product_id')
                ->join('vendors', 'vendors.id=products.vendor_id');
        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }
    
    public function get_trained_users($where = null) {
        $this->db->select("users.first_name, users.last_name, groups.name as g_name, role.name as role, training.*")
                ->join('users_groups', 'users_groups.user_id = users.id')
                ->join('groups', 'groups.id = users_groups.group_id')
                ->join('role', 'role.id = users.role_id')
                ->group_by('users.id')
                ->join('training', 'training.user_id = users.id');
        if ($where) {
            $this->db->where($where);
        }

        return $this->db->get('users')->result();
    }

     public function get_product_report($coop_id) {
        
        $query = "SELECT SUM(price*(stock-sold)) as total_available_stock_price , SUM((stock-sold)) as total_available_stock
        FROM products WHERE status='available' AND coop_id=$coop_id";

        $q = $this->db->query($query);
        return $q->row();
    }

     public function get_user_coops($identity = null) {
        $this->db->select("users.id as uid, users.username, cooperatives.*")
                ->join('cooperatives', 'users.coop_id = cooperatives.id');
        if ($identity) {
            $this->db->where('username', $identity)
            ->or_where('phone', $identity)
            ->or_where('email', $identity);
        }

        return $this->db->get('users')->result();
    }

    public function get_sms_report($where = null){
        $this->db->select("username, first_name,last_name, payment, sms_log.date, user_id")
            ->join('sms_log', 'users.id = sms_log.user_id')
            ->select_sum('price')
            ->select_sum('unit')
            ->group_by("user_id");
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->get('users')->result();
    }

}
