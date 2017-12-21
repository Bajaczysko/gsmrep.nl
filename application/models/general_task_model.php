<?php

class General_task_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function generate_search_bill_results()
    {
        $query = $this->db->query("SELECT entity_id,sku FROM catalog_product_entity");
        foreach($query->result() as $skus)
        {
            $user_arr[] = $skus->entity_id;
            $user_arr2[] = $this->woocommerce_model->get_attribute_by_sku('title', '1', $skus->sku);
        }

        $result = json_encode($user_arr2);

        // write result to config table
        $this->config_model->insert_config(0, 'billing_search_results', $result);

    }

    public function fix_customer_data()
    {

        $query = $this->db->query("SELECT * FROM `customers`");

        if($query->num_rows > 0)
        {

            foreach($query->result() as $customer_results)
            {

                $email = $customer_results->email;
                $customer_id = $customer_results->id;
                $query_billing = $this->db->query("select * from `gsmrep_prod`.`customers_billing_address` where `email` = '$email' limit 0,1");

                // check if results and if so then fill the missing customer data in customer table
                if($query_billing->num_rows() > 0)
                {
                    foreach($query_billing->result() as $customer_billing)
                    {
                        $company_billing = $customer_billing->company;
                        $phone_billing = $customer_billing->phone;
                        $email_billing = $customer_billing->email;

                        $data = array(
                            'phone' => $phone_billing,
                            'company' => $company_billing
                        );

                        $where = "id = '$customer_id'";
                        $update_order_query = $this->db->update_string('customers', $data, $where);
                        $this->db->query($update_order_query);
                    }
                }

            }

        }


    }

}