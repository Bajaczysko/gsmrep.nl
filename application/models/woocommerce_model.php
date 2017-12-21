<?php
/**
 * Created by PhpStorm.
 * User: Sebastiaan
 * Date: 09-01-16
 * Time: 21:52
 */

$dir = dirname( __FILE__ ) . '/woocommerce_api/';

// base class
require_once( $dir . 'class-wc-api-client.php' );

// plumbing
require_once( $dir . 'class-wc-api-client-authentication.php' );
require_once( $dir . 'class-wc-api-client-http-request.php' );

// exceptions
require_once( $dir . '/exceptions/class-wc-api-client-exception.php' );
require_once( $dir . '/exceptions/class-wc-api-client-http-exception.php' );

// resources
require_once( $dir . '/resources/abstract-wc-api-client-resource.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-coupons.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-custom.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-customers.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-index.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-orders.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-order-notes.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-order-refunds.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-products.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-reports.php' );
require_once( $dir . '/resources/class-wc-api-client-resource-webhooks.php' );

class Woocommerce_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function get_woocommerce_stores($storeid = null)
    {

        $storearray = array();
        if($storeid != null)
        {
            $query = $this->db->query("select * from `core_stores` where `type` = 'woocommerce' and `id` = '$storeid'");
        }

        else
        {
            $query = $this->db->query("select * from `core_stores` where `type` = 'woocommerce'");
        }

        if($query->num_rows() > 0)
        {

            $x=0;
            foreach($query->result() as $storedata)
            {
                $storedataarray = array();
                $x++;
                // set data
                $storedataarray['id'] = $storedata->id;
                $storedataarray['url'] = $storedata->url;
                $storedataarray['api_key'] = $storedata->api_key;
                $storedataarray['api_secret'] = $storedata->api_secret;
                $storedataarray['last_order_sync'] = $storedata->last_order_sync;

                $storearray[$x] = $storedataarray;

            }

        }
        return $storearray;
    }

    public function get_import_site()
    {

    }

    public function one_product()
    {
        // get all woocommcerce stores that are permit to import

        // get store information check if info not is empty
        if($stores = $this->get_woocommerce_stores($storeid = 2))
        {
            foreach($stores as $store)
            {
                // connect to API of website

                $storeurl = $store['url'];
                $storapikey = $store['api_key'];
                $storeapisecret = $store['api_secret'];

                $options = array(
                    'debug'           => false,
                    'return_as_array' => true,
                    'validate_url'    => true,
                    'timeout'         => 30,
                    'ssl_verify'      => false,
                );

                try {

                    $client = new WC_API_Client( $storeurl, $storapikey, $storeapisecret, $options );
                    foreach($client->orders->get('', $args = array(
                        'filter' => array(
                            'date_min' => '2016-05-01 10:09:03',
                            'date_max' => '2016-05-10',
                            'limit' => '4'
                        )
                    )) as $products) {

                        echo "<pre>";
                        print_r($products);
                        echo "</pre>";
                    }
                    }

                catch ( WC_API_Client_Exception $e ) {

                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getCode() . PHP_EOL;

                    if ( $e instanceof WC_API_Client_HTTP_Exception ) {

                        print_r( $e->get_request() );
                        print_r( $e->get_response() );
                    }
                }

            }
        }

    }

    public function import_products()
    {

        // get all woocommcerce stores that are permit to import

        // get store information check if info not is empty
        if($stores = $this->get_woocommerce_stores($storeid = 2))
        {
           foreach($stores as $store)
           {
               // connect to API of website

               $storeurl = $store['url'];
               $storapikey = $store['api_key'];
               $storeapisecret = $store['api_secret'];

               $options = array(
                   'debug'           => false,
                   'return_as_array' => true,
                   'validate_url'    => true,
                   'timeout'         => 80000000000,
                   'ssl_verify'      => false,
               );

               try {

                   $client = new WC_API_Client( $storeurl, $storapikey, $storeapisecret, $options );



                   $allowed_sync = array('title', 'id', 'status', 'downloadable', 'virtual', 'permalink', 'price', 'regular_price', 'sale_price', 'price_html', 'taxable', 'tax_status', 'tax_class', 'managing_stock', 'stock_quantity', 'in_stock', 'backorders_allowed', 'backordered', 'sold_individually', 'purchaseable', 'featured', 'visible', 'catalog_visibility', 'on_sale', 'weight', 'dimensions', 'shipping_required', 'shipping_taxable', 'shipping_class', 'shipping_class_id', 'description', 'short_description', 'reviews_allowed', 'average_rating', 'rating_count', 'related_ids', 'upsell_ids', 'cross_sell_ids', 'parent_id', 'categories', 'tags', 'attributes', 'downloads', 'download_limit', 'download_expiry', 'download_type', 'purchase_note', 'total_sales', 'variations', 'parent', 'images');
                   $store = '0';


                   foreach($client->products->get('', $args = array('filter[limit]' => '2000')) as $products)
                   {

                       print_r(count($products));

                       foreach($products as $product)
                       {

                           // define product options
                           $created_at = $product['created_at'];
                           $updated_at = $product['updated_at'];
                           $type = $product['type'];
                           $sku = $product['sku'];

                           // insert product into database if not exist

                           $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
                           if($query->num_rows() < 1)
                           {
                               $this->db->query("insert into `catalog_product_entity` ( `created_at`, `updated_at`, `type`, `sku`) values ( '$created_at', '$updated_at', '$type', '$sku')");
                               $product_id = $this->db->insert_id();
                           }

                           else
                           {
                              foreach($query->result() as $product_result)
                              {
                                  $product_id = $product_result->entity_id;
                              }

                           }

                            foreach($product as $value => $data)
                            {

                                // insert attributes for product
                                if (in_array($value, $allowed_sync)) {

                                    $attribute_id = $this->get_attribute_id($value);

                                    if(!empty($data)) {

                                        if(is_array($data))
                                        {
                                            // escape data from HTML
                                            $html_escaped_data = serialize($data);
                                        }

                                        else
                                        {
                                            // escape data from HTML
                                            $html_escaped_data = htmlentities($data);
                                        }

                                        //insert attribute on product with store id
                                        $this->insert_attribute_product($attribute_id, $product_id, $store, $html_escaped_data);
                                    }
                                }

                            }

                       }

                   }



               }

               catch ( WC_API_Client_Exception $e ) {

                   echo $e->getMessage() . PHP_EOL;
                   echo $e->getCode() . PHP_EOL;

                   if ( $e instanceof WC_API_Client_HTTP_Exception ) {

                       print_r( $e->get_request() );
                       print_r( $e->get_response() );
                   }
               }

               }
        }

    }

    public function sync_price()
    {

        // get all woocommcerce stores that are permit to import

        // get store information check if info not is empty
        if($stores = $this->get_woocommerce_stores($storeid = 2))
        {
            foreach($stores as $store)
            {
                // connect to API of website

                $storeurl = $store['url'];
                $storapikey = $store['api_key'];
                $storeapisecret = $store['api_secret'];

                $options = array(
                    'debug'           => true,
                    'return_as_array' => true,
                    'validate_url'    => false,
                    'timeout'         => 3000,
                    'ssl_verify'      => false,
                );

                try {

                    $client = new WC_API_Client( $storeurl, $storapikey, $storeapisecret, $options );



                    $allowed_sync = array('title', 'id', 'status', 'downloadable', 'virtual', 'permalink', 'price', 'regular_price', 'sale_price', 'price_html', 'taxable', 'tax_status', 'tax_class', 'managing_stock', 'stock_quantity', 'in_stock', 'backorders_allowed', 'backordered', 'sold_individually', 'purchaseable', 'featured', 'visible', 'catalog_visibility', 'on_sale', 'weight', 'dimensions', 'shipping_required', 'shipping_taxable', 'shipping_class', 'shipping_class_id', 'description', 'short_description', 'reviews_allowed', 'average_rating', 'rating_count', 'related_ids', 'upsell_ids', 'cross_sell_ids', 'parent_id', 'categories', 'tags', 'attributes', 'downloads', 'download_limit', 'download_expiry', 'download_type', 'purchase_note', 'total_sales', 'variations', 'parent', 'images');
                    $store = '0';


                    foreach($client->products->get('', $args = array('filter[limit]' => '750')) as $products)
                    {

                        foreach($products as $product)
                        {

                            // define product options
                            $created_at = $product['created_at'];
                            $updated_at = $product['updated_at'];
                            $type = $product['type'];
                            $sku = $product['sku'];

                            // insert product into database if not exist

                            $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
                            if($query->num_rows() < 1)
                            {
                                $this->db->query("insert into `catalog_product_entity` ( `created_at`, `updated_at`, `type`, `sku`) values ( '$created_at', '$updated_at', '$type', '$sku')");
                                $product_id = $this->db->insert_id();
                            }

                            else
                            {
                                foreach($query->result() as $product_result)
                                {
                                    $product_id = $product_result->entity_id;
                                }

                            }

                            foreach($product as $value => $data)
                            {

                                // insert attributes for product
                                if (in_array($value, $allowed_sync)) {

                                    $attribute_id = $this->get_attribute_id($value);

                                    if(!empty($data)) {

                                        if(is_array($data))
                                        {
                                            // escape data from HTML
                                            $html_escaped_data = serialize($data);
                                        }

                                        else
                                        {
                                            // escape data from HTML
                                            $html_escaped_data = htmlentities($data);
                                        }

                                        //insert attribute on product with store id
                                        $this->insert_attribute_product($attribute_id, $product_id, $store, $html_escaped_data);
                                    }
                                }

                            }

                        }

                    }



                }

                catch ( WC_API_Client_Exception $e ) {

                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getCode() . PHP_EOL;

                    if ( $e instanceof WC_API_Client_HTTP_Exception ) {

                        print_r( $e->get_request() );
                        print_r( $e->get_response() );
                    }
                }

            }
        }

    }

    public function insert_attribute_product($attribute_id, $product_id, $store, $value)
    {
        $query = $this->db->query("select * from `catalog_product_attribute` where `store_id` = '$store' AND `attribute_id` = '$attribute_id' AND `entity_id` = '$product_id'");
        if($query->num_rows() > 0)
        {
            // update the value
            // get the id of this record

            $id = $query->row()->id;

            // update customer data
            $data =
                array
                (
                    'value' => $value
                );

            $where = "id = '$id'";

            // generate the query
            $update_product = $this->db->update_string('catalog_product_attribute', $data, $where);

            // run query to insert above data
            $this->db->query("$update_product");

        }
        else
        {
            $escaped_query = $this->db->escape($value);
            // no value exist so insert the value as a new entry
            $this->db->query("insert into `catalog_product_attribute` ( `value`, `attribute_id`, `store_id`, `entity_id`) values ( $escaped_query, '$attribute_id', '$store', '$product_id')");
        }
    }

    public function insert_new_product_id($product_id, $store, $value)
    {
        $this->db->query("insert into `catalog_product_attribute` ( `value`, `attribute_id`, `store_id`, `entity_id`) values ( '$value', '2', '$store', '$product_id')");
    }

    public function get_attribute_id($value)
    {

        // query to get attribute id from attribute value
        $query = $this->db->query("select attribute_id from `eav_attribute` where `attribute_code` = '$value'");
        if($query->num_rows() == 1)
        {
            // process and return id
            foreach($query->result() as $id)
            {
                return $id->attribute_id;
            }
        }
        else
        {
            // if no exist or multiple results delete everything and create a new one
            $this->db->query("insert into `eav_attribute` (`attribute_code`) values ('$value')");
            return $this->db->insert_id();
        }

    }

    public function import_categories()
    {

        // empty the categorie table
        $this->db->query("truncate table `catalog_categories`");

        // get all woocommcerce stores that are permit to import

        // get store information check if info not is empty
        if($stores = $this->get_woocommerce_stores($storeid = 2))
        {
            foreach($stores as $store)
            {
                // connect to API of website

                $storeurl = $store['url'];
                $storapikey = $store['api_key'];
                $storeapisecret = $store['api_secret'];

                $options = array(
                    'debug'           => false,
                    'return_as_array' => false,
                    'validate_url'    => true,
                    'timeout'         => 30,
                    'ssl_verify'      => false,
                );

                try {

                    $client = new WC_API_Client( $storeurl, $storapikey, $storeapisecret, $options );
                    $x=0;
                    foreach($client->products->get_categories() as $categories)
                    {

                        foreach($categories as $category)
                        {
                            $catid = $category->id;
                            $catname = $category->name;
                            $catslug = $category->slug;
                            $catparent = $category->parent;
                            $catdescription = $category->description;
                            $catcount = $category->count;

                            $this->db->query("insert into `catalog_categories` ( `storeid`, `slug`, `parent`, `category_id`, `description`, `product_count`, `name`) values ( '0', '$catslug', '$catparent', '$catid', '$catdescription', '$catcount', '$catname')");
                        }

                        $x++;
                    }



                }

                catch ( WC_API_Client_Exception $e ) {

                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getCode() . PHP_EOL;

                }

            }
        }

    }

    public function sync_reporting()
    {

        // get all woocommcerce stores that are permit to import

        // get store information check if info not is empty
        if ($stores = $this->get_woocommerce_stores($storeid = 2)) {
            foreach ($stores as $store) {
                // connect to API of website

                $storeurl = $store['url'];
                $storapikey = $store['api_key'];
                $storeapisecret = $store['api_secret'];

                $options = array(
                    'debug' => false,
                    'return_as_array' => false,
                    'validate_url' => true,
                    'timeout' => 30,
                    'ssl_verify' => false,
                );

                try {

                    $client = new WC_API_Client($storeurl, $storapikey, $storeapisecret, $options);
                    foreach ( $client->orders->get() as $reporting) {

                        print_r($reporting);

                    }

                } catch (WC_API_Client_Exception $e) {

                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getCode() . PHP_EOL;

                }

            }

        }
    }

    public function sync_woocommerce_stores()
    {

        // get all stores to sync

        // get store information check if info not is empty
        if($stores = $this->get_woocommerce_stores($storeid = 4))
        {
            foreach($stores as $store)
            {

                // connect to API of website
                $storeid = $store['id'];
                $storeurl = $store['url'];
                $storapikey = $store['api_key'];
                $storeapisecret = $store['api_secret'];

                $options = array(
                    'debug'           => false,
                    'return_as_array' => true,
                    'validate_url'    => true,
                    'timeout'         => 30,
                    'ssl_verify'      => false,
                );

                try {

                    $client = new WC_API_Client($storeurl, $storapikey, $storeapisecret, $options);

                    // loop trough products and check if exist
                    $query = $this->db->query("select entity_id, sku from `catalog_product_entity`");
                    foreach($query->result() as $product)
                    {

                        $sku = $product->sku;
                        $product_id = $product->entity_id;

                        $data =
                            Array
                            (
            'title' => $this->get_attribute_by_sku('title', $storeid, $sku),
            'id' => $this->get_attribute_by_sku('id', $storeid, $sku),
            'created_at' => $this->get_attribute_by_sku('created_at', $storeid, $sku),
            'updated_at' => $this->get_attribute_by_sku('updated_at', $storeid, $sku),
            'type' => $this->get_attribute_by_sku('type', $storeid, $sku),
            'status' => $this->get_attribute_by_sku('status', $storeid, $sku),
            'downloadable' => $this->get_attribute_by_sku('downloadable', $storeid, $sku),
            'virtual' => $this->get_attribute_by_sku('virtual', $storeid, $sku),
            'permalink' => $this->get_attribute_by_sku('permalink', $storeid, $sku),
            'sku' => $sku,
            'price' => $this->get_attribute_by_sku('price', $storeid, $sku),
            'regular_price' => $this->get_attribute_by_sku('regular_price', $storeid, $sku),
            'sale_price' => $this->get_attribute_by_sku('sale_price', $storeid, $sku),
            'price_html' => $this->get_attribute_by_sku('price_html', $storeid, $sku),
            'taxable' => $this->get_attribute_by_sku('taxable', $storeid, $sku),
            'tax_status' => $this->get_attribute_by_sku('tax_status', $storeid, $sku),
            'tax_class' => $this->get_attribute_by_sku('tax_class', $storeid, $sku),
            'managing_stock' => $this->get_attribute_by_sku('managing_stock', $storeid, $sku),
            'stock_quantity' => $this->get_attribute_by_sku('stock_quantity', $storeid, $sku),
            'in_stock' => $this->get_attribute_by_sku('in_stock', $storeid, $sku),
            'backorders_allowed' => $this->get_attribute_by_sku('backorders_allowed', $storeid, $sku),
            'backordered' => $this->get_attribute_by_sku('backordered', $storeid, $sku),
            'sold_individually' => $this->get_attribute_by_sku('sold_individually', $storeid, $sku),
            'purchaseable' => $this->get_attribute_by_sku('purchaseable', $storeid, $sku),
            'featured' => $this->get_attribute_by_sku('featured', $storeid, $sku),
            'visible' => $this->get_attribute_by_sku('visible', $storeid, $sku),
            'catalog_visibility' => $this->get_attribute_by_sku('catalog_visibility', $storeid, $sku),
            'on_sale' => $this->get_attribute_by_sku('on_sale', $storeid, $sku),
            'weight' => $this->get_attribute_by_sku('weight', $storeid, $sku),
            'dimensions' => unserialize($this->get_attribute_by_sku('dimensions', $storeid, $sku)),
            'shipping_required' => $this->get_attribute_by_sku('shipping_required', $storeid, $sku),
            'shipping_taxable' => $this->get_attribute_by_sku('shipping_taxable', $storeid, $sku),
            'shipping_class' => $this->get_attribute_by_sku('shipping_class', $storeid, $sku),
            'shipping_class_id' => $this->get_attribute_by_sku('shipping_class_id', $storeid, $sku),
            'description' => $this->get_attribute_by_sku('description', $storeid, $sku),
            'short_description' => $this->get_attribute_by_sku('short_description', $storeid, $sku),
            'reviews_allowed' => $this->get_attribute_by_sku('reviews_allowed', $storeid, $sku),
            'average_rating' => $this->get_attribute_by_sku('average_rating', $storeid, $sku),
            'rating_count' => $this->get_attribute_by_sku('rating_count', $storeid, $sku),
            'related_ids' => unserialize($this->get_attribute_by_sku('related_ids', $storeid, $sku)),
            'upsell_ids' => unserialize($this->get_attribute_by_sku('upsell_ids', $storeid, $sku)),
            'cross_sell_ids' => unserialize($this->get_attribute_by_sku('cross_sell_ids', $storeid, $sku)),
            'parent_id' => $this->get_attribute_by_sku('parent_id', $storeid, $sku),
            'categories' => unserialize($this->get_attribute_by_sku('categories', $storeid, $sku)),
            'tags' => unserialize($this->get_attribute_by_sku('tags', $storeid, $sku)),
            'attributes' => unserialize($this->get_attribute_by_sku('attributes', $storeid, $sku)),
            'downloads' => unserialize($this->get_attribute_by_sku('downloads', $storeid, $sku)),
            'download_limit' => $this->get_attribute_by_sku('download_limit', $storeid, $sku),
            'download_expiry' => $this->get_attribute_by_sku('download_expiry', $storeid, $sku),
            'download_type' => $this->get_attribute_by_sku('download_type', $storeid, $sku),
            'purchase_note' => $this->get_attribute_by_sku('purchase_note', $storeid, $sku),
            'total_sales' => $this->get_attribute_by_sku('total_sales', $storeid, $sku),
            'variations' => unserialize($this->get_attribute_by_sku('variations', $storeid, $sku)),
            'parent' => unserialize($this->get_attribute_by_sku('parent', $storeid, $sku)),
            'images' => unserialize($this->get_attribute_by_sku('images', $storeid, $sku))

        );


                        if($data['id'] == 'no_exist')
                        {
                            // create product
                            echo "geen sku dus maak product aan";
                            $returned_data = $client->products->create($data);

                            $id = $returned_data['product']['id'];
                            $this->insert_new_product_id($product_id, $storeid, $id);

                            echo "created id under storeview $storeid = $id";

                        }

                        else
                        {
                            $client = new WC_API_Client($storeurl, $storapikey, $storeapisecret, $options);

                            // update product
                            $client->products->update($data['id'], $data);
                        }


                    }

                }

                catch ( WC_API_Client_Exception $e ) {

                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getCode() . PHP_EOL;

                    if ( $e instanceof WC_API_Client_HTTP_Exception ) {

                        print_r( $e->get_request() );
                        print_r( $e->get_response() );
                    }
                }

            }
        }


    }

    public function sync_woocommerce_orders()
    {
	$insert_data = array();

        // get store information check if info not is empty
        if($stores = $this->get_woocommerce_stores())
        {
            foreach($stores as $store)
            {
		$insert_data[$store['id']] = 0;
                // connect to API of website

                $storeid = $store['id'];
                $storeurl = $store['url'];
                $storapikey = $store['api_key'];
                $storeapisecret = $store['api_secret'];
                $storelastsync = date('Y-m-dTH:i:sZ', strtotime($store['last_order_sync']));

                $options = array(
                    'debug'           => false,
                    'return_as_array' => true,
                    'validate_url'    => true,
                    'timeout'         => 30,
                    'ssl_verify'      => false,
                );

                //try {

                    $client = new WC_API_Client( $storeurl, $storapikey, $storeapisecret, $options );
                    foreach($client->orders->get('', $args = array(
                        'filter' => array(
                            'date_min' => '2015-06-01',
                            'date_max' => '2015-07-01',
                            'limit' => '500'
                        )
                    )) as $orders) {
                        foreach($orders as $order) {


                            $orderid_check = $order['order_number'];
                            $store_id_check = $store['id'];
                            // check if order already imported
                            $query_check = $this->db->query("select * from `orders` where `order_id` = '$orderid_check' AND `store_id` = '$store_id_check'");
			    $insert = FALSE;
			    if($query_check->num_rows() == 0) {
				$insert = TRUE;
			    }
			    else {
				$result = $query_check->result(); 
				$create = str_replace(' ', 'T', $result[0]->created_at) . 'Z';
				if($order['created_at'] != $create) {
					$orderid_check = intval(67 . $orderid_check);
					$query_check = $this->db->query("select * from `orders` where `order_id` = '$orderid_check' AND `store_id` = '$store_id_check'");
					if($query_check->num_rows() == 0) {
						$insert = TRUE;
						$order['order_number'] = intval( 67 . $order['order_number']);
					}
				}
			    }

                            // check if order exist if not then create otherwise skip
                            if ($insert) {

                                $data =
                                    array
                                    (
                                        'store_id' => $store['id'],
                                        'order_id' => $order['order_number'],
                                        'created_at' => $order['created_at'],
                                        'updated_at' => $order['updated_at'],
                                        'completed_at' => '0000-00-00 00:00:00',
                                        'status' => 'draft',
                                        'currency' => $order['currency'],
                                        'total' => $order['subtotal'],
                                        'subtotal' => $order['subtotal'],
                                        'total_line_items_quantity' => $order['total_line_items_quantity'],
                                        'total_tax' => $order['total_tax'],
                                        'total_shipping' => $order['total_shipping'],
                                        'cart_tax' => $order['cart_tax'],
                                        'shipping_tax' => $order['shipping_tax'],
                                        'total_discount' => $order['total_discount'],
                                        'shipping_methods' => $order['shipping_methods'],
                                        'note' => $order['note'],
                                        'customer_ip' => $order['customer_ip'],
                                        'customer_user_agent' => $order['customer_user_agent'],
                                        'expected_time' => $order['meta']['Tijd'],
                                        'expected_date' => date('Y-m-d', strtotime($order['meta']['Datum'])),
                                        'physical_store_id' => $this->convert_email_to_physical_store($order['meta']['Vestiging_mail'])
                                    );

                                // generate the query
                                $query_order = $this->db->insert_string('orders', $data);

                                // insert order and return ID

                                $this->db->query("$query_order");
                                $order_id = $this->db->insert_id();

                                // Insert order lines

                                foreach ($order['line_items'] as $order_item) {
                                    $data =
                                        array
                                        (
                                            'subtotal' => $order_item['subtotal'],
                                            'subtotal_tax' => $order_item['subtotal_tax'],
                                            'total' => $order_item['subtotal'],
                                            'total_tax' => $order_item['total_tax'],
                                            'price' => $order_item['subtotal'],
                                            'quantity' => $order_item['quantity'],
                                            'tax_class' => $order_item['tax_class'],
                                            'name' => $order_item['name'],
                                            'sku' => $order_item['sku'],
                                            'order_id' => $order_id
                                        );

                                    // generate the query
                                    $query_line = $this->db->insert_string('order_line', $data);


                                    // run query to insert above data
                                    $this->db->query("$query_line");

                                }

                                // if coupon then process coupons
                                if (!empty($order['coupon_lines'])) {
                                    foreach ($order['coupon_lines'] as $coupon) {
                                        $coupon_code = $coupon['code'];
                                        $query = $this->db->query("select * from `coupons` where `coupon_code` = '$coupon_code'");
                                        if ($query->num_rows > 0) {
                                            foreach ($query->result() as $coupon_result) {
                                                $data =
                                                    array
                                                    (
                                                        'price' => '-' . $coupon['amount'],
                                                        'quantity' => '1',
                                                        'name' => $coupon_result->name,
                                                        'order_id' => $order_id
                                                    );

                                                // generate the query
                                                $query_line = $this->db->insert_string('order_line', $data);


                                                // run query to insert above data
                                                $this->db->query("$query_line");

                                            }
                                        }
                                    }
                                }


                                // search if customer exist
                                $query = $this->db->query("select * from `customers` where `email` = " . $this->db->escape($order['customer']['email']) . " limit 1");

                                if ($query->num_rows() > 0) {
                                    // client exist so get ID
                                    foreach ($query->result() as $customerreturn) {
                                        $client_id = $customerreturn->id;
                                    }
                                } else {
                                    // create new customer

                                    $data =
                                        array(
                                            'email' => $order['customer']['email'],
                                            'name' => $order['customer']['first_name'] . " " . $order['customer']['last_name'],
                                            'phone' => $order['customer']['billing_address']['phone'],
                                            'company' => $order['customer']['billing_address']['company']
                                        );

                                    // generate the query
                                    $query_line = $this->db->insert_string('customers', $data);

                                    // run query to insert above data
                                    $this->db->query("$query_line");
                                    $client_id = $this->db->insert_id();
                                }


                                // build data for billing and shipping address


                                $billing_address_data =
                                    array(
                                        'first_name' => $order['customer']['billing_address']['first_name'],
                                        'last_name' => $order['customer']['billing_address']['last_name]'],
                                        'company' => $order['customer']['billing_address']['company'],
                                        'address_1' => $order['customer']['billing_address']['address_1'],
                                        'address_2' => $order['customer']['billing_address']['address_2'],
                                        'city' => $order['customer']['billing_address']['city'],
                                        'state' => $order['customer']['billing_address']['state'],
                                        'postcode' => $order['customer']['billing_address']['postcode'],
                                        'country' => $order['customer']['billing_address']['country'],
                                        'email' => $order['customer']['billing_address']['email'],
                                        'phone' => $order['customer']['billing_address']['phone']
                                    );

                                $phone = $order['customer']['billing_address']['phone'];
                                $email = $order['customer']['billing_address']['email'];

                                $query = $this->db->query("select * from `customers_billing_address` where `email` = ".$this->db->escape($email)." AND `phone` = ".$this->db->escape($phone)." AND `customer_id` = ".$this->db->escape($client_id)."");

                                if ($query->num_rows() < 1) {
                                    // create new customer
                                    // generate the query
                                    $query_line_billing = $this->db->insert_string('customers_billing_address', $billing_address_data);

                                    // run query to insert above data
                                    $this->db->query($query_line_billing);
                                }


                                $shipping_address_data =
                                    array(
                                        'first_name' => $order['customer']['shipping_address']['first_name'],
                                        'last_name' => $order['customer']['shipping_address']['last_name]'],
                                        'company' => $order['customer']['shipping_address']['company'],
                                        'address_1' => $order['customer']['shipping_address']['address_1'],
                                        'address_2' => $order['customer']['shipping_address']['address_2'],
                                        'city' => $order['customer']['shipping_address']['city'],
                                        'state' => $order['customer']['shipping_address']['state'],
                                        'postcode' => $order['customer']['shipping_address']['postcode'],
                                        'country' => $order['customer']['shipping_address']['country']
                                    );

                                $firstname = $order['customer']['shipping_address']['first_name'];
                                $lastname = $order['customer']['shipping_address']['last_name'];
                                $company = $order['customer']['shipping_address']['company'];

                                $query = $this->db->query("select * from `customers_shipping_address` where `first_name` = ".$this->db->escape($firstname)." AND `last_name` = ".$this->db->escape($lastname)." AND `company` = ".$this->db->escape($company)." AND `customer_id` = ".$this->db->escape($client_id)."");

                                if ($query->num_rows() < 1) {
                                    // create new customer
                                    // generate the query
                                    $query_line_shipping = $this->db->insert_string('customers_shipping_address', $shipping_address_data);

                                    // run query to insert above data
                                    $this->db->query($query_line_shipping);
                                }


                                // update order with customer id
                                $this->db->query("update `orders` set `customer_id`='$client_id' where `id`='$order_id' ");

                                $this->load->model('billing_model');
                                $array_update_order = $this->billing_model->billing_update_price($order_id);

                                $data = array(
                                    'total' => $array_update_order['total'],
                                    'subtotal' => $array_update_order['subtotal'],
                                    'total_line_items_quantity' => $array_update_order['total_line_items_quantity'],
                                    'total_tax' => $array_update_order['total_tax'],
                                );

                                $where = "id = '$order_id'";
                                $update_order_query = $this->db->update_string('orders', $data, $where);
                                $this->db->query($update_order_query);
				$insert_data[$store['id']]++;
                            }
                        }
                    }
		try {
                }

                catch ( WC_API_Client_Exception $e ) {

                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getCode() . PHP_EOL;

                    if ( $e instanceof WC_API_Client_HTTP_Exception ) {

                        print_r( $e->get_request() );
                        print_r( $e->get_response() );
                    }
                }

                // update the last store update

                $current_sql_date = date("Y-m-d H:i:s");
                $this->db->query("update `core_stores` set `last_import_date`='$current_sql_date' where `id`='$storeid'");

            }
        }

    }

    public function convert_email_to_physical_store($email)
    {
        $query = $this->db->query("select id from `stores_physical` where `email` = '$email'");
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $storeid)
            {
                return $storeid->id;
            }
        }
        else
        {
            return '0';
        }
    }

    public function get_storename_by_id($storeid)
    {
        $query = $this->db->query("select name from `core_stores` where `id` = '$storeid'");
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $name)
            {
                return $name->name;
            }
        }
        else
        {
            return 'not found';
        }
    }

    public function get_attribute_by_sku($attribute, $storeid, $sku)
    {

        if($attribute == 'created_at')
        {

            $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->created_at;
                }
            }

        }

        elseif($attribute == 'updated_at')
        {
            $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->updated_at;
                }
            }
        }

        elseif($attribute == 'type')
        {
            $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->type;
                }
            }
        }

        elseif($attribute == 'sku')
        {
            $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->sku;
                }
            }
        }

        elseif($attribute == 'status')
        {
            // try to get status on current store view
            $query = $this->db->query("SELECT catalog_product_attribute.`value`
FROM eav_attribute INNER JOIN catalog_product_attribute ON eav_attribute.attribute_id = catalog_product_attribute.attribute_id
	 INNER JOIN catalog_product_entity ON catalog_product_attribute.entity_id = catalog_product_entity.entity_id
WHERE catalog_product_entity.sku = '$sku' and catalog_product_attribute.store_id = '$storeid' and eav_attribute.attribute_code = 'status'");

            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->value;
                }
            }

            else
            {
                return 'future';
            }

        }

        elseif($attribute == 'id')
        {
            // try to get status on current store view
            $query = $this->db->query("SELECT catalog_product_attribute.`value`
FROM eav_attribute INNER JOIN catalog_product_attribute ON eav_attribute.attribute_id = catalog_product_attribute.attribute_id
	 INNER JOIN catalog_product_entity ON catalog_product_attribute.entity_id = catalog_product_entity.entity_id
WHERE catalog_product_entity.sku = '$sku' and catalog_product_attribute.store_id = '$storeid' and eav_attribute.attribute_code = 'id'");

            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->value;
                }
            }

            else
            {
                return 'no_exist';
            }

        }

        elseif($attribute == 'visible')
        {
            // try to get status on current store view
            $query = $this->db->query("SELECT catalog_product_attribute.`value`
FROM eav_attribute INNER JOIN catalog_product_attribute ON eav_attribute.attribute_id = catalog_product_attribute.attribute_id
	 INNER JOIN catalog_product_entity ON catalog_product_attribute.entity_id = catalog_product_entity.entity_id
WHERE catalog_product_entity.sku = '$sku' and catalog_product_attribute.store_id = '$storeid' and eav_attribute.attribute_code = 'visible'");

            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return $result->value;
                }
            }

            else
            {
                return '0';
            }
        }

        else
        {

            // get attribute for this store view
            $query = $this->db->query("SELECT catalog_product_attribute.`value`
FROM eav_attribute INNER JOIN catalog_product_attribute ON eav_attribute.attribute_id = catalog_product_attribute.attribute_id
	 INNER JOIN catalog_product_entity ON catalog_product_attribute.entity_id = catalog_product_entity.entity_id
WHERE catalog_product_entity.sku = '$sku' and catalog_product_attribute.store_id = '$storeid' and eav_attribute.attribute_code = '$attribute'");

            if($query->num_rows() > 0)
            {
                foreach($query->result() as $result)
                {
                    return html_entity_decode($result->value);
                }
            }

            else
            {

                $query = $this->db->query("SELECT catalog_product_attribute.`value`
FROM eav_attribute INNER JOIN catalog_product_attribute ON eav_attribute.attribute_id = catalog_product_attribute.attribute_id
	 INNER JOIN catalog_product_entity ON catalog_product_attribute.entity_id = catalog_product_entity.entity_id
WHERE catalog_product_entity.sku = '$sku' and catalog_product_attribute.store_id = '0' and eav_attribute.attribute_code = '$attribute'");

                if($query->num_rows() > 0)
                {
                    foreach($query->result() as $result)
                    {
                        return html_entity_decode($result->value);
                    }
                }

                else
                {
                    return 0;
                }


            }


        }


    }

}
