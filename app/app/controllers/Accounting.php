<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends BASE_Controller {

    const MENU_ID = 17;

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->is_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->library('excel');
        if ($this->coop) {
            if ($this->coop->status === 'processing') {
                redirect('settings');
            }
        }
        $this->licence_cheker($this->coop, $this->app_settings);
        
        $this->load->model('account_model', 'coa');
    }

    public function books() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['title'] = lang('books');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/books', $this->data);
    }

    public function index() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('pv_no', lang('pv_no'), 'trim|required');
        $this->form_validation->set_rules('reference', lang('reference'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('mop', lang('mop'), 'trim|required');
        $this->form_validation->set_rules('dr', lang('dr'), 'trim|required');
        $this->form_validation->set_rules('cr', lang('cr'), 'trim|required');
        $this->form_validation->set_rules('particular', lang('particular'), 'trim|required');
        $this->form_validation->set_rules('payment_date', lang('payment_date'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');

        if ($this->form_validation->run()) {
            $credit_id = $this->input->post('cr');
            $debit_id = $this->input->post('dr');
            $credit_name = $this->common->get_this('acc_value', ['coop_id' => $this->coop->id, 'id' => $credit_id]);
            $debit_name = $this->common->get_this('acc_value', ['coop_id' => $this->coop->id, 'id' => $debit_id]);
            $amount = str_replace(',', '', $this->input->post('amount'));

            $credit_total_credit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'credit_id' => $credit_id], 'amount')->amount;
            $credit_total_debit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'debit_id' => $credit_id], 'amount')->amount;
            $credit_tatal_bal = $credit_total_credit - $credit_total_debit;

            $debit_total_credit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'credit_id' => $debit_id], 'amount')->amount;
            $debit_total_debit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'debit_id' => $debit_id], 'amount')->amount;
            $debit_tatal_bal = $debit_total_credit - $debit_total_debit;
            $data = [
                'note' => $this->input->post('narration'),
                'created_by' => $this->user->id,
                'coop_id' => $this->coop->id,
                'credit_id' => $credit_id,
                'mop' => $this->input->post('mop'),
                'pv_no' => $this->input->post('pv_no'),
                'debit_id' => $debit_id,
                'amount' => $amount,
                'credit_bal' => $credit_tatal_bal + $amount,
                'debit_bal' => $debit_tatal_bal - $amount,
                'reference' => $this->input->post('reference'),
                'particular' => $this->input->post('particular'),
                'credit_name' => $credit_name->name,
                'debit_name' => $debit_name->name,
                'payment_date' => $this->input->post('payment_date'),
            ];

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('add') . ' ' . lang('ledger')
            ];

            $this->common->start_trans();
            $this->common->add('ledger', $data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('act_successful'));
                redirect('accounting/journal');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->data['acc_title'] = $this->coa->get_acc_titles();

            $acc = $this->coa->get_title_and_subtitle_and_value(['acc_value.coop_id' => $this->coop->id]);
            $acc_data = [];
            foreach ($acc as $ac) {
                $acc_data[$ac->title_code . ' - ' . $ac->title][$ac->sub_title_code . ' - ' . $ac->sub_title][] = (object) [
                            'id' => $ac->value_id,
                            'name' => $ac->value_name,
                            'code' => $ac->value_code
                ];
            }
            $this->data['mop'] = $this->common->get_all('mop');
            $this->data['pv_no'] = $this->utility->generate_pv_no($this->coop->coop_name);
            $this->data['acc_data'] = $acc_data;
            $this->data['title'] = lang('ledger_entry');
            $this->data['controller'] = lang('accounting');
            $this->layout->set_app_page('accounting/index', $this->data);
        }
    }

    public function edit_ledger_entry($id){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('pv_no', lang('pv_no'), 'trim|required');
        $this->form_validation->set_rules('reference', lang('reference'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
        $this->form_validation->set_rules('mop', lang('mop'), 'trim|required');
        $this->form_validation->set_rules('dr', lang('dr'), 'trim|required');
        $this->form_validation->set_rules('cr', lang('cr'), 'trim|required');
        $this->form_validation->set_rules('particular', lang('particular'), 'trim|required');
        $this->form_validation->set_rules('payment_date', lang('payment_date'), 'trim|required');
        $this->form_validation->set_rules('narration', lang('narration'), 'trim|required');
        $id = $this->utility->un_mask($id);
        $this->data['ledger'] =  $this->common->get_this('ledger', ['id' => $id, 'coop_id' => $this->coop->id]);
        if ($this->form_validation->run()) {
            $credit_id = $this->input->post('cr');
            $debit_id = $this->input->post('dr');
            $credit_name = $this->common->get_this('acc_value', ['coop_id' => $this->coop->id, 'id' => $credit_id]);
            $debit_name = $this->common->get_this('acc_value', ['coop_id' => $this->coop->id, 'id' => $debit_id]);
            $amount = str_replace(',', '', $this->input->post('amount'));

            $credit_total_credit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'credit_id' => $credit_id], 'amount')->amount;
            $credit_total_debit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'debit_id' => $credit_id], 'amount')->amount;
            $credit_tatal_bal = ($credit_total_credit - $credit_total_debit) - $this->data['ledger']->amount;

            $debit_total_credit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'credit_id' => $debit_id], 'amount')->amount;
            $debit_total_debit = $this->common->sum_this('ledger', ['coop_id' => $this->coop->id, 'debit_id' => $debit_id], 'amount')->amount;
            $debit_tatal_bal = ($debit_total_credit - $debit_total_debit) + $this->data['ledger']->amount;;
            $data = [
                'note' => $this->input->post('narration'),
                'created_by' => $this->user->id,
                'coop_id' => $this->coop->id,
                'credit_id' => $credit_id,
                'mop' => $this->input->post('mop'),
                'pv_no' => $this->input->post('pv_no'),
                'debit_id' => $debit_id,
                'amount' => $amount,
                'credit_bal' => $credit_tatal_bal + $amount,
                'debit_bal' => $debit_tatal_bal - $amount,
                'reference' => $this->input->post('reference'),
                'particular' => $this->input->post('particular'),
                'credit_name' => $credit_name->name,
                'debit_name' => $debit_name->name,
                'payment_date' => $this->input->post('payment_date'),
            ];

            // var_dump($data, $this->data['ledger']);exit;

            $activities = [
                'coop_id' => $this->coop->id,
                'user_id' => $this->user->id,
                'action' => lang('edited') . ' ' . lang('ledger')
            ];

            $this->common->start_trans();
            $this->common->update_this('ledger', ['id'=>$id, 'coop_id'=> $this->coop->id], $data);
            $this->common->add('activities', $activities);
            $this->common->finish_trans();

            if ($this->common->status_trans()) {
                $this->session->set_flashdata('message', lang('act_successful'));
                redirect('accounting/journal');
            } else {
                $this->session->set_flashdata('error', lang('act_unsuccessful'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->data['acc_title'] = $this->coa->get_acc_titles();
            $acc = $this->coa->get_title_and_subtitle_and_value(['acc_value.coop_id' => $this->coop->id]);
            $acc_data = [];
            foreach ($acc as $ac) {
                $acc_data[$ac->title_code . ' - ' . $ac->title][$ac->sub_title_code . ' - ' . $ac->sub_title][] = (object) [
                    'id' => $ac->value_id,
                    'name' => $ac->value_name,
                    'code' => $ac->value_code
                ];
            }
            $this->data['mop'] = $this->common->get_all('mop');
            $this->data['acc_data'] = $acc_data;
            $this->data['title'] = lang('edit').' '.lang('ledger_entry');
            $this->data['controller'] = lang('accounting');
            $this->layout->set_app_page('accounting/edit_ledger_entry', $this->data);
        }
    }

    public function delete_ledger_entry($id = null) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xdelete', 'on');
        $id = $this->utility->un_mask($id);
        
        $previous_data = $this->common->get_this('ledger', ['id' => $id]);
        $activities = [
            'coop_id' => $this->coop->id,
            'user_id' => $this->user->id,
            'action' => lang('deleted').' '.lang('ledger_entry'),
            'metadata' => $this->utility->activities_matadata($previous_data, [])
        ];

        $this->common->start_trans();
        $this->common->delete_this('ledger', ['id' => $id]);
        $this->common->add('activities', $activities);
        $this->common->finish_trans();

        if ($this->common->status_trans()) {
            $this->session->set_flashdata('message',lang('deleted') . ' ' . lang('ledger_entry'));
        } else {
            $this->session->set_flashdata('error', lang('act_unsuccessful'));
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    public function journal() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $where = [
            'ledger.coop_id' => $this->coop->id,
            'ledger.payment_date>=' => $start_date,
            'ledger.payment_date<=' => $end_date,
        ];

        if ($this->form_validation->run()) {
            $this->data['ledger'] = $this->info->get_journals($where);
        } else {
            $start_date = $this->utility->get_this_year('start');
            $end_date = $this->utility->get_this_year('end');
            $where = [
                'ledger.coop_id' => $this->coop->id,
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date,
            ];
            $this->data['ledger'] = $this->info->get_journals($where);
        }
        $this->data['start_date'] = $this->utility->just_date($start_date, false);
        $this->data['end_date'] = $this->utility->just_date($end_date, false);
        $this->data['title'] = lang('journals');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/journal', $this->data);
    }

    public function ledger($id) {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $id = $this->utility->un_mask($id);
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');

        if ($this->form_validation->run()) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $ledger_credit = $this->info->get_journals(['ledger.credit_id' => $id, 'ledger.coop_id' => $this->coop->id, 'ledger.payment_date>=' => $start_date, 'ledger.payment_date<=' => $end_date,]);
            $ledger_debit = $this->info->get_journals(['ledger.debit_id' => $id, 'ledger.coop_id' => $this->coop->id, 'ledger.payment_date>=' => $start_date, 'ledger.payment_date<=' => $end_date,]);
        } else {
            $start_date = $this->utility->get_this_year('start');
            $end_date = $this->utility->get_this_year('end');
            $ledger_credit = $this->info->get_journals(['ledger.credit_id' => $id, 'ledger.coop_id' => $this->coop->id, 'ledger.payment_date>=' => $start_date, 'ledger.payment_date<=' => $end_date,]);
            $ledger_debit = $this->info->get_journals(['ledger.debit_id' => $id, 'ledger.coop_id' => $this->coop->id, 'ledger.payment_date>=' => $start_date, 'ledger.payment_date<=' => $end_date,]);
        }

        foreach ($ledger_credit as $l_cr) {
            $l_cr->type = 'credit';
            $l_cr->bal = $l_cr->credit_bal;
        }

        foreach ($ledger_debit as $l_dr) {
            $l_dr->type = 'debit';
            $l_dr->bal = $l_dr->debit_bal;
        }
        $combine = array_merge($ledger_credit, $ledger_debit);
        array_multisort($combine,SORT_ASC);
        
        $this->data['ledger'] = $combine;
        $this->data['ledger_id'] = $this->utility->mask($id);
        $this->data['ledger_name'] = $this->common->get_this('acc_value',['id'=>$id])->name;
        $this->data['start_date'] = $this->utility->just_date($start_date, false);
        $this->data['end_date'] = $this->utility->just_date($end_date, false);
        $this->data['title'] = lang('ledger_statement');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/ledger', $this->data);
    }

    public function trial_balance() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'trim|required');
        $all_ledger = $this->common->get_all_these('acc_value', ['coop_id'=> $this->coop->id]);

        if ($this->form_validation->run() == true) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date
            ];
        } else {
            $start_date = $this->utility->get_this_year('start');
            $end_date = $this->utility->get_this_year('end');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date,
            ];
        }
        $all_debit = [];
        $all_credit = [];
        $trial_balance = [];
        $trial_balance2 = [];
        foreach ($all_ledger as $l) {
            $credit = $this->common->sum_this('ledger', ['credit_id' => $l->id], 'amount', $range);
            $debit = $this->common->sum_this('ledger', ['debit_id' => $l->id], 'amount', $range);
            if ($credit->amount) {
                $all_credit[] = (object) [
                            'acc_title_id' => $l->acc_title_id,
                            'id' => $l->id,
                            'name' => $l->name,
                            'amount' => $credit->amount,
                ];
            }

            if ($debit->amount) {
                $all_debit[] = (object) [
                            'acc_title_id' => $l->acc_title_id,
                            'id' => $l->id,
                            'name' => $l->name,
                            'amount' => $debit->amount,
                ];
            }
        }


        foreach ($all_credit as $cr) {
            foreach ($all_debit as $dr) {
                if ($cr->id == $dr->id) {
                    $trial_balance[$cr->name] = (object) [
                                'acc_title_id' => $cr->acc_title_id,
                                'id' => $cr->id,
                                'name' => $cr->name,
                                'balance' => abs($cr->amount - $dr->amount)
                    ];
                } else {
                    $trial_balance2[$cr->name] = (object) [
                                'acc_title_id' => $cr->acc_title_id,
                                'id' => $cr->id,
                                'name' => $cr->name,
                                'balance' => $cr->amount
                    ];
                    $trial_balance2[$dr->name] = (object) [
                                'acc_title_id' => $dr->acc_title_id,
                                'id' => $dr->id,
                                'name' => $dr->name,
                                'balance' => $dr->amount
                    ];
                }
            }
        }

        foreach ($trial_balance as $tb) {
            foreach ($trial_balance2 as $tb2) {
                if ($tb->id == $tb2->id) {
                    $tb2->id = $tb->id;
                    $tb2->name = $tb->name;
                    $tb2->balance = $tb->balance;
                }
            }
        }

        $this->data['trial_balance'] = $trial_balance2;
        $this->data['start_date'] = $this->utility->just_date($start_date, false);
        $this->data['end_date'] = $this->utility->just_date($end_date, false);
        $this->data['title'] = lang('trial_balance');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/trial_balance', $this->data);
    }

    public function balance_sheet() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'trim|required');

        $assets_id = 3;
        $liabilities_id = 4;
        $equity_id = 5;
        $where = "coop_id = {$this->coop->id} AND (acc_title_id = $assets_id OR acc_title_id = $liabilities_id OR acc_title_id =$equity_id)";
        $all_ledger = $this->common->get_all_these('acc_value', $where);
        if ($this->form_validation->run() == true) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date
            ];
        } else {
            $start_date = $this->utility->get_this_year('start');
            $end_date = $this->utility->get_this_year('end');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date,
            ];
        }

        $all_debit = $all_credit = $trial_balance = $trial_balance2 = [];
        foreach ($all_ledger as $l) {
            $credit = $this->common->sum_this('ledger', ['credit_id' => $l->id], 'amount', $range);
            $debit = $this->common->sum_this('ledger', ['debit_id' => $l->id], 'amount', $range);
            if ($credit->amount) {
                $all_credit[] = (object) [
                            'acc_title_id' => $l->acc_title_id,
                            'acc_sub_title_id' => $l->acc_sub_title_id,
                            'id' => $l->id,
                            'name' => $l->name,
                            'amount' => $credit->amount,
                ];
            }

            if ($debit->amount) {
                $all_debit[] = (object) [
                            'acc_title_id' => $l->acc_title_id,
                            'acc_sub_title_id' => $l->acc_sub_title_id,
                            'id' => $l->id,
                            'name' => $l->name,
                            'amount' => $debit->amount,
                ];
            }
        }

        foreach ($all_credit as $cr) {
            foreach ($all_debit as $dr) {
                if ($cr->id == $dr->id) {
                    $trial_balance[$cr->name] = (object) [
                                'acc_title_id' => $cr->acc_title_id,
                                'acc_sub_title_id' => $cr->acc_sub_title_id,
                                'id' => $cr->id,
                                'name' => $cr->name,
                                'balance' => abs($cr->amount - $dr->amount)
                    ];
                } else {
                    $trial_balance2[$cr->name] = (object) [
                                'acc_title_id' => $cr->acc_title_id,
                                'acc_sub_title_id' => $cr->acc_sub_title_id,
                                'id' => $cr->id,
                                'name' => $cr->name,
                                'balance' => $cr->amount
                    ];
                    $trial_balance2[$dr->name] = (object) [
                                'acc_title_id' => $dr->acc_title_id,
                                'acc_sub_title_id' => $dr->acc_sub_title_id,
                                'id' => $dr->id,
                                'name' => $dr->name,
                                'balance' => $dr->amount
                    ];
                }
            }
        }
        foreach ($trial_balance as $tb) {
            foreach ($trial_balance2 as $tb2) {
                if ($tb->id == $tb2->id) {
                    $tb2->id = $tb->id;
                    $tb2->name = $tb->name;
                    $tb2->balance = $tb->balance;
                }
            }
        }

        $assets = $liability = $equity = [];
        foreach ($trial_balance2 as $value) {
            $assets_sub_acc = $this->common->get_this('acc_sub_title', ['acc_title_id' => $assets_id, 'id' => $value->acc_sub_title_id]);
            if ($assets_sub_acc) {
                $assets[$assets_sub_acc->name][] = $value;
            }
        }

        foreach ($trial_balance2 as $value) {
            $assets_sub_acc = $this->common->get_this('acc_sub_title', ['acc_title_id' => $liabilities_id, 'id' => $value->acc_sub_title_id]);
            if ($assets_sub_acc) {
                $liability[$assets_sub_acc->name][] = $value;
            }
        }

        foreach ($trial_balance2 as $value) {
            $assets_sub_acc = $this->common->get_this('acc_sub_title', ['acc_title_id' => $equity_id, 'id' => $value->acc_sub_title_id]);
            if ($assets_sub_acc) {
                $equity[$assets_sub_acc->name][] = $value;
            }
        }

        $this->data['asset'] = $assets;
        $this->data['liability'] = $liability;
        $this->data['equity'] = $equity;
        $this->data['start_date'] = $this->utility->just_date($start_date, false);
        $this->data['end_date'] = $this->utility->just_date($end_date, false);

        $this->data['title'] = lang('balance_sheet');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/balance_sheet', $this->data);
    }

    public function income_statement() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'trim|required');

        $income_id = 1;
        $expenditure_id = 2;
        $where = "coop_id = {$this->coop->id} AND (acc_title_id = $income_id OR acc_title_id = $expenditure_id)";
        $all_ledger = $this->common->get_all_these('acc_value', $where);
        if ($this->form_validation->run() == true) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date
            ];
        } else {
            $start_date = $this->utility->get_this_year('start');
            $end_date = $this->utility->get_this_year('end');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date,
            ];
        }

        $all_debit = $all_credit = $trial_balance = $trial_balance2 = [];
        foreach ($all_ledger as $l) {
            $credit = $this->common->sum_this('ledger', ['credit_id' => $l->id], 'amount', $range);
            $debit = $this->common->sum_this('ledger', ['debit_id' => $l->id], 'amount', $range);
            if ($credit->amount) {
                $all_credit[] = (object) [
                            'acc_title_id' => $l->acc_title_id,
                            'acc_sub_title_id' => $l->acc_sub_title_id,
                            'id' => $l->id,
                            'name' => $l->name,
                            'amount' => $credit->amount,
                ];
            }

            if ($debit->amount) {
                $all_debit[] = (object) [
                            'acc_title_id' => $l->acc_title_id,
                            'acc_sub_title_id' => $l->acc_sub_title_id,
                            'id' => $l->id,
                            'name' => $l->name,
                            'amount' => $debit->amount,
                ];
            }
        }

        foreach ($all_credit as $cr) {
            foreach ($all_debit as $dr) {
                if ($cr->id == $dr->id) {
                    $trial_balance[$cr->name] = (object) [
                                'acc_title_id' => $cr->acc_title_id,
                                'acc_sub_title_id' => $cr->acc_sub_title_id,
                                'id' => $cr->id,
                                'name' => $cr->name,
                                'balance' => abs($cr->amount - $dr->amount)
                    ];
                } else {
                    $trial_balance2[$cr->name] = (object) [
                                'acc_title_id' => $cr->acc_title_id,
                                'acc_sub_title_id' => $cr->acc_sub_title_id,
                                'id' => $cr->id,
                                'name' => $cr->name,
                                'balance' => $cr->amount
                    ];
                    $trial_balance2[$dr->name] = (object) [
                                'acc_title_id' => $dr->acc_title_id,
                                'acc_sub_title_id' => $dr->acc_sub_title_id,
                                'id' => $dr->id,
                                'name' => $dr->name,
                                'balance' => $dr->amount
                    ];
                }
            }
        }
        foreach ($trial_balance as $tb) {
            foreach ($trial_balance2 as $tb2) {
                if ($tb->id == $tb2->id) {
                    $tb2->id = $tb->id;
                    $tb2->name = $tb->name;
                    $tb2->balance = $tb->balance;
                }
            }
        }

        $income = $expenses = [];
        foreach ($trial_balance2 as $value) {
            $income_sub_acc = $this->common->get_this('acc_sub_title', ['acc_title_id' => $income_id, 'id' => $value->acc_sub_title_id]);
            if ($income_sub_acc) {
                $income[$income_sub_acc->name][] = $value;
            }
        }

        foreach ($trial_balance2 as $value) {
            $expenses_sub_acc = $this->common->get_this('acc_sub_title', ['acc_title_id' => $expenditure_id, 'id' => $value->acc_sub_title_id]);
            if ($expenses_sub_acc) {
                $expenses[$expenses_sub_acc->name][] = $value;
            }
        }


        $this->data['income'] = $income;
        $this->data['expenses'] = $expenses;
        $this->data['start_date'] = $this->utility->just_date($start_date, false);
        $this->data['end_date'] = $this->utility->just_date($end_date, false);

        $this->data['title'] = lang('income_statement');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/income_statement', $this->data);
    }

    public function cash_flow() {
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->form_validation->set_rules('start_date', $this->lang->line("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', $this->lang->line("end_date"), 'trim|required');
        if ($this->form_validation->run() == true) {
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date
            ];
        } else {
            $start_date = $this->utility->get_this_year('start');
            $end_date = $this->utility->get_this_year('end');
            $range = [
                'ledger.payment_date>=' => $start_date,
                'ledger.payment_date<=' => $end_date,
            ];
        }

        $this->data['start_date'] = $this->utility->just_date($start_date, false);
        $this->data['end_date'] = $this->utility->just_date($end_date, false);
        $this->data['title'] = lang('cash_flow');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/cashflow', $this->data);
    }

    public function auto_postings($id=null){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xread', 'on');
        $this->data['savings_tracker'] = $this->common->get_this('gl_savings_tracker', ['coop_id'=>$this->coop->id]);
        $this->data['withdrawal_tracker'] = $this->common->get_this('gl_withdrawal_tracker', ['coop_id'=>$this->coop->id]);
        $this->data['loan_tracker']  = $this->common->get_this('gl_loan_tracker', ['coop_id' => $this->coop->id]);
        $this->data['loan_repayment_tracker']  = $this->common->get_this('gl_loan_repayment_tracker', ['coop_id' => $this->coop->id]);
        $this->data['credit_sales_tracker']  = $this->common->get_this('gl_credit_sales_tracker', ['coop_id' => $this->coop->id]);
        $this->data['credit_sales_repayment_tracker'] = $this->common->get_this('gl_credit_sales_repayment_tracker', ['coop_id' => $this->coop->id]);
        $this->data['title'] = lang('auto_posting');
        $this->data['controller'] = lang('accounting');
        $this->layout->set_app_page('accounting/auto_posting', $this->data);
    }

    public function auto_post_options($name=''){
        $this->data['acc_title'] = $this->coa->get_acc_titles();

        $acc = $this->coa->get_title_and_subtitle_and_value(['acc_value.coop_id' => $this->coop->id]);
        $acc_data = [];
        foreach ($acc as $ac) {
            $acc_data[$ac->title_code . ' - ' . $ac->title][$ac->sub_title_code . ' - ' . $ac->sub_title][] = (object) [
                'id' => $ac->value_id,
                'name' => $ac->value_name,
                'code' => $ac->value_code
            ];
        }

        $this->data['savings_type'] = $this->common->get_all_these('savings_types', ['coop_id' => $this->coop->id]);
        $this->data['loan_type'] = $this->common->get_all_these('loan_types', ['coop_id' => $this->coop->id]);
        $this->data['product_type'] = $this->common->get_all_these('product_types', ['coop_id' => $this->coop->id]);
        $this->data['mop'] = $this->common->get_all('mop');
        $this->data['pv_no'] = $this->utility->generate_pv_no($this->coop->coop_name);
        $this->data['acc_data'] = $acc_data;
        $this->data['name'] = $name;
        $this->data['title'] = lang($name) . ' ' . lang('auto_posting');
        $this->data['controller'] = lang('accounting');

        if($name =='savings' or $name == 'withdrawal'){
            $this->data['tracker'] = $this->coa->get_gl_savings_tracker(['acc_value.coop_id' => $this->coop->id]);
            if($name =='withdrawal'){
                $this->data['tracker'] = $this->coa->get_gl_withdrawal_tracker(['acc_value.coop_id' => $this->coop->id]);
            }
            $this->layout->set_app_page('accounting/auto_post_options', $this->data);
        }else if($name == 'reg_fee'){

        }else{
            if ($name == 'loan') {
                $this->data['tracker'] = $this->coa->get_gl_loan_tracker(['acc_value.coop_id' => $this->coop->id], 'gl_loan_tracker');
            }
            if ($name == 'loan_repayment') {
                $this->data['tracker'] = $this->coa->get_gl_loan_tracker(['acc_value.coop_id' => $this->coop->id], 'gl_loan_repayment_tracker');
            }
            if ($name == 'credit_sales') {
                $this->data['tracker'] = $this->coa->get_gl_credit_sales_tracker(['acc_value.coop_id' => $this->coop->id], 'gl_credit_sales_tracker');
            }
            if ($name == 'credit_sales_repayment') {
                $this->data['tracker'] = $this->coa->get_gl_credit_sales_tracker(['acc_value.coop_id' => $this->coop->id], 'gl_credit_sales_repayment_tracker');
            }
            $this->layout->set_app_page('accounting/auto_post_loan', $this->data);
        }
    }

    public function update_auto_post(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
        $this->form_validation->set_rules('savings_type', lang('savings_type'), 'trim|required');
        $this->form_validation->set_rules('dr', lang('dr'), 'trim|required');
        $this->form_validation->set_rules('cr', lang('cr'), 'trim|required');
        $this->form_validation->set_rules('ledger_type', lang('ledger_type'), 'trim|required');
      
        if ($this->form_validation->run()) {
            $savings_type = $this->input->post('savings_type');
            $data = [
                'coop_id'=> $this->coop->id,
                'created_on'=> date('Y-m-d H:i:s'),
                'savings_type'=> $savings_type,
                'dr'=> $this->input->post('dr'),
                'cr'=> $this->input->post('cr'),
            ];

            if($data['cr']== $data['dr']){
                $this->session->set_flashdata('error', lang('cr_dr_cannot_be_equal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $name = 'gl_'.$this->input->post('ledger_type'). '_tracker';
            $record_exist = $this->common->get_this($name, ['coop_id'=>$this->coop->id, 'savings_type'=> $savings_type]);
            if($record_exist){
                $this->common->update_this($name, ['coop_id' => $this->coop->id, 'savings_type' => $savings_type], $data);
            }else{
                $this->common->add($name,$data);
            }
            $this->session->set_flashdata('message', lang('act_successful'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->session->set_flashdata('error', validation_errors());
        redirect($_SERVER["HTTP_REFERER"]);
    }

    // use for both loans and credit sales
    public function update_auto_post_loan(){
        $this->utility->access_check(self::MENU_ID, $this->user->role_id, 'xwrite', 'on');
       if($this->input->post('ledger_type') == 'loan' or  $this->input->post('ledger_type') == 'loan_repayment'){
            $type = $this->input->post('loan_type');
            $this->form_validation->set_rules('loan_type', lang('loan_type'), 'trim|required');
       }else{
            $type = $this->input->post('product_type');
            $this->form_validation->set_rules('product_type', lang('product_type'), 'trim|required');
       }
        $this->form_validation->set_rules('principal_dr', lang('principal').' '.lang('dr'), 'trim|required');
        $this->form_validation->set_rules('principal_cr', lang('principal') . ' ' . lang('cr'), 'trim|required');
        $this->form_validation->set_rules('interest_dr', lang('interest').' '.lang('dr'), 'trim|required');
        $this->form_validation->set_rules('interest_cr', lang('interest') . ' ' . lang('cr'), 'trim|required');
        $this->form_validation->set_rules('ledger_type', lang('ledger_type'), 'trim|required');
      
        if ($this->form_validation->run()) {
           
            $data = [
                'coop_id'=> $this->coop->id,
                'created_on'=> date('Y-m-d H:i:s'),
                'principal_dr'=> $this->input->post('principal_dr'),
                'principal_cr'=> $this->input->post('principal_cr'),
                'interest_dr'=> $this->input->post('interest_dr'),
                'interest_cr'=> $this->input->post('interest_cr'),
            ];

            if ($this->input->post('ledger_type') == 'loan' or  $this->input->post('ledger_type') == 'loan_repayment') {
                $data['loan_type'] = $type;
                $where = ['coop_id' => $this->coop->id, 'loan_type' => $type];
            } else {
                $data['product_type'] = $type;
                $where = ['coop_id' => $this->coop->id, 'product_type' => $type];
            }

            if(($data['principal_dr']== $data['principal_cr']) || ($data['interest_dr'] == $data['interest_cr'])){
                $this->session->set_flashdata('error', lang('cr_dr_cannot_be_equal'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            $name = 'gl_'.$this->input->post('ledger_type'). '_tracker';
            $record_exist = $this->common->get_this($name, $where);
            if($record_exist){
                $this->common->update_this($name, $where, $data);
            }else{
                $this->common->add($name,$data);
            }
            $this->session->set_flashdata('message', lang('act_successful'));
            redirect('accounting/auto_postings');
        }
        $this->session->set_flashdata('error', validation_errors());
        redirect($_SERVER["HTTP_REFERER"]);
    }

     public function ajax_enable_auto_posting() {
        $status = $this->input->get('status', true);
        $gl_savings = $this->common->get_this('gl_savings_tracker', ['coop_id'=>$this->coop->id]);
        $gl_withdrwal = $this->common->get_this('gl_withdrawal_tracker', ['coop_id'=>$this->coop->id]);
        $gl_loan = $this->common->get_this('gl_loan_tracker', ['coop_id'=>$this->coop->id]);
        $gl_loan_repayment = $this->common->get_this('gl_loan_repayment_tracker', ['coop_id'=>$this->coop->id]);
        $gl_credit_sales = $this->common->get_this('gl_credit_sales_tracker', ['coop_id'=>$this->coop->id]);
        $gl_credit_sales_repayment = $this->common->get_this('gl_credit_sales_repayment_tracker', ['coop_id'=>$this->coop->id]);

        if (!$gl_savings or !$gl_withdrwal or !$gl_loan or !$gl_loan_repayment or !$gl_credit_sales or !$gl_credit_sales_repayment){
            echo json_encode(array('status' => 'error', 'message' => 'Cannot be enabled. Automatic posting needs to be properly configured 
            for savings, withdrawal, loan, loan repaymeny, credit sales and credit sales repayment'));
        }else{
            if ($status == 'false') {
                $updated =  $this->common->update_this('cooperatives', ['id' => $this->coop->id], ['ledger_auto_post' => 'true']);
            }
            if ($status == 'true') {
                $updated = $this->common->update_this('cooperatives', ['id' => $this->coop->id], ['ledger_auto_post' => 'false']);
            }

            if ($updated) {
                echo json_encode(array('status' => 'success', 'message' => "action successfull"));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
            }
        }

        
    }
}
