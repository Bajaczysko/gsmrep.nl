<?php

class Config_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function insert_config($storeid = 0, $key, $value)
    {

        // insert config if not exist

        if(is_array($value))
        {
            $value_insert = serialize($value);
        }
        else
        {
            $value_insert = $value;
        }

        $query = $this->db->query("select * from `core_config` where `key` = '$key' AND `storeid` = '$storeid'");
        if($query->num_rows() > 0)
        {
            // update because value exist
            $result = $query->row();
            $id = $result->id;


            $data = array('value' => $value_insert);
            $where = "id = $id";

            $run_query = $this->db->update_string('core_config', $data, $where);
        }
        else
        {
            // insert because value not exist
            $data = array('key' => $key, 'value' => $value_insert, 'storeid' => $storeid);

            $run_query = $this->db->insert_string('core_config', $data);
        }

        $this->db->query($run_query);
    }

    public function get_config($storeid = 0, $key)
    {

    }

}