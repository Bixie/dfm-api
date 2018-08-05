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
            'Investment' => FILTER_SANITIZE_NUMBER_INT,
            'PortfolioSize' => FILTER_SANITIZE_NUMBER_INT,
            'HoldingPeriod' => FILTER_SANITIZE_NUMBER_INT,
            'ValidationPeriod' => FILTER_SANITIZE_NUMBER_INT,
            'PennyStocks' => FILTER_SANITIZE_STRING,
            'GrowthPotential' => FILTER_SANITIZE_STRING,
            'HedgePercentage' => FILTER_SANITIZE_NUMBER_INT,
            'BalanceRR' => FILTER_SANITIZE_STRING,
            'Watchlists' => FILTER_SANITIZE_STRING,
            'TransactionCosts' => FILTER_SANITIZE_NUMBER_INT,
            'LoanPercentage' => FILTER_SANITIZE_NUMBER_INT,
            'DividendTax' => FILTER_SANITIZE_NUMBER_INT,
            'DataProvider' => FILTER_SANITIZE_STRING,
        ],
        'options' => [
            'width' => FILTER_SANITIZE_NUMBER_INT,
            'layout' => FILTER_SANITIZE_STRING,
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