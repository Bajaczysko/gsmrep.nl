<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Default_controller extends CI_Controller {

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
        if (! $this->flexi_auth->is_logged_in() && $this->uri->segment(2) != 'update_email')
        {
            // Set a custom error message.
            $this->flexi_auth->set_error_message('You must login to access this area.', TRUE);
            $this->session->set_flashdata('message', $this->flexi_auth->get_messages());
            redirect('auth');
        }

        // Define a global variable to store data that is then used by the end view page.

        $this->data = null;
    }

    public function index()
    {
        $this->load->model('interface_model');
        $this->load->model('jquery_engine');
        $this->load->model('billing_model');

         //if not posted
         $this->jquery_engine->list_merken();

        // if posted
        if($this->input->post())
        {
            $this->billing_model->create_new_billing();
        }

        // load interface
        $this->interface_model->full_interface('user/home_view', $this->data);
    }

    public function fixing()
    {
        // disable php timeout
        set_time_limit (0);

        $this->load->model('general_task_model');
      //  $this->general_task_model->fix_customer_data();
    }

    public function woocommerce()
    {

        // disable php timeout
        set_time_limit (0);

        $this->load->model('woocommerce_model');
        $this->load->model('xml_feed_model');

        //$this->xml_feed_model->import_xml_feed();

        //echo "<pre>";
        //$this->woocommerce_model->import_products();
        //echo "</pre>";



       // $this->woocommerce_model->import_categories();
    }

    public function email()
    {
        $this->load->model('email_model');
        $this->email_model->generate_billing_bill();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */