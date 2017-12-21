<?php

class Billing_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function create_new_billing()
    {

        $data =
            array
            (
                'store_id' => '1',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'completed_at' => '0000-00-00 00:00:00',
                'status' => 'customer-selection',
                'currency' => 'EUR',
                'total_discount' => '0',
                'shipping_methods' => 'in shop',
                'physical_store_id' => $this->userid_to_store(),
                'barcode_id' => time()

            );

        // generate the query
        $query_order = $this->db->insert_string('orders', $data);

        // insert order AND get ID

        $this->db->query("$query_order");
        $billing_id = $this->db->insert_id();

        // insert order lines

        // get posted product

        $this->load->model('woocommerce_model');

        if($this->input->post('reparatie'))
        {
            $product_id = $this->input->post('reparatie');

            $query = $this->db->query("select sku from `catalog_product_entity` where `entity_id` = '$product_id'");
            foreach($query->result() as $sku)
            {
                $product_sku = $sku->sku;
            }

            $price = $this->woocommerce_model->get_attribute_by_sku('price', '2', $product_sku);
            $tax = ($price / 121) * 21;

            $data_line =
                array
                (
                    'total_tax' =>  $tax,
                    'subtotal' => $price - $tax,
                    'price' => $price,
                    'quantity' => '1',
                    'name' => $this->woocommerce_model->get_attribute_by_sku('title', '2', $product_sku),
                    'sku' => $product_sku,
                    'order_id' => $billing_id
                );
        }

        elseif($this->input->post('manual_create'))
        {

            $product_name = $this->input->post('manual_product');
            $product_qty = $this->input->post('manual_product_qty');
            $product_price = $this->input->post('manual_product_price');
            $product_tax = ($product_price / 121) * 21;

            $data_line =
                array
                (
                    'total_tax' => $product_tax,
                    'subtotal' => $product_price - $product_tax,
                    'price' => $product_price,
                    'quantity' => $product_qty,
                    'name' => $product_name,
                    'order_id' => $billing_id
                );
        }



        // generate the query
        $query_line = $this->db->insert_string('order_line', $data_line);

        // run query to insert above data
        $this->db->query("$query_line");

        // update order information

        $data = array(
            'total' => $data_line['price'],
            'subtotal' => $data_line['subtotal'],
            'total_line_items_quantity' => '1',
            'total_tax' => $data_line['total_tax'],
        );

        $where = "id = '$billing_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/select_customer/$billing_id");

    }

    public function add_item_to_billing()
    {
        // get posted product
        $billing_id = $this->uri->segment(3, 0);

        $this->load->model('woocommerce_model');

        if($this->input->post('reparatie'))
        {
            $product_id = $this->input->post('reparatie');

            $query = $this->db->query("select sku from `catalog_product_entity` where `entity_id` = '$product_id'");
            foreach($query->result() as $sku)
            {
                $product_sku = $sku->sku;
            }

            $price = $this->woocommerce_model->get_attribute_by_sku('price', '2', $product_sku);
            $tax = ($price / 121) * 21;

            $data_line =
                array
                (
                    'total_tax' => $tax,
                    'subtotal' => $price - $tax,
                    'price' => $price,
                    'quantity' => '1',
                    'name' => $this->woocommerce_model->get_attribute_by_sku('title', '2', $product_sku),
                    'sku' => $product_sku,
                    'order_id' => $billing_id
                );
        }

        elseif($this->input->post('manual_create'))
        {

            $product_name = $this->input->post('manual_product');
            $product_qty = $this->input->post('manual_product_qty');
            $product_price = $this->input->post('manual_product_price');
            $product_tax = ($product_price / 121) * 21;

            $data_line =
                array
                (
                    'total_tax' => $product_tax,
                    'subtotal' => $product_price - $product_tax,
                    'price' => $product_price,
                    'quantity' => $product_qty,
                    'name' => $product_name,
                    'order_id' => $billing_id

                );

        }

        // generate the query
        $query_line = $this->db->insert_string('order_line', $data_line);

        // run query to insert above data
        $this->db->query("$query_line");

        // update order information

        $array_update_order = $this->billing_update_price($billing_id);

        $data = array(
            'total' => $array_update_order['total'],
            'subtotal' => $array_update_order['subtotal'],
            'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
            'total_tax' => $array_update_order['total_tax'],
        );

        $where = "id = '$billing_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/edit/$billing_id");
    }

    public function billing_update_price($billing_id)
    {
        // get all data from bill

        $query = $this->db->query("SELECT order_line.total,
	order_line.price,
	order_line.`name`,
	order_line.id,
	order_line.total_tax,
	order_line.subtotal_tax,
	order_line.subtotal,
	order_line.quantity
FROM order_line
WHERE order_line.order_id = '$billing_id'");

        $price = '';
        $quantity = '';

        foreach($query->result() as $item)
        {
            $price += $item->price * $item->quantity;
            $quantity += $item->quantity;
        }

        $tax = ($price / 121) * 21;

        $data = array(
            'total' => $price,
            'subtotal' => $price - $tax,
            'total_line_items_quantity' => $quantity,
            'total_tax' => $tax
        );

        return $data;

    }

    public function delete_item_from_billing()
    {

        $itemid = $this->uri->segment(3, 0);

        // get orderid from item
        $query = $this->db->query("select * from `order_line` where `id` = '$itemid'");
        if($query->num_rows() > 0)
        {
            $result = $query->row();
            $orderid = $result->order_id;

            // delete the entry
            $this->db->query("delete from `order_line` where `id`='$itemid'");

            // update order information
            $array_update_order = $this->billing_update_price($orderid);

            $data = array(
                'total' => $array_update_order['total'],
                'subtotal' => $array_update_order['subtotal'],
                'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
                'total_tax' => $array_update_order['total_tax'],
            );

            $where = "id = '$orderid'";
            $update_order_query = $this->db->update_string('orders', $data, $where);
            $this->db->query($update_order_query);

            redirect("/billing/edit/$orderid");

        }

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
	stores_physical.`name` as website,
	core_stores.`name` as store,
	orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' AND orders.`hide` = '0' AND (orders.`status` = 'on-hold' OR orders.`status` = 'done') AND (customers.`name` LIKE '%$search_query%' OR orders.`barcode_id` LIKE '%$search_query%' OR orders.`status` LIKE '%$search_query%' OR orders.`id` LIKE '%$search_query%')
ORDER BY orders.created_at DESC")->num_rows();
            $this->data['customers'] = $this->db->query("SELECT orders.`status`,
	orders.created_at,
	orders.total,
	customers.`name` as customer_name,
	stores_physical.`name` as website,
	core_stores.`name` as store,
		orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' AND orders.`hide` = '0' AND (orders.`status` = 'on-hold' OR orders.`status` = 'done') AND (customers.`name` LIKE '%$search_query%' OR orders.`barcode_id` LIKE '%$search_query%' OR orders.`status` LIKE '%$search_query%' OR orders.`id` LIKE '%$search_query%')
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
	stores_physical.`name` as website,
	core_stores.`name` as store,
		orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' AND orders.`hide` = '0' AND (orders.`status` = 'on-hold' OR orders.`status` = 'done') ORDER BY created_at DESC")->num_rows();
            $this->data['customers'] = $this->db->query("SELECT orders.`status`,
	orders.created_at,
	orders.total,
	customers.`name` as customer_name,
	stores_physical.`name`,
	core_stores.`name`,
		orders.`id` as billingid
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
WHERE stores_physical_rights.userid = '$userid' AND orders.`hide` = '0' AND (orders.`status` = 'on-hold' OR orders.`status` = 'done') ORDER BY created_at DESC limit $till,$limit")->result();
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

    public function get_expected_billing_data()
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
            $pagination_url = 'billing/expected_customers/search/'.$uri['search'].'/';
            $config['uri_segment'] = 6; // Changing to 6 will select the 6th segment, example 'controller/function/search/query/page/10'.

            // Convert uri '-' back to ' ' spacing.
            $search_query = str_replace('-',' ',$uri['search']);

            // Get users and total row count for pagination.
            // Custom SQL SELECT, WHERE and LIMIT statements have been set above using the sql_select(), sql_where(), sql_limit() functions.
            // Using these functions means we only have to set them once for them to be used in future function calls.
            $total_customers = $this->db->query("SELECT orders.expected_time,
	orders.expected_date,
	stores_physical.`name` AS store,
	core_stores.`name` AS website,
	orders.created_at AS placed_at,
	orders.total AS total_price,
	customers.`name` AS customer_name,
	orders.total_line_items_quantity AS count_products,
	orders.id
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 INNER JOIN customers ON orders.customer_id = customers.id
	 INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id
WHERE orders.expected_date >= CURDATE() AND stores_physical_rights.userid = '$userid' AND orders.status = 'draft' AND customers.`name` LIKE '%$search_query%'
ORDER BY orders.expected_date, orders.expected_time  ASC")->num_rows();
            $this->data['customers'] = $this->db->query("SELECT orders.expected_time,
	orders.expected_date,
	stores_physical.`name` AS store,
	core_stores.`name` AS website,
	orders.created_at AS placed_at,
	orders.total AS total_price,
	customers.`name` AS customer_name,
	orders.total_line_items_quantity AS count_products,
	orders.id
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 INNER JOIN customers ON orders.customer_id = customers.id
	 INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id
WHERE orders.expected_date >= CURDATE() AND stores_physical_rights.userid = '$userid' AND orders.status = 'draft' AND customers.`name` LIKE '%$search_query%'
ORDER BY orders.expected_date, orders.expected_time  ASC limit $till,$limit")->result();
        }
        else
        {
            // Set some defaults.
            $pagination_url = 'billing/expected_customers/';
            $search_query = FALSE;
            $config['uri_segment'] = 4; // Changing to 4 will select the 4th segment, example 'controller/function/page/10'.

            // Get users and total row count for pagination.
            // Custom SQL SELECT and WHERE statements have been set above using the sql_select() and sql_where() functions.
            // Using these functions means we only have to set them once for them to be used in future function calls.
            $total_customers = $this->db->query("SELECT orders.expected_time,
	orders.expected_date,
	stores_physical.`name` AS store,
	core_stores.`name` AS website,
	orders.created_at AS placed_at,
	orders.total AS total_price,
	customers.`name` AS customer_name,
	orders.total_line_items_quantity AS count_products,
	orders.id
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 INNER JOIN customers ON orders.customer_id = customers.id
	 INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id
WHERE orders.expected_date >= CURDATE() AND stores_physical_rights.userid = '$userid' AND orders.status = 'draft'
ORDER BY orders.expected_date, orders.expected_time  ASC")->num_rows();
            $this->data['customers'] = $this->db->query("SELECT orders.expected_time,
	orders.expected_date,
	stores_physical.`name` AS store,
	core_stores.`name` AS website,
	orders.created_at AS placed_at,
	orders.total AS total_price,
	customers.`name` AS customer_name,
	orders.total_line_items_quantity AS count_products,
	orders.id
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 INNER JOIN core_stores ON orders.store_id = core_stores.id
	 INNER JOIN customers ON orders.customer_id = customers.id
	 INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id
WHERE orders.expected_date >= CURDATE() AND stores_physical_rights.userid = '$userid'  AND orders.status = 'draft'
ORDER BY orders.expected_date, orders.expected_time  ASC limit $till,$limit")->result();
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

    public function confirm_billing()
    {
        $billing_id = $this->uri->segment(3, 0);
        $method = $this->uri->segment(4, 0);

        $data = array(
            'status' => 'done',
            'completed_at' => date("Y-m-d H:i:s"),
            'payment_method' => $method,
        );

        $where = "id = '$billing_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        // generate a mail for confirmation
        $this->load->model('email_model');
        $this->email_model->generate_billing_bill($billing_id);

        redirect("/billing/view_bill/$billing_id");

    }

    public function confirm_billing_change_payment()
    {
        $billing_id = $this->uri->segment(3, 0);
        $method = $this->uri->segment(4, 0);

        $data = array(
            'payment_method' => $method
        );

        $where = "id = '$billing_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/view_bill/$billing_id");

    }

    public function remove_billing()
    {
        $billing_id = $this->uri->segment(3, 0);

        $data = array(
            'status' => 'removed',
            'completed_at' => date("Y-m-d H:i:s"),
            'hide' => '1'
        );

        $where = "id = '$billing_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/manage");

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
	orders.total_tax,
	orders.payment_method
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
	 LEFT OUTER JOIN customers_shipping_address ON customers.id = customers_shipping_address.customer_id
	 LEFT OUTER JOIN customers_billing_address ON customers.id = customers_billing_address.customer_id
WHERE stores_physical_rights.userid = '$userid' AND orders.hide != '1' AND orders.`status` != 'removed' AND orders.id = '$billing_id'");

        if($query->num_rows() > 0)
        {
            $this->data['bill'] = $query->row();
        }

    }

    public function load_bill_items()
    {

        $billing_id = $this->uri->segment(3, 0);

        $this->data['bill_items'] = false;

        $query = $this->db->query("SELECT order_line.total,
	order_line.price,
	order_line.`name`,
	order_line.id,
	order_line.total_tax,
	order_line.subtotal_tax,
	order_line.subtotal,
	order_line.quantity
FROM order_line
WHERE order_line.order_id = '$billing_id'");

        if($query->num_rows() > 0)
        {
            $this->data['bill_items'] = $query->result();
        }


    }

    public function get_bill_information_by_id($billing_id)
    {

        $this->data['bill'] = false;
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
	orders.total_tax,
	orders.payment_method
FROM stores_physical_rights INNER JOIN orders ON stores_physical_rights.storeid = orders.physical_store_id
	 LEFT OUTER JOIN customers ON customers.id = orders.customer_id
	 LEFT OUTER JOIN customers_shipping_address ON customers.id = customers_shipping_address.customer_id
	 LEFT OUTER JOIN customers_billing_address ON customers.id = customers_billing_address.customer_id
WHERE stores_physical_rights.userid = '$userid' AND orders.hide != '1' AND orders.`status` != 'removed' AND orders.id = '$billing_id'");

        if($query->num_rows() > 0)
        {
            $this->data['bill'] = $query->row();
        }

    }

    public function load_bill_items_by_id($billing_id)
    {

        $this->data['bill_items'] = false;

        $query = $this->db->query("SELECT order_line.total,
	order_line.price,
	order_line.`name`,
	order_line.id,
	order_line.total_tax,
	order_line.subtotal_tax,
	order_line.subtotal,
	order_line.quantity
FROM order_line
WHERE order_line.order_id = '$billing_id'");

        if($query->num_rows() > 0)
        {
            $this->data['bill_items'] = $query->result();
        }


    }

    public function load_customer_from_billingid($billing_id)
    {
        return $this->db->query("SELECT customers.`name`, customers.email FROM orders INNER JOIN customers ON orders.customer_id = customers.id WHERE orders.id = '$billing_id'")->row();
    }

    public function expected_to_billing_converter()
    {
        $expected_id = $this->uri->segment(3, 0);

        $data = array(
            'status' => 'on-hold',
            'barcode_id' => time()
        );

        $where = "id = '$expected_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        $array_update_order = $this->billing_update_price($expected_id);

        $data = array(
            'total' => $array_update_order['total'],
            'subtotal' => $array_update_order['subtotal'],
            'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
            'total_tax' => $array_update_order['total_tax'],
        );

        $where = "id = '$expected_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/edit/$expected_id");
    }

    public function load_bill_company_information()
    {

        $billing_id = $this->uri->segment(3, 0);

        $this->data['company_information'] = $this->db->query("SELECT stores_physical.`name`,
	stores_physical.email,
	stores_physical.address,
	stores_physical.zip,
	stores_physical.town,
	stores_physical.phone,
	stores_physical.btw_nummer,
	stores_physical.kvk_nummer,
	orders.payment_method,
	orders.barcode_id,
	orders.completed_at,
	core_stores.url,
	core_stores.logo,
	orders.`status`,
	orders.total,
	orders.subtotal,
	orders.total_tax
FROM orders INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON core_stores.id = orders.store_id
WHERE orders.id = '$billing_id'")->row();

    }

    public function load_bill_company_information_by_id($billing_id)
    {

        return $this->db->query("SELECT stores_physical.`name`,
	stores_physical.email,
	stores_physical.address,
	stores_physical.zip,
	stores_physical.town,
	stores_physical.phone,
	stores_physical.btw_nummer,
	stores_physical.kvk_nummer,
	orders.payment_method,
	orders.barcode_id,
	orders.completed_at,
	core_stores.url,
	core_stores.logo,
	orders.`status`,
	orders.total,
	orders.subtotal,
	orders.total_tax
FROM orders INNER JOIN stores_physical ON orders.physical_store_id = stores_physical.id
	 INNER JOIN core_stores ON core_stores.id = orders.store_id
WHERE orders.id = '$billing_id'")->row();

    }

    public function load_bill_customer_information()
    {

        $billing_id = $this->uri->segment(3, 0);

        $this->data['customer_information'] = $this->db->query("SELECT customers.`name`,
	customers.phone,
	customers.email,
	customers.company,
	customers.imei,
	customers.newsletter
FROM orders INNER JOIN customers ON orders.customer_id = customers.id
WHERE orders.id = '$billing_id'")->row();

    }



    // more information

    public function link_customer($custom_customer = null)
    {

        if($custom_customer == null)
        {
            $customerid = $this->uri->segment(3, 0);
        }
        else
        {
            $customerid = $custom_customer;
        }


        $billingid = $this->session->userdata('billing_ID');

        $data = array(
            'customer_id' => $customerid,
            'status' => 'on-hold'
        );

        $where = "id = '$billingid'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/edit/$billingid");

    }

    public function new_customer()
    {
        $fullname = $this->input->post('full-name');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $company = $this->input->post('company');
        $newsletter = $this->input->post('newsletter');
        $imei = $this->input->post('imei');

        $data =
            array(
                'email' => $email,
                'name' => $fullname,
                'phone' => $phone,
                'company' => $company,
                'newsletter' => $newsletter,
                'imei' => $imei
            );

        // generate the query
        $query_line = $this->db->insert_string('customers', $data);

        // run query to insert above data
        $this->db->query($query_line);
        $client_id = $this->db->insert_id();

        // insert also a record in other customer tables


        $billingid = $this->session->userdata('billing_ID');

        $data = array(
            'customer_id' => $client_id,
            'status' => 'on-hold'
        );

        $where = "id = '$billingid'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/edit/$billingid");
    }


    public function get_coupons()
    {
        $query = $this->db->query("select * from `coupons`");
        $this->data['coupons'] = $query->result();
    }

    public function link_coupon()
    {


                $coupon_id = $this->input->post('coupon');
                $order_id = $this->uri->segment(3, 0);
                $query = $this->db->query("select * from `coupons` where `id` = '$coupon_id'");
                if($query->num_rows > 0)
                {
                    foreach($query->result() as $coupon_result)
                    {
                        $data =
                            array
                            (
                                'price' => $coupon_result->value,
                                'quantity' => '1',
                                'name' => $coupon_result->name,
                                'order_id' => $order_id
                            );

                        // generate the query
                        $query_line = $this->db->insert_string('order_line', $data);


                        // run query to insert above data
                        $this->db->query("$query_line");

                        // update order information
                        $array_update_order = $this->billing_update_price($order_id);

                        $data = array(
                            'total' => $array_update_order['total'],
                            'subtotal' => $array_update_order['subtotal'],
                            'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
                            'total_tax' => $array_update_order['total_tax'],
                        );

                        $where = "id = '$order_id'";
                        $update_order_query = $this->db->update_string('orders', $data, $where);
                        $this->db->query($update_order_query);



                    }
                }

        redirect("/billing/edit/$order_id");

    }

    public function add_quick_product()
    {
        $order_id = $this->uri->segment(3, 0);
        $quick_product_id = $this->uri->segment(4, 0);

        $query = $this->db->query("select * from `products_quick_bar` where `id` = '$quick_product_id'");
        if($query->num_rows > 0)
        {
            foreach($query->result() as $product_result)
            {
                $data =
                    array
                    (
                        'price' => $product_result->product_price,
                        'quantity' => $product_result->product_qty,
                        'name' => $product_result->product_name,
                        'order_id' => $order_id
                    );

                // generate the query
                $query_line = $this->db->insert_string('order_line', $data);


                // run query to insert above data
                $this->db->query("$query_line");

                // update order information
                $array_update_order = $this->billing_update_price($order_id);

                $data = array(
                    'total' => $array_update_order['total'],
                    'subtotal' => $array_update_order['subtotal'],
                    'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
                    'total_tax' => $array_update_order['total_tax'],
                );

                $where = "id = '$order_id'";
                $update_order_query = $this->db->update_string('orders', $data, $where);
                $this->db->query($update_order_query);

            }
        }

        redirect("/billing/edit/$order_id");

    }

    public function get_customer_id($order_id)
    {
        return $this->db->query("SELECT customer_id from `orders` where `id` = '$order_id'")->row()->customer_id;
    }

    public function update_bill_item_qty()
    {

        $order_id = $this->uri->segment(3, 0);

        $name = $this->input->post('full-name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $company = $this->input->post('company');
        $imei = $this->input->post('imei');
        $newsletter_choose = $this->input->post('newsletter');

        if($newsletter_choose == 1)
        {
            $newsletter = 1;
        }
        else
        {
            $newsletter = 0;
        }

        // get customer id for this order
        $customer_id = $this->get_customer_id($order_id);

        // update customer data
        $data =
            array
            (
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'company' => $company,
                'imei' => $imei,
                'newsletter' => $newsletter
            );

        $where = "id = '$customer_id'";

        // generate the query
        $update_customerdata_query = $this->db->update_string('customers', $data, $where);

        // run query to insert above data
        $this->db->query("$update_customerdata_query");

        // set input to other array
        $qty_array = $this->input->post();
        // strip values to define the qty changes
        unset($qty_array['name']);
        unset($qty_array['phone']);
        unset($qty_array['email']);
        unset($qty_array['company']);
        unset($qty_array['imei']);
        unset($qty_array['newsletter']);
        unset($qty_array['newsletter-no']);

        foreach($qty_array as $number => $qty)
        {
            $data =
                array
                (
                    'quantity' => $qty,
                );

            $where = "id = '$number'";

            // generate the query
            $update_order_query = $this->db->update_string('order_line', $data, $where);


            // run query to insert above data
            $this->db->query("$update_order_query");

        }

        // update order information
        $array_update_order = $this->billing_update_price($order_id);

        $data = array(
            'total' => $array_update_order['total'],
            'subtotal' => $array_update_order['subtotal'],
            'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
            'total_tax' => $array_update_order['total_tax'],
        );

        $where = "id = '$order_id'";
        $update_order_query = $this->db->update_string('orders', $data, $where);
        $this->db->query($update_order_query);

        redirect("/billing/edit/$order_id");


    }

    public function userid_to_store()
    {
        $userid = $this->flexi_auth->get_user_id();

        $query = $this->db->query("SELECT stores_physical.id
FROM stores_physical_rights INNER JOIN stores_physical ON stores_physical_rights.storeid = stores_physical.id
WHERE stores_physical_rights.userid = '$userid'")->result();

        foreach($query as $user)
        {
            return $user->id;
        }


    }

    public function get_billing_lines_without_discount($billingid)
    {
        return $this->db->query("select * from `order_line` where `order_id` = '$billingid' AND `sku` IS NOT NULL")->result();
    }

    public function get_customer_phone($billing_id)
    {

        return $this->db->query("SELECT customers_billing_address.phone
FROM orders INNER JOIN customers ON orders.customer_id = customers.id
	 INNER JOIN customers_billing_address ON customers.email = customers_billing_address.email
WHERE orders.id = '$billing_id'")->row()->phone;

    }

    public function load_quick_products()
    {
        $this->data['quick_products'] = $this->db->query("select * from `products_quick_bar`")->result();
    }
}