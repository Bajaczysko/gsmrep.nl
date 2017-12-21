<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class catalog extends CI_Controller {

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
        if (! $this->flexi_auth->is_logged_in() && ((!$this->flexi_auth->is_privileged('1')) || (!$this->flexi_auth->is_privileged('2'))))
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
        $this->products();
    }

    public function products()
    {

        // load models
        $this->load->model('interface_model');
        $this->load->model('product_model');
        $this->load->model('woocommerce_model');


        // first process the store change
        $this->product_model->process_store_change();

        // Check user has privileges to view user accounts, else display a message to notify the user they do not have valid privileges.
        if (! $this->flexi_auth->is_privileged('1'))
        {
            $this->session->set_flashdata('message', '<p class="error_msg">You do not have privileges to view products.</p>');
            redirect('auth');
        }

        if ($this->input->post('search_product') && $this->input->post('search_query'))
        {
            // Convert uri ' ' to '-' spacing to prevent '20%'.
            // Note: Native php functions like urlencode() could be used, but by default, CodeIgniter disallows '+' characters.
            $search_query = str_replace(' ','-',$this->input->post('search_query'));
            $this->data['search_query'] = $this->input->post('search_query');

            // Assign search to query string.
            redirect('catalog/products/search/'.$search_query.'/page/');
        }

        $this->product_model->get_stores();
        $this->product_model->get_products();


        $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];


        // load interface
        $this->interface_model->full_interface('catalog/products_view', $this->data, $this->language_model->translate('Product Manager'));

    }

    public function categories()
    {

        // load models
        $this->load->model('interface_model');
        $this->load->model('product_model');

        // Check user has privileges to view user accounts, else display a message to notify the user they do not have valid privileges.
        if (! $this->flexi_auth->is_privileged('1'))
        {
            $this->session->set_flashdata('message', '<p class="error_msg">You do not have privileges to view user accounts.</p>');
            redirect('auth');
        }

        if ($this->input->post('search_product') && $this->input->post('search_query'))
        {
            // Convert uri ' ' to '-' spacing to prevent '20%'.
            // Note: Native php functions like urlencode() could be used, but by default, CodeIgniter disallows '+' characters.
            $search_query = str_replace(' ','-',$this->input->post('search_query'));
            $this->data['search_query'] = $this->input->post('search_query');

            // Assign search to query string.
            redirect('catalog/categories/search/'.$search_query.'/page/');
        }

        $this->product_model->get_categories();

        $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];


        // load interface
        $this->interface_model->full_interface('catalog/categories_view', $this->data, $this->language_model->translate('Categories'));

    }

    public function attributes()
    {

        // load models
        $this->load->model('interface_model');
        $this->load->model('product_model');

        // define message
        $this->data['message'] = '';

        // load data into model
        $this->product_model->get_all_attributes();

        // load interface
        $this->interface_model->full_interface('catalog/attributes_view', $this->data, $this->language_model->translate('Attributes'));

    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */