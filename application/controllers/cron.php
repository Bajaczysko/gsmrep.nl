<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cron extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        // Load required CI libraries and helpers.
        $this->load->database();
        $this->data = null;
    }

    public function index()
    {

        $this->one_minute();
        $this->five_minutes();

    }

    public function one_minute()
    {
        // disable php timeout
        set_time_limit (0);

        // models
        $this->load->model('email_model');

        // run task
        $this->email_model->send_email();

        echo "done";
    }

    public function five_minutes()
    {

        // disable php timeout
        set_time_limit (0);

        // load models
        $this->load->model('woocommerce_model');
        $this->load->model('report_model');

        // run tasks
        $this->woocommerce_model->sync_woocommerce_orders();
        // $this->report_model->generate_reports();
    }

    public function hourly()
    {
        // disable php timeout
        set_time_limit (0);

        // load models
        $this->load->model('general_task_model');
        $this->load->model('woocommerce_model');

        // run task
        $this->general_task_model->generate_search_bill_results();
    }

    public function daily()
    {
        // disable php timeout
        set_time_limit (0);

        // load models
        $this->load->model('general_task_model');
        $this->load->model('woocommerce_model');
        $this->load->model('xml_feed_model');

        // run task
        $this->woocommerce_model->import_categories();
        $this->woocommerce_model->import_products();
        //$this->xml_feed_model->import_xml_feed();

    }

public function special() {
// disable php timeout
        set_time_limit (0);

        // load models
        $this->load->model('general_task_model');
        $this->load->model('woocommerce_model');
        $this->load->model('xml_feed_model');

        // run task
$this->woocommerce_model->import_products();
}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */