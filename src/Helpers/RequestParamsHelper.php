<?php


namespace Bixie\DfmApi\Helpers;


use Lime\Helper;

class RequestParamsHelper extends Helper {

    const HEADER_KEY_APITOKEN = 'HTTP_X_DFM_APITOKEN';

    /**
     * @var array
     */
    protected $requestfilter = [
        'params' => [
            'Investment' => FILTER_VALIDATE_INT,
            'PortfolioSize' => FILTER_VALIDATE_INT,
            'HoldingPeriod' => FILTER_VALIDATE_INT,
            'ValidationPeriod' => FILTER_VALIDATE_INT,
            'PennyStocks' => FILTER_VALIDATE_INT,
            'GrowthPotential' => FILTER_VALIDATE_INT,
            'HedgePercentage' => FILTER_VALIDATE_INT,
            'BalanceRR' => FILTER_VALIDATE_INT,
            'Watchlists' => FILTER_VALIDATE_INT,
            'TransactionCosts' => FILTER_VALIDATE_INT,
            'LoanPercentage' => FILTER_VALIDATE_INT,
            'DividendTax' => FILTER_VALIDATE_INT,
            'DataProvider' => FILTER_VALIDATE_INT,
        ],
        'options' => [
            'timestamp' => FILTER_VALIDATE_INT,
            'width' => FILTER_VALIDATE_INT,
            'layout' => FILTER_SANITIZE_STRING,
            'locale' => FILTER_SANITIZE_STRING,
        ],
    ];

    /**
     * @var array Sanitized input
     */
    protected $input = [];

    public function initialize() {
        //only process complete requests
        if (!isset($_REQUEST['params']) || !isset($_REQUEST['options'])) {
            return;
        }
        // trim the $_REQUEST data before any spaces get encoded to "%20"
        array_filter($_REQUEST['params'], 'trim');
        array_filter($_REQUEST['options'], 'trim');
        //sanatize input via filters
        $this->input['params'] = filter_var_array($_REQUEST['params'], $this->requestfilter['params']);
        $this->input['options'] = filter_var_array($_REQUEST['options'], $this->requestfilter['options']);
    }


    /**
     * Get sanitazed data from request
     * @param $key
     * @return array
     */
    public function getData($key){
        return isset($this->input[$key]) ? $this->input[$key] : [];
    }
}