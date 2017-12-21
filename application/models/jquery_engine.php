<?php
class Jquery_engine extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function list_merken()
    {
        $this->data['merken'] = $this->db->query("select * from `catalog_categories` where `parent` = '145'  order by `name`");
    }

    public function get_toestel()
    {
        $merk_id = $this->input->post('merk_id');
        $query = $this->db->query("select * from `catalog_categories` where `parent` = '$merk_id'");

        if($query->num_rows() > 0)
        {
            $html = '<div class="row"><div class="col-xs-12"><div class="form-group"><select class="selectpicker form-control" name="state" onchange="showPart(this);"><option value="">Please Select</option>';
            foreach($query->result() as $toestel)
            {

                $toestel_id = $toestel->category_id;
                $toestel_name = $toestel->name;
                $html .= "<option value='$toestel_id'>$toestel_name</option>";

            }
            $html .= "</select></label>";
            echo $html;
        }

        else
        {
            $html = '<label>Toestel:<select class="selectpicker1" name="state" onchange="showPart(this);"><option value="">Please Select</option>';
            $html .= "<option value='0'>Geen resultaten</option>";
            $html .= "</select></label>";
            echo $html;
        }

    }

    public function get_reparatie()
    {
        $merk_id = $this->input->post('reparatie_id');
        $query = $this->db->query("select name from `catalog_categories` where `category_id` = '$merk_id' ");

        if($query->num_rows() > 0) {

            foreach($query->result() as $result)
            {
                $cat_name = $result->name;
            }

            $namequery = $this->db->query("SELECT product_name.`value`,
	catalog_product_entity.entity_id
FROM catalog_product_attribute product_name INNER JOIN catalog_product_entity ON product_name.entity_id = catalog_product_entity.entity_id
	 INNER JOIN catalog_product_attribute category ON category.entity_id = catalog_product_entity.entity_id
	 INNER JOIN eav_attribute ON product_name.attribute_id = eav_attribute.attribute_id
WHERE eav_attribute.attribute_code = 'title' AND category.`value` LIKE '%\"$cat_name\"%'");

            if ($namequery->num_rows() > 0) {
                $html = '<div class="row"><div class="col-xs-12"><div class="form-group"><select class="selectpicker1 form-control" onchange="this.form.submit()" name="reparatie"><option value="">Please Select</option>';
                foreach ($namequery->result() as $toestel) {

                    $toestel_id = $toestel->entity_id;
                    $toestel_name = $toestel->value;
                    $html .= "<option value='$toestel_id'>$toestel_name</option>";

                }
                $html .= "</select></label>";
                echo $html;
            }
        }
    }

    public function search_product()
    {

        $this->load->model('woocommerce_model');
        $search = $this->uri->segment(3, 0);


    }

}