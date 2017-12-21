<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporting extends CI_Controller {

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
        if (! $this->flexi_auth->is_logged_in() && ((!$this->flexi_auth->is_privileged('9')) || (!$this->flexi_auth->is_privileged('10'))))
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

        // load models
        $this->load->model('interface_model');

        // load interface
        $this->interface_model->full_interface('reporting/reporting_view', $this->data, $this->language_model->translate('Reporting'));

    }

    public function set()
    {
        // load models
        $this->load->model('report_model');

        // load functions and data in models
        $this->report_model->set_data_buttons();
    }

    public function set_post()
    {
        // load models
        $this->load->model('report_model');

        // load functions and data in models
        $this->report_model->set_data_posts();
    }

    public function statistics()
    {

        // load models
        $this->load->model('report_model');

        // load functions and data in models

        // load variables
        $shop_name = $this->report_model->id_to_shop_name($this->uri->segment(3, 0));

        // load views and display data
        $this->interface_model->full_interface_reporting('reporting/reporting_statistics', $this->data, $this->language_model->translate('Reporting of ' . $shop_name));

    }

    public function turnover()
    {

        // load models
        $this->load->model('report_model');

        // load date range correctly
        $this->report_model->get_right_date_range();

        // load functions and data in models
        $this->report_model->accepted_bills();
        $this->report_model->turnover_product();
        $this->report_model->draft_bills();
        $this->report_model->removed_bills();
        $this->report_model->customers_no_shows();

        // tile information functions
        $this->report_model->period_turnover_total();
        $this->report_model->period_turnover_total_tax();
        $this->report_model->period_turnover_subtotal();
        $this->report_model->period_total_products();
        $this->report_model->period_total_bills();
        $this->report_model->period_total_pin();
        $this->report_model->period_total_contant();
        $this->report_model->period_total_factuur();

        // load variables
        $shop_name = $this->report_model->id_to_shop_name($this->uri->segment(3, 0));

        // load views and display data
        $this->interface_model->full_interface_reporting('reporting/reporting_turnover', $this->data, $this->language_model->translate('Omzet voor ' . $shop_name));

    }

    public function order_lines()
    {
        // load models
        $this->load->model('report_model');

        // load functions and data in models
        $this->report_model->reverse_bill_search();

        // load views and display data
        $this->interface_model->full_interface('reporting/reporting_view_all_bills', $this->data, $this->language_model->translate('Bonnen item overzicht'));

    }

    public function edit_bill()
    {
        $billing_id = $this->uri->segment(3, 0);

        // load models
        $this->load->model('billing_model');
        $this->load->model('report_model');

        // load data
        $this->report_model->get_bill_information();
        $this->billing_model->load_bill_items();
        $this->billing_model->load_bill_customer_information();

        // if post then update bill
        if($this->input->post())
        {
            $this->billing_model->update_bill_item_qty();
        }

        // load interface
        $this->interface_model->full_interface('billing/edit_billing_view', $this->data, $this->language_model->translate('Edit Bill'));

    }

    public function manage()
    {
        // load models
        $this->load->model('interface_model');
        $this->load->model('billing_model');
        $this->load->model('report_model');

        if ($this->input->post('search_customer') && $this->input->post('search_query'))
        {
            // Convert uri ' ' to '-' spacing to prevent '20%'.
            // Note: Native php functions like urlencode() could be used, but by default, CodeIgniter disallows '+' characters.
            $search_query = str_replace(' ','-',$this->input->post('search_query'));
            $this->data['search_query'] = $this->input->post('search_query');

            // Assign search to query string.
            redirect('reporting/manage/search/'.$search_query.'/page/');
        }

        $this->report_model->get_billing_data();

        $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];

        // load interface
        $this->interface_model->full_interface('reporting/manage_billing_view', $this->data, $this->language_model->translate('Reporting overview all bills'));

    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */