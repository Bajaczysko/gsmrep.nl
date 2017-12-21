<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interface_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function full_interface($page = 'home', $data, $pagetitle = false)
    {

        $this->load->model('auth_admin_model');
        $this->load->model('report_model');

        // load head interface
        $this->load->view('base/head', $data);
        $this->load->view('base/header', $data);

        // load page title if not false
        if($pagetitle)
        {
            $this->load->view('base/page_title', $data = array('pagetitle' => $pagetitle));
        }

        // load page from controller
        $this->load->view($page, $data);

        // load foot interface
        $this->load->view('base/foot', $data);
        $this->load->view('base/footer', $data);

    }

    public function full_interface_reporting($page = 'home', $data, $pagetitle = false)
    {

        $this->load->model('auth_admin_model');
        $this->load->model('report_model');

        // load head interface
        $this->load->view('base/head', $data);
        $this->load->view('base/header', $data);

        // load page title if not false
        if($pagetitle)
        {
            $this->load->view('base/page_title', $data = array('pagetitle' => $pagetitle));
        }

        // load the search bar
        $this->load->view('reporting/reporting_search_bar', $data);

        // load page from controller
        $this->load->view($page, $data);

        // load foot interface
        $this->load->view('base/foot', $data);
        $this->load->view('base/footer', $data);

    }

    public function interface_no_title($page = 'home', $data, $pagetitle = false)
    {

        $this->load->model('auth_admin_model');

        // load head interface
        $this->load->view('base/head', $data);
        $this->load->view('base/header', $data);

        // load page from controller
        $this->load->view($page, $data);

        // load foot interface
        $this->load->view('base/foot', $data);
        $this->load->view('base/footer', $data);


    }

    public function build_table($title, $column)
    {

    }


}