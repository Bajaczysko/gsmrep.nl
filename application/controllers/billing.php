<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class billing extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        // Load required CI libraries and helpers.
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');

        // IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded!
        // It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
        $this->auth = new stdClass;

        // Load 'standard' flexi auth library by default.
        $this->load->library('flexi_auth');

        // Check user is logged in via either password or 'Remember me'.
        // Note: Allow access to logged out users that are attempting to validate a change of their email address via the 'update_email' page/method.
        if (! $this->flexi_auth->is_logged_in() && ((!$this->flexi_auth->is_privileged('4')) || (!$this->flexi_auth->is_privileged('5'))))
        {
            // Set a custom error message.
            $this->flexi_auth->set_error_message('You dont have the permissions, TRUE');
            $this->session->set_flashdata('message', $this->flexi_auth->get_messages());
            redirect('auth');
        }

        // Define a global variable to store data that is then used by the end view page.

        $this->data = null;
    }

    public function index()
    {
    }

    public function manage()
    {
        // load models
        $this->load->model('interface_model');
        $this->load->model('billing_model');
        $this->load->model('report_model');

        // Check user has privileges to view user accounts, else display a message to notify the user they do not have valid privileges.
        if (! $this->flexi_auth->is_privileged('4'))
        {
            $this->session->set_flashdata('message', '<p class="error_msg">You do not have privileges to view user accounts.</p>');
            redirect('auth');
        }

        if ($this->input->post('search_customer') && $this->input->post('search_query'))
        {
            // Convert uri ' ' to '-' spacing to prevent '20%'.
            // Note: Native php functions like urlencode() could be used, but by default, CodeIgniter disallows '+' characters.
            $search_query = str_replace(' ','-',$this->input->post('search_query'));
            $this->data['search_query'] = $this->input->post('search_query');

            // Assign search to query string.
            redirect('billing/manage/search/'.$search_query.'/page/');
        }

        $this->billing_model->get_billing_data();

        $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];

        // load interface
        $this->interface_model->full_interface('billing/manage_billing_view', $this->data, $this->language_model->translate('Manage Bills'));

    }

    public function expected_customers()
    {
        // load models
        $this->load->model('interface_model');
        $this->load->model('billing_model');

        // Check user has privileges to view user accounts, else display a message to notify the user they do not have valid privileges.
        if (! $this->flexi_auth->is_privileged('4'))
        {
            $this->session->set_flashdata('message', '<p class="error_msg">You do not have privileges to view user accounts.</p>');
            redirect('auth');
        }

        if ($this->input->post('search_customer') && $this->input->post('search_query'))
        {
            // Convert uri ' ' to '-' spacing to prevent '20%'.
            // Note: Native php functions like urlencode() could be used, but by default, CodeIgniter disallows '+' characters.
            $search_query = str_replace(' ','-',$this->input->post('search_query'));
            $this->data['search_query'] = $this->input->post('search_query');

            // Assign search to query string.
            redirect('billing/expected_customers/search/'.$search_query.'/page/');
        }

        $this->billing_model->get_expected_billing_data();

        $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];

        // load interface
        $this->interface_model->full_interface('billing/expected_customer_view', $this->data, $this->language_model->translate('Expected Customers'));


    }

    public function edit()
    {
        $billing_id = $this->uri->segment(3, 0);

        // load models
        $this->load->model('billing_model');

        // load data
        $this->billing_model->get_bill_information();
        $this->billing_model->load_bill_items();
        $this->billing_model->load_bill_customer_information();
        $this->billing_model->load_quick_products();

        // if post then update bill
        if($this->input->post())
        {
            $this->billing_model->update_bill_item_qty();
        }

        // load interface
        $this->interface_model->full_interface('billing/edit_billing_view', $this->data, $this->language_model->translate('Edit Bill'));

    }

    public function confirm()
    {

        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->confirm_billing();

    }

    public function confirm_change()
    {

        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->confirm_billing_change_payment();

    }

    public function remove()
    {
        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->remove_billing();

    }

    public function add_item()
    {

        // load models
        $this->load->model('interface_model');
        $this->load->model('jquery_engine');
        $this->load->model('billing_model');

        //if not posted
        $this->jquery_engine->list_merken();

        // if posted
        if($this->input->post())
        {
            $this->billing_model->add_item_to_billing();
        }

        // load interface
        $this->interface_model->full_interface('billing/add_item', $this->data);

    }

    public function add_quick_product()
    {
        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->add_quick_product();
    }

    public function delete_item()
    {

        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->delete_item_from_billing();

    }

    public function expected_to_billing()
    {
        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->expected_to_billing_converter();

    }

    public function select_customer()
    {
        // load models
        $this->load->model('interface_model');
        $this->load->model('customer_model');
        $this->load->model('billing_model');

        $uri = $this->uri->uri_to_assoc(3);

        // set billingid to the session after removing the old one
        if ($this->uri->segment(3, 0) != 'search'){
        $billing_id = $this->uri->segment(3, 0);
        $this->session->unset_userdata('billing_ID');
        $this->session->set_userdata('billing_ID', $billing_id);
        }

        // Check user has privileges to view user accounts, else display a message to notify the user they do not have valid privileges.
        if (! $this->flexi_auth->is_privileged('4'))
        {
            $this->session->set_flashdata('message', '<p class="error_msg">You do not have privileges to view user accounts.</p>');
            redirect('auth');
        }

        // check if post is new customer then create new customer
        if($this->input->post('new-customer-input'))
        {
            $this->billing_model->new_customer();
        }

        if ($this->input->post('search_customer') && $this->input->post('search_query'))
        {
            // Convert uri ' ' to '-' spacing to prevent '20%'.
            // Note: Native php functions like urlencode() could be used, but by default, CodeIgniter disallows '+' characters.
            $search_query = str_replace(' ','-',$this->input->post('search_query'));
            $this->data['search_query'] = $this->input->post('search_query');

            // Assign search to query string.
            redirect('billing/select_customer/search/'.$search_query.'/page/');
        }

        $this->customer_model->get_customers();

        $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];

        // load interface
        $this->interface_model->full_interface('billing/customeroverview_view', $this->data, $this->language_model->translate('For which customer is this bill?'));

    }

    public function link_customer()
    {
        // load models
        $this->load->model('billing_model');

        // call to action
        $this->billing_model->link_customer();

    }

    public function add_coupon()
    {
        // load models
        $this->load->model('billing_model');

        // load data
        $this->billing_model->get_coupons();

        if($this->input->post())
        {
            // call to action
            $this->billing_model->link_coupon();
        }

        // load interface
        $this->interface_model->full_interface('billing/link_coupon', $this->data, $this->language_model->translate('Select Coupon'));


    }

    public function view_bill()
    {
        // load models
        $this->load->model('billing_model');
        $this->load->model('barcode_model');

        // load information
        $this->billing_model->load_bill_company_information();
        $this->billing_model->load_bill_customer_information();
        $this->billing_model->load_bill_items();
        $this->billing_model->get_bill_information();

        // load views
        $this->load->view('billing/bill_view', $this->data);
    }

    public function view_invoice()
    {
        // load models
        $this->load->model('billing_model');
        $this->load->model('barcode_model');

        // load information
        $this->billing_model->load_bill_company_information();
        $this->billing_model->load_bill_customer_information();
        $this->billing_model->load_bill_items();

        // set post to get
        if($this->input->post())
        {
            $period = $this->input->post('period');
            $invoice_id = $this->uri->segment(3, 0);
            redirect("/billing/view_invoice/$invoice_id/$period");
        }

        // load views
        $this->load->view('billing/invoice_view', $this->data);

    }

    public function daily_check()
    {
        // load models
        $this->load->model('billing_model');
        $this->load->model('report_model');

        // load data
        // tile information functions
        $this->report_model->user_daily_total_bills();
        $this->report_model->user_daily_total_pin();
        $this->report_model->user_daily_total_contant();
        $this->report_model->user_daily_total_factuur();

        // load views
        $this->interface_model->full_interface('billing/daily_check_view', $this->data, $this->language_model->translate('Dagelijks overzicht'));

    }



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */