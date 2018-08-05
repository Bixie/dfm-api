<?php


namespace Bixie\DfmApi;


use Lime\Helper;

class ApiKeyHelper extends Helper {

    const HEADER_KEY_APITOKEN = 'HTTP_X_DFM_APITOKEN';

    public function test(){
        $api_key = isset($_SERVER[self::HEADER_KEY_APITOKEN]) ? $_SERVER[self::HEADER_KEY_APITOKEN] : '';
        return $api_key === $this->app['dfm.api_key'];
    }
}