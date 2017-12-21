<?php
class Language_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function translate($translation)
    {

        if($result = $this->lang->line($translation, false))
        {
            return $result;
        }

        else
        {
            return $translation;
        }

    }

}
