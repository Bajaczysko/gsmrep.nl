<?php
class Customer_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

   public function get_customers()
   {

       $this->data['search_page'] = false;
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
           // set search is yes
           $this->data['search_page'] = true;

           // Set pagination url to include search query.
           $pagination_url = 'customers/customeroverview/search/'.$uri['search'].'/';
           $config['uri_segment'] = 6; // Changing to 6 will select the 6th segment, example 'controller/function/search/query/page/10'.

           // Convert uri '-' back to ' ' spacing.
           $search_query = str_replace('-',' ',$uri['search']);

           // Get users and total row count for pagination.
           // Custom SQL SELECT, WHERE and LIMIT statements have been set above using the sql_select(), sql_where(), sql_limit() functions.
           // Using these functions means we only have to set them once for them to be used in future function calls.
           $total_customers = $this->db->query("select * from `customers` where `name` LIKE '%$search_query%'")->num_rows();
           $this->data['customers'] = $this->db->query("select * from `customers` where `name` LIKE '%$search_query%' limit $till,$limit")->result();
       }
       else
       {
           // Set some defaults.
           $pagination_url = 'customers/customeroverview/';
           $search_query = FALSE;
           $config['uri_segment'] = 4; // Changing to 4 will select the 4th segment, example 'controller/function/page/10'.

           // Get users and total row count for pagination.
           // Custom SQL SELECT and WHERE statements have been set above using the sql_select() and sql_where() functions.
           // Using these functions means we only have to set them once for them to be used in future function calls.
           $total_customers = $this->db->query("select id from `customers`")->num_rows();
           $this->data['customers'] = $this->db->query("select * from `customers` limit $till,$limit")->result();
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