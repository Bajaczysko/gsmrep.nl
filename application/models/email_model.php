<?php

class Email_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function send_email()
    {

        // set the email settings
        $this->load->library('email');
        $config['protocol'] = 'smtp';
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $config['smtp_host'] = '46.243.156.170';
        $config['smtp_user'] = 'outgoing@bvisually.nl';
        $config['smtp_pass'] = 'sLNXTfUCk2';

        $this->email->initialize($config);

        // get all emails that are not send yet
        $query = $this->db->query("select * from `core_emails` where `send` = '0'");
        if($query->num_rows() > 0)
        {
            // process the query to send the mails
            foreach($query->result() as $email_data)
            {
                // clear data from previous state
                $this->email->clear();

                // set new data
                $this->email->from($email_data->email_from, $email_data->name_from);
                $this->email->to($email_data->email_to);

                if(!empty($email_data->email_bcc)):
                    $this->email->bcc($email_data->email_bcc);
                endif;

                $this->email->subject($email_data->subject);
                $this->email->message($email_data->message);

                $this->email->send();

                // set email as sended
                $email_line_id = $email_data->id;
                $this->db->query("update `core_emails` set `send`='1' where `id`='$email_line_id'");
            }
        }


    }

    public function write_email()
    {

    }

    public function generate_billing_bill($billing_id)
    {

        // load models
        $this->load->model('billing_model');

        // set data
        $this->billing_model->load_bill_items_by_id($billing_id);
        $this->billing_model->get_bill_information_by_id($billing_id);

        $customer_info = $this->billing_model->load_customer_from_billingid($billing_id);
        $query_info = $this->billing_model->load_bill_company_information_by_id($billing_id);

        if(!empty($customer_info->email)):

        $message = $this->load->view('email/review_email', $this->data, true);

        $set_query = array(
            'message' => $message,
            'subject' => 'Bedankt voor uw bezoek!',
            'name_from' => $query_info->name,
            'email_from' => $query_info->email,
            'send' => 0,
            'email_to' => $customer_info->email,
            'name_to' => $customer_info->name,
            'email_bcc' => 'review@gsmreparatiecentrum.com'

        );

        $query_run = $this->db->insert_string('core_emails', $set_query);
        $this->db->query($query_run);

        endif;

    }



}