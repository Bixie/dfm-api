<?php


namespace Bixie\DfmApi\Helpers;


use Lime\Helper;

class DRInputHelper extends Helper {

    /**
     * @var array
     */
    protected $requestfilter = [
        'PURCHASE_ID' => FILTER_VALIDATE_INT,
        'RUNNING_NO' => FILTER_VALIDATE_INT,
        'PURCHASE_DATE' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '#\d{2}/\d{2}/\d{4}#',]],
        'PRODUCT_ID' => FILTER_VALIDATE_INT,
        'QUANTITY' => FILTER_VALIDATE_INT,
        'LASTNAME' => FILTER_SANITIZE_STRING,
        'EMAIL' => FILTER_VALIDATE_EMAIL,
        'COUNTRY' => FILTER_SANITIZE_STRING,
        'ENCODING' => FILTER_SANITIZE_STRING,
        'LANGUAGE_ID' => FILTER_VALIDATE_INT,
        'ISO_CODE' => FILTER_SANITIZE_STRING,
        'NLALLOW' => FILTER_SANITIZE_STRING,
    ];

    /**
     * @var array Sanitized input
     */
    protected $input = [];

    public function initialize()
    {
        //only process complete requests
        if (!isset($_REQUEST['PURCHASE_ID'])) {
            return;
        }
        //sanatize input via filters
        $this->input = filter_var_array($_REQUEST, $this->requestfilter);
    }

    public function getData($key = null, $default = null)
    {
        return $key ? $this->input[$key] ?? $default : $this->input;
    }
}
