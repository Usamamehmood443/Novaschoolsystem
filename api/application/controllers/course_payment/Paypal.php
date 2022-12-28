<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Paypal extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();
        $this->load->library('paypal_payment');
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
    }
    
    /*
    This is used to show payment detail page
    */
    public function index() {

        $pay_method = $this->paymentsetting_model->getActiveMethod();
        if ($pay_method->payment_type == "paypal") {
            $data = array();
            if ($this->session->has_userdata('course_amount')) {
                if ($pay_method->api_username != "" && $pay_method->api_password != "" && $pay_method->api_signature != "") {
                    $params = $this->session->userdata('course_amount');
                    $data['session_params'] = $params;
                    $data['setting'] = $this->setting;
                    
                }
                $this->load->view('course_payment/paypal/index', $data);
            }
        } else {
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('payment/error');
        }
    }
    
    /*
    This is for payment gateway functionality
    */
    public function pay() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            
        }
    }

    //paypal successpayment
    public function getsuccesspayment() {
        $params = $this->session->userdata('params');
        $data = array();
        $student_fees_master_id = $params['student_fees_master_id'];
        $fee_groups_feetype_id = $params['fee_groups_feetype_id'];
        $student_id = $params['student_id'];
        $total = $params['total'];

        $data['student_fees_master_id'] = $student_fees_master_id;
        $data['fee_groups_feetype_id'] = $fee_groups_feetype_id;
        $data['student_id'] = $student_id;
        $data['total'] = $total;
        $data['symbol'] = $params['invoice']->symbol;
        $data['currency_name'] = $params['invoice']->currency_name;
        $data['name'] = $params['name'];
        $data['guardian_phone'] = $params['guardian_phone'];
        $response = $this->paypal_payment->success($data);

        $paypalResponse = $response->getData();
        if ($response->isSuccessful()) {
            $purchaseId = $_GET['PayerID'];

            if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
                if ($purchaseId) {
                    $params = $this->session->userdata('params');
                    $ref_id = $paypalResponse['PAYMENTINFO_0_TRANSACTIONID'];
                    $json_array = array(
                        'amount' => $params['total'],
                        'date' => date('Y-m-d'),
                        'amount_discount' => 0,
                        'amount_fine' => $params['payment_detail']->fine_amount,
                        'received_by' => '',
                        'description' => "Online fees deposit through Paypal Ref ID: " . $ref_id,
                        'payment_mode' => 'Paypal',
                    );
                    $data = array(
                        'student_fees_master_id' => $params['student_fees_master_id'],
                        'fee_groups_feetype_id' => $params['fee_groups_feetype_id'],
                        'amount_detail' => $json_array,
                    );
                    $send_to = $params['guardian_phone'];
                    $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to, "");
                    $invoice_detail = json_decode($inserted_id);
                    redirect("payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id, "refresh");
                }
            }
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
            redirect('payment/paymentfailed', 'refresh');
        }
    }
}