<?php

$dir = dirname( __FILE__ ) . '/barcode/';


require_once($dir .'BarcodeGenerator.php');
require_once($dir .'BarcodeGeneratorPNG.php');
require_once($dir .'BarcodeGeneratorSVG.php');
require_once($dir .'BarcodeGeneratorHTML.php');

class Barcode_model extends CI_Model
{

    // The following method prevents an error occurring when $this->data is modified.
    // Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
    public function &__get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function get_barcode_code($barcode_data)
    {
        $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
        echo '<img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($barcode_data, $generatorPNG::TYPE_CODE_128)) . '">';
    }

}