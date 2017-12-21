<?php

class Report_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function generate_reports()
    {

        // first remove all current reports

        // generate reports
        $this->generate_productcount_reports();


    }

    public function generate_productcount_reports()
    {

        // select all dates to create reports
        $query = $this->db->query("SELECT DISTINCT DATE( gsmrep_prod.orders.completed_at ) AS dates FROM gsmrep_prod.orders WHERE `status` = 'done' ORDER BY dates");

        foreach($query->result() as $dates)
        {
            $date = $dates->dates;

            // select all fysical stores to process
            $stores = $this->db->query("select * from `gsmrep_prod`.`stores_physical`")->result();

            foreach($stores as $store)
            {
                $store_id = $store->id;

                $product_lines = $this->db->query("SELECT count(*) as total_sales,
	order_line.`name`
FROM orders INNER JOIN order_line ON orders.id = order_line.order_id
WHERE orders.`status` = 'done' AND orders.completed_at LIKE '$date%' AND orders.physical_store_id = '$store_id'
GROUP BY order_line.`name`")->result();

                foreach($product_lines as $product_counter)
                {

                    $data = array(
                      'product' => $product_counter->name,
                      'count' => $product_counter->total_sales,
                      'store' => $store_id,
                      'date' => $date
                    );

                    $query_line = $this->db->insert_string('report_product_counts', $data);
                    $this->db->query($query_line);

                }


            }


        }

    }

    public function load_shops()
    {
        return $this->db->query("select id, short_name as name from `stores_physical`")->result();
    }

    public function id_to_shop_name($shop_id)
    {
        return $this->db->query("select name from `stores_physical` where `id` = '$shop_id'")->row()->name;
    }

    public function date_to_human($date)
    {
        return date('d-m-Y H:m:i', strtotime($date));
    }

    public function get_right_date_range()
    {
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $array = $this->db->query("select DISTINCT DATE(created_at) from `orders` where `completed_at` BETWEEN '$date_from' AND '$date_to' order by `id`")->result_array();

        $this->data['range_first_date'] = reset($array);
        $this->data['range_last_date'] = end($array);

    }

    public function base64url_encode($str) {
        return strtr(base64_encode($str), '+/', '-_');

    }

    public function base64url_decode($base64url) {
        return base64_decode(strtr($base64url, '-_', '+/'));
    }

    public function set_data_buttons()
    {
        $shop = $this->uri->segment(3, 0);
        $type = $this->uri->segment(4, 0);
        $range = $this->uri->segment(5, 0);

        if($range == 'today'):

            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d');

        elseif($range == 'this-week'):

            $dto = new DateTime();
            $date_from = $dto->setISODate(date('Y'), date('W'))->format('Y-m-d');
            $date_to = $dto->modify('+6 days')->format('Y-m-d');

        elseif($range == 'this-month'):

            $date_from = date('Y-m-01'); // hard-coded '01' for first day
            $date_to  = date('Y-m-t');

        endif;

        // data colected now redirect with right date spectrum
        redirect("/reporting/$type/$shop/$date_from/$date_to");

    }

    public function set_data_posts()
    {
        $date_from = $this->input->post('date-from');
        $date_to = $this->input->post('date-to');
        $shop = $this->input->post('shop');
        $type = $this->input->post('type');

        // data colected now redirect with right date spectrum
        redirect("/reporting/$type/$shop/$date_from/$date_to");
    }

    public function load_products_from_bill($billingid)
    {

        return $this->db->query("SELECT order_line.total,
	order_line.price,
	order_line.`name`,
	order_line.id,
	order_line.total_tax,
	order_line.subtotal_tax,
	order_line.subtotal,
	order_line.quantity
FROM order_line
WHERE order_line.order_id = '$billingid'")->result();

    }

    public function accepted_bills()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['accepted_bills'] = $this->db->query("SELECT *,orders.id as orderID, customers.`name` as customer_name FROM orders LEFT JOIN customers ON orders.customer_id = customers.id WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->result();
    }

    public function removed_bills()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['removed_bills'] = $this->db->query("SELECT *,orders.id as orderID, customers.`name` AS customer_name FROM orders LEFT JOIN customers ON orders.customer_id = customers.id WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'removed' AND `physical_store_id` = '$shop'")->result();
    }

    public function draft_bills()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['draft_bills'] = $this->db->query("SELECT customers.`name`, orders.created_at, orders.id, orders.total FROM orders INNER JOIN customers ON orders.customer_id = customers.id WHERE orders.created_at BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'draft' AND `physical_store_id` = '$shop'")->result();
    }

    public function customers_no_shows()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['customers_no_shows'] = $this->db->query("SELECT customers.`name`, orders.id, orders.total, orders.expected_date, orders.expected_time, customers.email, customers.phone FROM orders INNER JOIN customers ON orders.customer_id = customers.id WHERE orders.`status` = 'draft' AND orders.expected_date BETWEEN '$date_from' AND NOW()-INTERVAL 1 DAY AND orders.physical_store_id = '$shop'")->result();
    }

    public function turnover_product()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['turnover_products'] = $this->db->query("SELECT order_line.`name`, sum(order_line.subtotal) AS subtotal, sum(order_line.total_tax) AS tax, sum(order_line.price) AS price, sum(order_line.quantity) AS counter FROM order_line INNER JOIN orders ON order_line.order_id = orders.id WHERE orders.physical_store_id = '$shop' AND orders.`completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND orders.`status` = 'done' GROUP BY order_line.`name`")->result();
    }

    public function period_turnover_total()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['period_turnover_total'] = $this->db->query("SELECT sum(orders.total) AS period_turnover_total FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->row()->period_turnover_total;
    }

    public function period_turnover_total_tax()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['period_turnover_total_tax'] = round($this->db->query("SELECT sum(orders.total_tax) AS period_turnover_total_tax FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->row()->period_turnover_total_tax, 2);

    }

    public function period_turnover_subtotal()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['period_turnover_subtotal'] = round($this->db->query("SELECT sum(orders.subtotal) AS period_turnover_subtotal FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->row()->period_turnover_subtotal, 2);

    }

    public function period_total_products()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['total_products'] = $this->db->query("SELECT COUNT(order_line.id) AS total_products FROM order_line INNER JOIN orders ON order_line.order_id = orders.id WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->row()->total_products;
    }

    public function period_total_bills()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $this->data['period_total_bills'] = $this->db->query("SELECT count(orders.id) AS period_total_bills FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->row()->period_total_bills;
    }

    public function period_total_pin()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $result = $this->db->query("SELECT sum(orders.total) AS period_total_pin FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `payment_method` = 'pin' AND `physical_store_id` = '$shop'")->row()->period_total_pin;

        if($result == ''):
        $this->data['period_total_pin'] = 0;
        else:
        $this->data['period_total_pin'] = $result;
        endif;

    }

    public function period_total_contant()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $result = $this->db->query("SELECT sum(orders.total) AS period_total_contant FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `payment_method` = 'contant' AND `physical_store_id` = '$shop'")->row()->period_total_contant;

        if($result == ''):
            $this->data['period_total_contant'] = 0;
        else:
            $this->data['period_total_contant'] = $result;
        endif;

    }

    public function period_total_factuur()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);

        $result = $this->db->query("SELECT sum(orders.total) AS period_total_factuur FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `payment_method` = 'factuur' AND `physical_store_id` = '$shop'")->row()->period_total_factuur;

        if($result == ''):
            $this->data['period_total_factuur'] = 0;
        else:
            $this->data['period_total_factuur'] = $result;
        endif;


    }

    public function get_bill_information()
    {

        $this->data['bill'] = false;
        $billing_id = $this->uri->segment(3, 0);
        $userid = $this->flexi_auth->get_user_id();

        $query = $this->db->query("SELECT customers_billing_address.first_name AS customers_billing_address_first_name,
	customers_billing_address.last_name AS customers_billing_address_last_name,
	customers_billing_address.company AS customers_billing_address_company,
	customers_billing_address.address_1 AS customers_billing_address_address_1,
	customers_billing_address.address_2 AS customers_billing_addres_address_2,
	customers_billing_address.city AS customers_billing_address_city,
	customers_billing_address.state AS customers_billing_address_state,
	customers_billing_address.postcode AS customers_billing_address_zip,
	customers_billing_address.country AS customers_billing_address_country,
	customers_billing_address.email AS customers_billing_address_email,
	customers_billing_address.phone AS customers_billing_address_phone,
	customers_shipping_address.first_name AS customers_shipping_address_first_name,
	customers_shipping_address.last_name AS customers_shipping_address_last_name,
	customers_shipping_address.company AS customers_shipping_address_company,
	customers_shipping_address.address_1 AS customers_shipping_address_address_1,
	customers_shipping_address.address_2 AS customers_shipping_address_address_2,
	customers_shipping_address.city AS customers_shipping_address_city,
	customers_shipping_address.state AS customers_shipping_address_state,
	customers_shipping_address.postcode AS customers_shipping_address_zip,
	customers_shipping_address.country AS customers_shipping_address_country,
	orders.created_at,
	orders.status,
	orders.id,
	orders.total,
	orders.subtotal,
	orders.total_tax
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
	 LEFT OUTER JOIN customers_shipping_address ON customers.id = customers_shipping_address.customer_id
	 LEFT OUTER JOIN customers_billing_address ON customers.id = customers_billing_address.customer_id
WHERE orders.id = '$billing_id'");

        if($query->num_rows() > 0)
        {
            $this->data['bill'] = $query->row();
        }

    }

    public function reverse_bill_search()
    {
        $shop = $this->uri->segment(3, 0);
        $date_from = $this->uri->segment(4, 0);
        $date_to = $this->uri->segment(5, 0);
        $product = $this->base64url_decode($this->uri->segment(6, 0));

        $this->data['reverse_bill_search'] = $this->db->query("SELECT orders.id, customers.`name`, orders.created_at, orders.total FROM order_line INNER JOIN orders ON order_line.order_id = orders.id INNER JOIN customers ON orders.customer_id = customers.id WHERE order_line.`name` = '$product' AND orders.completed_at BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND orders.physical_store_id = '$shop'")->result();
    }

    public function user_daily_total_bills()
    {
        $userid = $this->flexi_auth->get_user_id();
        $shop = $this->db->query("SELECT stores_physical.id FROM stores_physical_rights INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id WHERE stores_physical_rights.userid = '$userid'")->row()->id;
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");

        $this->data['period_total_bills'] = $this->db->query("SELECT count(orders.id) AS period_total_bills FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `physical_store_id` = '$shop'")->row()->period_total_bills;
    }

    public function user_daily_total_pin()
    {
        $userid = $this->flexi_auth->get_user_id();
        $shop = $this->db->query("SELECT stores_physical.id FROM stores_physical_rights INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id WHERE stores_physical_rights.userid = '$userid'")->row()->id;
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");

        $result = $this->db->query("SELECT sum(orders.total) AS period_total_pin FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `payment_method` = 'pin' AND `physical_store_id` = '$shop'")->row()->period_total_pin;

        if($result == ''):
            $this->data['period_total_pin'] = 0;
        else:
            $this->data['period_total_pin'] = $result;
        endif;

    }

    public function user_daily_total_contant()
    {
        $userid = $this->flexi_auth->get_user_id();
        $shop = $this->db->query("SELECT stores_physical.id FROM stores_physical_rights INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id WHERE stores_physical_rights.userid = '$userid'")->row()->id;
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");

        $result = $this->db->query("SELECT sum(orders.total) AS period_total_contant FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `payment_method` = 'contant' AND `physical_store_id` = '$shop'")->row()->period_total_contant;

        if($result == ''):
            $this->data['period_total_contant'] = 0;
        else:
            $this->data['period_total_contant'] = $result;
        endif;

    }

    public function user_daily_total_factuur()
    {
        $userid = $this->flexi_auth->get_user_id();
        $shop = $this->db->query("SELECT stores_physical.id FROM stores_physical_rights INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id WHERE stores_physical_rights.userid = '$userid'")->row()->id;
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");

        $result = $this->db->query("SELECT sum(orders.total) AS period_total_factuur FROM orders WHERE `completed_at` BETWEEN '$date_from 00:00:00' AND '$date_to 23:59:59' AND `status` = 'done' AND `payment_method` = 'factuur' AND `physical_store_id` = '$shop'")->row()->period_total_factuur;

        if($result == ''):
            $this->data['period_total_factuur'] = 0;
        else:
            $this->data['period_total_factuur'] = $result;
        endif;


    }

    public function get_billing_data()
    {
        $userid = $this->flexi_auth->get_user_id();

        // Get url for any search query or pagination position.
        $uri = $this->uri->uri_to_assoc(3);

        // Set pagination limit, get current position and get total users.
        $offset = (isset($uri['page'])) ? $uri['page'] : FALSE;
        if($offset){
            $till = $offset;
        }
        else{
            $till = 0;
        }
        $limit = $till + 25;
        $max = 25;

        // Set SQL WHERE condition depending on whether a user search was submitted.
        if (array_key_exists('search', $uri))
        {
            // Set pagination url to include search query.
            $pagination_url = 'billing/manage/search/'.$uri['search'].'/';
            $config['uri_segment'] = 6; // Changing to 6 will select the 6th segment, example 'controller/function/search/query/page/10'.

            // Convert uri '-' back to ' ' spacing.
            $search_query = str_replace('-',' ',$uri['search']);

            // Get users and total row count for pagination.
            // Custom SQL SELECT, WHERE and LIMIT statements have been set above using the sql_select(), sql_where(), sql_limit() functions.
            // Using these functions means we only have to set them once for them to be used in future function calls.
            $total_customers = $this->db->query("SELECT orders.`status`,
	orders.created_at,
	orders.total,
	customers.`name` as customer_name,
	stores_physical.`name`  as storename,
	core_stores.`name` as store,
	orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' AND (customers.`name` LIKE '%$search_query%' OR orders.`barcode_id` LIKE '%$search_query%' OR orders.`status` LIKE '%$search_query%' OR orders.`id` LIKE '%$search_query%')
ORDER BY orders.created_at DESC")->num_rows();
            $this->data['customers'] = $this->db->query("SELECT orders.`status`,
	orders.created_at,
	orders.total,
	customers.`name` as customer_name,
	stores_physical.`name`  as storename,
	core_stores.`name` as store,
		orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' AND (customers.`name` LIKE '%$search_query%' OR orders.`barcode_id` LIKE '%$search_query%' OR orders.`status` LIKE '%$search_query%' OR orders.`id` LIKE '%$search_query%')
ORDER BY orders.created_at DESC limit $till,$limit")->result();
        }
        else
        {
            // Set some defaults.
            $pagination_url = 'billing/manage/';
            $search_query = FALSE;
            $config['uri_segment'] = 4; // Changing to 4 will select the 4th segment, example 'controller/function/page/10'.

            // Get users and total row count for pagination.
            // Custom SQL SELECT and WHERE statements have been set above using the sql_select() and sql_where() functions.
            // Using these functions means we only have to set them once for them to be used in future function calls.
            $total_customers = $this->db->query("SELECT orders.`status`,
	orders.created_at,
	orders.total,
	customers.`name` as customer_name,
	stores_physical.`name`  as storename,
	core_stores.`name` as store,
		orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' ORDER BY created_at DESC")->num_rows();
            $this->data['customers'] = $this->db->query("SELECT orders.`status`,
	orders.created_at,
	orders.total,
	customers.`name` as customer_name,
	stores_physical.`name` as storename,
	core_stores.`name`,
		orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' ORDER BY created_at DESC limit $till,$limit")->result();
        }

        // Create user record pagination.
        $this->load->library('pagination');
        $config['base_url'] = base_url().$pagination_url.'page/';
        $config['total_rows'] = $total_customers;
        $config['per_page'] = '25';
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] ="</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tagl_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        $this->pagination->initialize($config);

        // Make search query and pagination data available to view.
        $this->data['total_rows'] = $total_customers;
        $this->data['search_query'] = $search_query; // Populates search input field in view.
        $this->data['pagination']['links'] = $this->pagination->create_links();
        $this->data['pagination']['total_users'] = $total_customers;

    }

}