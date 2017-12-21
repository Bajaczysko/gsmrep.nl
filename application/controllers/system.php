<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class system extends CI_Controller {

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
        if (! $this->flexi_auth->is_logged_in() && !$this->flexi_auth->is_privileged('11'))
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
        $this->load->model('interface_model');
        $this->load->model('jquery_engine');

        //if not posted
        $this->jquery_engine->list_merken();

        // load interface
        $this->interface_model->full_interface('user/home_view', $this->data);
    }

    public function user_management()
    {
        // load models
        $this->load->model('auth_admin_model');
        $this->load->model('interface_model');

        // load functions from models into data
        $this->auth_admin_model->get_user_accounts();

        // load interface
        $this->interface_model->full_interface('configuration/user_management_view', $this->data, $this->language_model->translate('User management'));

    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */