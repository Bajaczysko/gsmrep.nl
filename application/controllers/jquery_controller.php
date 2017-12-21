<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jquery_controller extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        // Load required CI libraries and helpers.
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->model('jquery_engine');

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

    public function get_toestel()
    {
        $this->jquery_engine->get_toestel();
    }

    public function get_reparatie()
    {
        $this->jquery_engine->get_reparatie();
    }

    public function search_product()
    {
        $this->jquery_engine->search_product();
    }


}