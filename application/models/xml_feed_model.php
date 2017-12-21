<?php
class Xml_feed_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function get_xmlfeed_stores($storeid = null)
    {

        $storearray = array();
        if($storeid != null)
        {
            $query = $this->db->query("select * from `core_stores` where `type` = 'xml_feed' and `id` = '$storeid'");
        }

        else
        {
            $query = $this->db->query("select * from `core_stores` where `type` = 'xml_feed'");
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

    function get_xml_from_url($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $xmlstr = curl_exec($ch);
        curl_close($ch);

        return $xmlstr;
    }

    public function import_xml_feed()
    {

        // load woocommerce model in order to read data
        $this->load->model('woocommerce_model');

        // get all woocommcerce stores that are permit to import

        // get store information check if info not is empty
        if ($stores = $this->get_xmlfeed_stores()) {

            foreach ($stores as $store) {
                $storeurl = $store['url'];
                $xml_data_from_url = $this->get_xml_from_url($storeurl);
                $products = simplexml_load_file($storeurl);

                echo "<pre>";
                //print_r($products);
                echo "</pre>";

                foreach ($products as $product) {


                    // define product options
                    $created_at = "0000-00-00 00:00:00";
                    $updated_at = "0000-00-00 00:00:00";
                    $type = $product->type;
                    $sku = $product->sku;

                    // insert product into database if not exist

                    $query = $this->db->query("select * from `catalog_product_entity` where `sku` = '$sku'");
                    if ($query->num_rows() < 1) {
                           $this->db->query("insert into `catalog_product_entity` ( `created_at`, `updated_at`, `type`, `sku`) values ( '$created_at', '$updated_at', '$type', '$sku')");
                           $product_id = $this->db->insert_id();
                    } else {
                        foreach ($query->result() as $product_result) {
                            $product_id = $product_result->entity_id;
                        }

                    }

                    // generate product array
                    $store = '0';

                    $raw_categories = explode(">", $product->categories);
                    $process_categories_1= array_map("ltrim", $raw_categories);
                    $categories_clear = array_map("rtrim", $process_categories_1);


                    $data_product =
                        Array
                        (
                            'title' => $product->title,
                            'id' => $product->id,
                            'created_at' => "0000-00-00 00:00:00",
                            'updated_at' => "0000-00-00 00:00:00",
                            'type' => $product->product_type,
                            'status' => "publish",
                            'permalink' => $product->permalink,
                            'sku' => $product->sku,
                            'price' => $product->price,
                            'regular_price' => $product->regular_price,
                            'sale_price' => $product->sale_price,
                            'visible' => $product->visible,
                            'description' => $product->description,
                            'short_description' => $product->short_description,
                            'average_rating' => $product->average_rating,
                            'parent_id' => $product->parent_id,
                            'categories' => $categories_clear,
                            'images' => explode(">", $product->images)

                        );


                    // process array
                    foreach ($data_product as $value => $data) {
                        $attribute_id = $this->woocommerce_model->get_attribute_id($value);

                        if (!empty($data)) {

                            if (is_array($data)) {
                                // escape data from HTML
                                $html_escaped_data = serialize($data);
                            } else {
                                // escape data from HTML
                                $html_escaped_data = htmlentities($data);
                            }

                            //insert attribute on product with store id
                            $this->woocommerce_model->insert_attribute_product($attribute_id, $product_id, $store, $html_escaped_data);

                        }


                    }


                }

            }


        }
    }

}